<?php

namespace App\Http\Controllers\FrontEnd\Room;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\Room\RoomBookingController;
use App\Models\PaymentGateway\OnlineGateway;
use App\Models\RoomManagement\RoomBooking;
use App\Traits\MiscellaneousTrait;
use Cartalyst\Stripe\Exception\CardErrorException;
use Cartalyst\Stripe\Exception\UnauthorizedException;
use Cartalyst\Stripe\Stripe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class StripeController extends Controller
{
  public function __construct()
  {
    $data = OnlineGateway::whereKeyword('stripe')->first();
    $stripeConf = json_decode($data->information, true);

    Config::set('services.stripe.key', $stripeConf["key"]);
    Config::set('services.stripe.secret', $stripeConf["secret"]);
  }

  public function bookingProcess(Request $request)
  {
    $roomBooking = new RoomBookingController();

    // do calculation
    $calculatedData = $roomBooking->calculation($request);

    $currencyInfo = MiscellaneousTrait::getCurrencyInfo();

    $information['subtotal'] = $calculatedData['subtotal'];
    $information['discount'] = $calculatedData['discount'];
    $information['total'] = $calculatedData['total'];
    $information['currency_symbol'] = $currencyInfo->base_currency_symbol;
    $information['currency_symbol_position'] = $currencyInfo->base_currency_symbol_position;
    $information['currency_text'] = $currencyInfo->base_currency_text;
    $information['currency_text_position'] = $currencyInfo->base_currency_text_position;
    $information['method'] = 'Stripe';
    $information['type'] = 'online';

    // store the room booking information in database
    $booking_details = $roomBooking->storeData($request, $information);

    // changing the currency before redirect to PayPal
    if ($currencyInfo->base_currency_text !== 'USD') {
      $rate = $currencyInfo->base_currency_rate;
      $convertedTotal = round(($calculatedData['total'] / $rate), 2);
    }

    $stripeTotal = $currencyInfo->base_currency_text === 'USD' ? $calculatedData['total'] : $convertedTotal;

    try {
      // initialize stripe
      $stripe = Stripe::make(Config::get('services.stripe.secret'));

      try {
        // generate token
        $token = $stripe->tokens()->create([
          'card' => [
            'number'    => $request['card_number'],
            'cvc'       => $request['cvc_number'],
            'exp_month' => $request['expiry_month'],
            'exp_year'  => $request['expiry_year']
          ]
        ]);

        // generate charge
        $charge = $stripe->charges()->create([
          'source' => $token['id'],
          'currency' => 'USD',
          'amount'   => $stripeTotal
        ]);

        if ($charge['status'] == 'succeeded') {
          // update the payment status for room booking in database
          $bookingInfo = RoomBooking::findOrFail($booking_details->id);

          $bookingInfo->update(['payment_status' => 1]);

          // generate an invoice in pdf format
          $invoice = $roomBooking->generateInvoice($bookingInfo);

          // update the invoice field information in database
          $bookingInfo->update(['invoice' => $invoice]);

          // send a mail to the customer with an invoice
          $roomBooking->sendMail($bookingInfo);

          return redirect()->route('room_booking.complete');
        } else {
          return redirect()->route('room_booking.cancel');
        }
      } catch (CardErrorException $e) {
        $request->session()->flash('error', $e->getMessage());

        return redirect()->route('room_booking.cancel');
      }
    } catch (UnauthorizedException $e) {
      $request->session()->flash('error', $e->getMessage());

      return redirect()->route('room_booking.cancel');
    }
  }
}
