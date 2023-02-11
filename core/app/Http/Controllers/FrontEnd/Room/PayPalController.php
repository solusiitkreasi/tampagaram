<?php

namespace App\Http\Controllers\FrontEnd\Room;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\Room\RoomBookingController;
use App\Models\PaymentGateway\OnlineGateway;
use App\Models\RoomManagement\RoomBooking;
use App\Traits\MiscellaneousTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class PayPalController extends Controller
{
  use MiscellaneousTrait;

  private $api_context;

  public function __construct()
  {
    $data = OnlineGateway::whereKeyword('paypal')->first();
    $paypalData = json_decode($data->information, true);

    $paypal_conf = Config::get('paypal');
    $paypal_conf['client_id'] = $paypalData['client_id'];
    $paypal_conf['secret'] = $paypalData['client_secret'];
    $paypal_conf['settings']['mode'] = $paypalData['sandbox_status'] == 1 ? 'sandbox' : 'live';

    $this->api_context = new ApiContext(
      new OAuthTokenCredential(
        $paypal_conf['client_id'],
        $paypal_conf['secret']
      )
    );

    $this->api_context->setConfig($paypal_conf['settings']);
  }

  public function bookingProcess(Request $request)
  {
    $roomBooking = new RoomBookingController();

    // do calculation
    $calculatedData = $roomBooking->calculation($request);

    $title = 'Room Booking';

    $currencyInfo = MiscellaneousTrait::getCurrencyInfo();

    $information['subtotal'] = $calculatedData['subtotal'];
    $information['discount'] = $calculatedData['discount'];
    $information['total'] = $calculatedData['total'];
    $information['currency_symbol'] = $currencyInfo->base_currency_symbol;
    $information['currency_symbol_position'] = $currencyInfo->base_currency_symbol_position;
    $information['currency_text'] = $currencyInfo->base_currency_text;
    $information['currency_text_position'] = $currencyInfo->base_currency_text_position;
    $information['method'] = 'PayPal';
    $information['type'] = 'online';

    // store the room booking information in database
    $booking_details = $roomBooking->storeData($request, $information);

    // changing the currency before redirect to PayPal
    if ($currencyInfo->base_currency_text !== 'USD') {
      $rate = $currencyInfo->base_currency_rate;
      $convertedTotal = $calculatedData['total'] / $rate;
    }

    $paypalTotal = $currencyInfo->base_currency_text === 'USD' ? $calculatedData['total'] : $convertedTotal;

    $notify_url = route('room_booking.paypal.notify');
    $cancel_url = route('room_booking.cancel');

    $payer = new Payer();
    $payer->setPaymentMethod('paypal');
    $item_1 = new Item();
    $item_1->setName($title)
      /** item name **/
      ->setCurrency('USD')
      ->setQuantity(1)
      ->setPrice($paypalTotal);
    /** unit price **/
    $item_list = new ItemList();
    $item_list->setItems(array($item_1));
    $amount = new Amount();
    $amount->setCurrency('USD')
      ->setTotal($paypalTotal);
    $transaction = new Transaction();
    $transaction->setAmount($amount)
      ->setItemList($item_list)
      ->setDescription($title . ' Via PayPal');
    $redirect_urls = new RedirectUrls();
    $redirect_urls->setReturnUrl($notify_url)
      /** Specify return URL **/
      ->setCancelUrl($cancel_url);
    $payment = new Payment();
    $payment->setIntent('Sale')
      ->setPayer($payer)
      ->setRedirectUrls($redirect_urls)
      ->setTransactions(array($transaction));

    try {
      $payment->create($this->api_context);
    } catch (\PayPal\Exception\PPConnectionException $ex) {
      return redirect()->back()->with('error', $ex->getMessage());
    }

    foreach ($payment->getLinks() as $link) {
      if ($link->getRel() == 'approval_url') {
        $redirect_url = $link->getHref();
        break;
      }
    }

    // put some data in session before redirect to paypal url
    $request->session()->put('bookingId', $booking_details->id);   // db row number
    $request->session()->put('paymentId', $payment->getId());

    if (isset($redirect_url)) {
      /** redirect to paypal **/
      return Redirect::away($redirect_url);
    }
  }

  public function notify(Request $request)
  {
    // get the information from session
    $bookingId = $request->session()->get('bookingId');
    $paymentId = $request->session()->get('paymentId');

    // get the information from the url
    $urlInfo = $request->all();

    if (empty($urlInfo['token']) || empty($urlInfo['PayerID'])) {
      return redirect()->route('room_booking.cancel');
    }

    /** Execute The Payment **/
    $payment = Payment::get($paymentId, $this->api_context);
    $execution = new PaymentExecution();
    $execution->setPayerId($urlInfo['PayerID']);
    $result = $payment->execute($execution, $this->api_context);

    if ($result->getState() == 'approved') {
      // update the payment status for room booking in database
      $bookingInfo = RoomBooking::findOrFail($bookingId);

      $bookingInfo->update(['payment_status' => 1]);

      $roomBooking = new RoomBookingController();

      // generate an invoice in pdf format
      $invoice = $roomBooking->generateInvoice($bookingInfo);

      // update the invoice field information in database
      $bookingInfo->update(['invoice' => $invoice]);

      // send a mail to the customer with an invoice
      $roomBooking->sendMail($bookingInfo);

      // remove all session data
      $request->session()->forget('bookingId');
      $request->session()->forget('paymentId');

      return redirect()->route('room_booking.complete');
    } else {
      return redirect()->route('room_booking.cancel');
    }
  }
}
