<?php

namespace App\Http\Controllers\FrontEnd\Package;

use Anand\LaravelPaytmWallet\Facades\PaytmWallet;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\Package\PackageBookingController;
use App\Models\PackageManagement\PackageBooking;
use App\Traits\MiscellaneousTrait;
use Illuminate\Http\Request;

class PaytmController extends Controller
{
  use MiscellaneousTrait;

  public function bookingProcess(Request $request)
  {
    $packageBooking = new PackageBookingController();

    // do calculation
    $calculatedData = $packageBooking->calculation($request);

    $currencyInfo = MiscellaneousTrait::getCurrencyInfo();

    // checking whether the currency is set to 'INR' or not
    if ($currencyInfo->base_currency_text !== 'INR') {
      return redirect()->back()->with('error', 'Invalid currency for paytm payment.');
    }

    $information['subtotal'] = $calculatedData['subtotal'];
    $information['discount'] = $calculatedData['discount'];
    $information['total'] = $calculatedData['total'];
    $information['currency_symbol'] = $currencyInfo->base_currency_symbol;
    $information['currency_symbol_position'] = $currencyInfo->base_currency_symbol_position;
    $information['currency_text'] = $currencyInfo->base_currency_text;
    $information['currency_text_position'] = $currencyInfo->base_currency_text_position;
    $information['method'] = 'Paytm';
    $information['type'] = 'online';

    // store the package booking information in database
    $booking_details = $packageBooking->storeData($request, $information);

    $payment = PaytmWallet::with('receive');

    $payment->prepare([
      'order' => time(),
      'user' => uniqid(),
      'mobile_number' => $booking_details->customer_phone,
      'email' => $booking_details->customer_email,
      'amount' => $calculatedData['total'],
      'callback_url' => route('package_booking.paytm.notify')
    ]);

    // put some data in session before redirect to paytm url
    $request->session()->put('bookingId', $booking_details->id);   // db row number

    return $payment->receive();
  }

  public function notify(Request $request)
  {
    // get the information from session
    $bookingId = $request->session()->get('bookingId');

    $transaction = PaytmWallet::with('receive');

    // this response is needed to check the transaction status
    $response = $transaction->response();

    if ($transaction->isSuccessful()) {
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
    } else if ($transaction->isFailed()) {
      return redirect()->route('package_booking.cancel');
    }
  }
}
