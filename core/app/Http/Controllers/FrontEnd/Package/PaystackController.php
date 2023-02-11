<?php

namespace App\Http\Controllers\FrontEnd\Package;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\Package\PackageBookingController;
use App\Models\PackageManagement\PackageBooking;
use App\Models\PaymentGateway\OnlineGateway;
use App\Traits\MiscellaneousTrait;
use Illuminate\Http\Request;

class PaystackController extends Controller
{
  use MiscellaneousTrait;

  private $api_key;

  public function __construct()
  {
    $data = OnlineGateway::whereKeyword('paystack')->first();
    $paystackData = json_decode($data->information, true);

    $this->api_key = $paystackData['paystack_key'];
  }

  public function bookingProcess(Request $request)
  {
    $packageBooking = new PackageBookingController();

    // do calculation
    $calculatedData = $packageBooking->calculation($request);

    $currencyInfo = MiscellaneousTrait::getCurrencyInfo();

    // checking whether the currency is set to 'NGN' or not
    if ($currencyInfo->base_currency_text !== 'NGN') {
      return redirect()->back()->with('error', 'Invalid currency for paystack payment.');
    }

    $information['subtotal'] = $calculatedData['subtotal'];
    $information['discount'] = $calculatedData['discount'];
    $information['total'] = $calculatedData['total'];
    $information['currency_symbol'] = $currencyInfo->base_currency_symbol;
    $information['currency_symbol_position'] = $currencyInfo->base_currency_symbol_position;
    $information['currency_text'] = $currencyInfo->base_currency_text;
    $information['currency_text_position'] = $currencyInfo->base_currency_text_position;
    $information['method'] = 'Paystack';
    $information['type'] = 'online';

    // store the package booking information in database
    $booking_details = $packageBooking->storeData($request, $information);

    $notify_url = route('package_booking.paystack.notify');

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL            => 'https://api.paystack.co/transaction/initialize',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST  => 'POST',
      CURLOPT_POSTFIELDS     => json_encode([
        'amount'       => intval($calculatedData['total'] * 100),
        'email'        => $booking_details->customer_email,
        'callback_url' => $notify_url
      ]),
      CURLOPT_HTTPHEADER     => [
        'authorization: Bearer ' . $this->api_key,
        'content-type: application/json',
        'cache-control: no-cache'
      ]
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    $transaction = json_decode($response, true);

    // put some data in session before redirect to paystack url
    $request->session()->put('bookingId', $booking_details->id);   // db row number

    if ($transaction['status'] == true) {
      return redirect($transaction['data']['authorization_url']);
    } else {
      return redirect()->back()->with('error', 'Sorry, transaction failed!');
    }
  }

  public function notify(Request $request)
  {
    // get the information from session
    $bookingId = $request->session()->get('bookingId');

    $urlInfo = $request->all();

    if ($urlInfo['trxref'] === $urlInfo['reference']) {
      // update the payment status for package booking in database
      $bookingInfo = PackageBooking::findOrFail($bookingId);

      $bookingInfo->update(['payment_status' => 1]);

      $packageBooking = new PackageBookingController();

      // generate an invoice in pdf format
      $invoice = $packageBooking->generateInvoice($bookingInfo);

      // update the invoice field information in database
      $bookingInfo->update(['invoice' => $invoice]);

      // send a mail to the customer with an invoice
      $packageBooking->sendMail($bookingInfo);

      // remove all session data
      $request->session()->forget('bookingId');

      return redirect()->route('package_booking.complete');
    } else {
      return redirect()->route('package_booking.cancel');
    }
  }
}
