<?php

namespace App\Http\Controllers\FrontEnd\Package;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\Package\PackageBookingController;
use App\Models\PackageManagement\PackageBooking;
use App\Models\PaymentGateway\OnlineGateway;
use App\Traits\MiscellaneousTrait;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class RazorpayController extends Controller
{
  use MiscellaneousTrait;

  private $key, $secret, $api;

  public function __construct()
  {
    $data = OnlineGateway::whereKeyword('razorpay')->first();
    $razorpayData = json_decode($data->information, true);

    $this->key = $razorpayData['razorpay_key'];
    $this->secret = $razorpayData['razorpay_secret'];
    $this->api = new Api($this->key, $this->secret);
  }

  public function bookingProcess(Request $request)
  {
    $packageBooking = new PackageBookingController();

    // do calculation
    $calculatedData = $packageBooking->calculation($request);

    $currencyInfo = MiscellaneousTrait::getCurrencyInfo();

    // checking whether the currency is set to 'INR' or not
    if ($currencyInfo->base_currency_text !== 'INR') {
      return redirect()->back()->with('error', 'Invalid currency for razorpay payment.');
    }

    $information['subtotal'] = $calculatedData['subtotal'];
    $information['discount'] = $calculatedData['discount'];
    $information['total'] = $calculatedData['total'];
    $information['currency_symbol'] = $currencyInfo->base_currency_symbol;
    $information['currency_symbol_position'] = $currencyInfo->base_currency_symbol_position;
    $information['currency_text'] = $currencyInfo->base_currency_text;
    $information['currency_text_position'] = $currencyInfo->base_currency_text_position;
    $information['method'] = 'Razorpay';
    $information['type'] = 'online';

    // store the package booking information in database
    $booking_details = $packageBooking->storeData($request, $information);

    $notify_url = route('package_booking.razorpay.notify');

    $orderData = [
      'receipt'         => 'Package Booking',
      'amount'          => $calculatedData['total'] * 100, // convert total rent into smallest unit
      'currency'        => 'INR',
      'payment_capture' => 1 // auto capture
    ];

    $razorpayOrder = $this->api->order->create($orderData);

    $data = [
      'key'               => $this->key,
      'amount'            => $orderData['amount'],
      'name'              => $orderData['receipt'],
      'description'       => 'Booking Tour Package Using Razorpay Gateway',
      'prefill'           => [
        'name'              => $booking_details->customer_name,
        'email'             => $booking_details->customer_email,
        'contact'           => $booking_details->customer_phone
      ],
      'notes'             => [
        'merchant_order_id' => $booking_details->order_number
      ],
      'order_id'          => $razorpayOrder['id']
    ];

    $jsonData = json_encode($data);

    // put some data in session before redirect to razorpay url
    $request->session()->put('bookingId', $booking_details->id);   // db row number
    $request->session()->put('razorpayOrderId', $razorpayOrder['id']);

    return view('frontend.partials.razorpay', compact('jsonData', 'notify_url'));
  }

  public function notify(Request $request)
  {
    // get the information from session
    $bookingId = $request->session()->get('bookingId');
    $razorpayOrderId = $request->session()->get('razorpayOrderId');

    // get the information from the url, which has send by razorpay through post request
    $urlInfo = $request->all();

    // let, assume that the transaction was successfull
    $success = true;

    // Either razorpay_order_id or razorpay_subscription_id must be present
    // the keys of $attributes array must be follow razorpay convention
    try {
      $attributes = [
        'razorpay_order_id' => $razorpayOrderId,
        'razorpay_payment_id' => $urlInfo['razorpayPaymentId'],
        'razorpay_signature' => $urlInfo['razorpaySignature']
      ];

      $this->api->utility->verifyPaymentSignature($attributes);
    } catch (SignatureVerificationError $e) {
      $success = false;
    }

    if ($success === true) {
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
      $request->session()->forget('razorpayOrderId');

      return redirect()->route('package_booking.complete');
    } else {
      return redirect()->route('package_booking.cancel');
    }
  }
}
