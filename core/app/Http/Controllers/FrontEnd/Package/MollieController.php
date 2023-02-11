<?php

namespace App\Http\Controllers\FrontEnd\Package;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\Package\PackageBookingController;
use App\Models\PackageManagement\PackageBooking;
use App\Traits\MiscellaneousTrait;
use Illuminate\Http\Request;
use Mollie\Laravel\Facades\Mollie;

class MollieController extends Controller
{
  use MiscellaneousTrait;

  public function bookingProcess(Request $request)
  {
    $packageBooking = new PackageBookingController();

    // do calculation
    $calculatedData = $packageBooking->calculation($request);

    $title = 'Package Booking';

    $available_currency = array('AED', 'AUD', 'BGN', 'BRL', 'CAD', 'CHF', 'CZK', 'DKK', 'EUR', 'GBP', 'HKD', 'HRK', 'HUF', 'ILS', 'ISK', 'JPY', 'MXN', 'MYR', 'NOK', 'NZD', 'PHP', 'PLN', 'RON', 'RUB', 'SEK', 'SGD', 'THB', 'TWD', 'USD', 'ZAR');

    $currencyInfo = MiscellaneousTrait::getCurrencyInfo();

    // checking whether the base currency is allowed or not
    if (!in_array($currencyInfo->base_currency_text, $available_currency)) {
      return redirect()->back()->with('error', 'Invalid currency for mollie payment.');
    }

    $information['subtotal'] = $calculatedData['subtotal'];
    $information['discount'] = $calculatedData['discount'];
    $information['total'] = $calculatedData['total'];
    $information['currency_symbol'] = $currencyInfo->base_currency_symbol;
    $information['currency_symbol_position'] = $currencyInfo->base_currency_symbol_position;
    $information['currency_text'] = $currencyInfo->base_currency_text;
    $information['currency_text_position'] = $currencyInfo->base_currency_text_position;
    $information['method'] = 'Mollie';
    $information['type'] = 'online';

    // store the package booking information in database
    $booking_details = $packageBooking->storeData($request, $information);

    $notify_url = route('package_booking.mollie.notify');

    $payment = Mollie::api()->payments->create([
      'amount' => [
        'currency' => $currencyInfo->base_currency_text,
        /**
         * we must send the correct number of decimals.
         * thus, we have used sprintf() function for format
         */
        'value' => sprintf('%0.2f', $calculatedData['total'])
      ],
      'description' => $title,
      'redirectUrl' => $notify_url
    ]);

    // put some data in session before redirect to mollie url
    $request->session()->put('bookingId', $booking_details->id);   // db row number
    $request->session()->put('paymentId', $payment->id);

    return redirect($payment->getCheckoutUrl(), 303);
  }

  public function notify(Request $request)
  {
    // get the information from session
    $bookingId = $request->session()->get('bookingId');
    $paymentId = $request->session()->get('paymentId');

    $payment_info = Mollie::api()->payments->get($paymentId);

    if ($payment_info->isPaid() == true) {
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
      $request->session()->forget('paymentId');

      return redirect()->route('package_booking.complete');
    } else {
      return redirect()->route('package_booking.cancel');
    }
  }
}
