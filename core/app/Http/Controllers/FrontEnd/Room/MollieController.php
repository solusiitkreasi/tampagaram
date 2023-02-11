<?php

namespace App\Http\Controllers\FrontEnd\Room;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\Room\RoomBookingController;
use App\Models\RoomManagement\RoomBooking;
use App\Traits\MiscellaneousTrait;
use Illuminate\Http\Request;
use Mollie\Laravel\Facades\Mollie;

class MollieController extends Controller
{
  use MiscellaneousTrait;

  public function bookingProcess(Request $request)
  {
    $roomBooking = new RoomBookingController();

    // do calculation
    $calculatedData = $roomBooking->calculation($request);

    $title = 'Room Booking';

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

    // store the room booking information in database
    $roomBooking = new RoomBookingController();
    $booking_details = $roomBooking->storeData($request, $information);

    $notify_url = route('room_booking.mollie.notify');

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
