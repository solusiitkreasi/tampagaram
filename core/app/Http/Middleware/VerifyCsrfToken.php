<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
  /**
   * The URIs that should be excluded from CSRF verification.
   *
   * @var array
   */
  protected $except = [
    '/room_booking/paypal/notify',
    '/room_booking/paytm/notify',
    '/room_booking/instamojo/notify',
    '/room_booking/paystack/notify',
    '/room_booking/flutterwave/notify',
    '/room_booking/mollie/notify',
    '/room_booking/razorpay/notify',
    '/room_booking/mercadopago/notify',
    '/package_booking/paypal/notify',
    '/package_booking/instamojo/notify',
    '/package_booking/paystack/notify',
    '/package_booking/razorpay/notify',
    '/package_booking/mollie/notify',
    '/package_booking/paytm/notify',
    '/package_booking/mercadopago/notify',
    '/package_booking/flutterwave/notify'
  ];
}
