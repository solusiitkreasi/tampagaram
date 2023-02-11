<?php

namespace App\Http\Controllers\FrontEnd\Room;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\Room\RoomBookingController;
use App\Models\PaymentGateway\OnlineGateway;
use App\Models\RoomManagement\RoomBooking;
use App\Traits\MiscellaneousTrait;
use Illuminate\Http\Request;

class MercadoPagoController extends Controller
{
  use MiscellaneousTrait;

  private $token, $sandbox_status;

  public function __construct()
  {
    $data = OnlineGateway::whereKeyword('mercadopago')->first();
    $mercadopagoData = json_decode($data->information, true);

    $this->token = $mercadopagoData['mercadopago_token'];
    $this->sandbox_status = $mercadopagoData['sandbox_status'];
  }

  public function bookingProcess(Request $request)
  {
    $roomBooking = new RoomBookingController();

    // do calculation
    $calculatedData = $roomBooking->calculation($request);

    $title = 'Room Booking';

    $available_currency = array('ARS', 'BOB', 'BRL', 'CLF', 'CLP', 'COP', 'CRC', 'CUC', 'CUP', 'DOP', 'EUR', 'GTQ', 'HNL', 'MXN', 'NIO', 'PAB', 'PEN', 'PYG', 'USD', 'UYU', 'VEF', 'VES');

    $currencyInfo = MiscellaneousTrait::getCurrencyInfo();

    // checking whether the base currency is allowed or not
    if (!in_array($currencyInfo->base_currency_text, $available_currency)) {
      return redirect()->back()->with('error', 'Invalid currency for mercadopago payment.');
    }

    $information['subtotal'] = $calculatedData['subtotal'];
    $information['discount'] = $calculatedData['discount'];
    $information['total'] = $calculatedData['total'];
    $information['currency_symbol'] = $currencyInfo->base_currency_symbol;
    $information['currency_symbol_position'] = $currencyInfo->base_currency_symbol_position;
    $information['currency_text'] = $currencyInfo->base_currency_text;
    $information['currency_text_position'] = $currencyInfo->base_currency_text_position;
    $information['method'] = 'MercadoPago';
    $information['type'] = 'online';

    // store the room booking information in database
    $booking_details = $roomBooking->storeData($request, $information);

    $notify_url = route('room_booking.mercadopago.notify');
    $complete_url = route('room_booking.complete');
    $cancel_url = route('room_booking.cancel');

    $curl = curl_init();

    $preferenceData = [
      'items' => [
        [
          'id' => uniqid(),
          'title' => $title,
          'description' => 'Booking Room Using MercadoPago Gateway',
          'quantity' => 1,
          'currency' => $booking_details->currency_text,
          'unit_price' => $calculatedData['total']
        ]
      ],
      'payer' => [
        'email' => $booking_details->customer_email
      ],
      'back_urls' => [
        'success' => $complete_url,
        'pending' => '',
        'failure' => $cancel_url
      ],
      'notification_url' => $notify_url,
      'auto_return' => 'approved'
    ];

    $httpHeader = ['Content-Type: application/json'];

    $url = 'https://api.mercadopago.com/checkout/preferences?access_token=' . $this->token;

    $curlOPT = [
      CURLOPT_URL             => $url,
      CURLOPT_CUSTOMREQUEST   => 'POST',
      CURLOPT_POSTFIELDS      => json_encode($preferenceData, true),
      CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
      CURLOPT_RETURNTRANSFER  => true,
      CURLOPT_TIMEOUT         => 30,
      CURLOPT_HTTPHEADER      => $httpHeader
    ];

    curl_setopt_array($curl, $curlOPT);

    $response = curl_exec($curl);
    $responseInfo = json_decode($response, true);

    curl_close($curl);

    // put some data in session before redirect to mercadopago url
    $request->session()->put('bookingId', $booking_details->id);   // db row number

    if ($this->sandbox_status == 1) {
      return redirect($responseInfo['sandbox_init_point']);
    } else {
      return redirect($responseInfo['init_point']);
    }
  }

  public function notify(Request $request)
  {
    // get the information from session
    $bookingId = $request->session()->get('bookingId');

    $payment_url = 'https://api.mercadopago.com/v1/payments/' . $request['data']['id'] . '?access_token=' . $this->token;

    $paymentData = $this->curlCalls($payment_url);
    $paymentInfo = json_decode($paymentData, true);

    if ($paymentInfo['status'] == 'approved') {
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

      return redirect()->route('room_booking.complete');
    } else {
      return redirect()->route('room_booking.cancel');
    }
  }

  public function curlCalls($url)
  {
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $curlData = curl_exec($curl);

    curl_close($curl);

    return $curlData;
  }
}
