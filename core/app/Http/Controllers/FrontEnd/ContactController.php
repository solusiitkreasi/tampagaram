<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Traits\MiscellaneousTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Config;

class ContactController extends Controller
{
  use MiscellaneousTrait;

  public function __construct()
  {
    $bs = DB::table('basic_settings')->select('google_recaptcha_site_key', 'google_recaptcha_secret_key')->first();
    Config::set('captcha.sitekey', $bs->google_recaptcha_site_key);
    Config::set('captcha.secret', $bs->google_recaptcha_secret_key);
  }

  public function contact()
  {
    $queryResult['breadcrumbInfo'] = MiscellaneousTrait::getBreadcrumb();

    $language = MiscellaneousTrait::getLanguage();

    $queryResult['pageHeading'] = MiscellaneousTrait::getPageHeading($language);

    return view('frontend.contact', $queryResult);
  }

  public function sendMail(Request $request)
  {
    $rules = [
      'full_name' => 'required',
      'email' => 'required|email:rfc,dns',
      'subject' => 'required',
      'message' => 'required'
    ];

    $bs = DB::table('basic_settings')->select('google_recaptcha_status')->first();

    if ($bs->google_recaptcha_status == 1) {
        $rules['g-recaptcha-response'] = 'required|captcha';
    }
    $messages = [
        'g-recaptcha-response.required' => 'Please verify that you are not a robot.',
        'g-recaptcha-response.captcha' => 'Captcha error! try again later or contact site admin.',
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    $mailInfo = DB::table('basic_settings')->select('to_mail')
      ->where('uniqid', 12345)
      ->first();

    $from = $request->email;
    $name = $request->full_name;
    $to = $mailInfo->to_mail;
    $subject = $request->subject;
    $message = $request->message;

    $mail = new PHPMailer(true);

    try {
      $mail->setFrom($from, $name);
      $mail->addAddress($to);

      $mail->isHTML(true);
      $mail->Subject = $subject;
      $mail->Body = $message;

      $mail->send();

      $request->session()->flash('success', 'Mail has been sent.');
    } catch (Exception $e) {
      $request->session()->flash('error', 'Mail could not be sent!');
    }

    return redirect()->back();
  }
}
