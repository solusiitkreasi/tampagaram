<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\PackageManagement\Package;
use App\Models\PackageManagement\PackageBooking;
use App\Models\RoomManagement\Room;
use App\Models\RoomManagement\RoomBooking;
use App\Rules\MatchEmailRule;
use App\Rules\MatchOldPasswordRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class AdminController extends Controller
{
  public function login()
  {
    return view('backend.admin.login');
  }

  public function authentication(Request $request)
  {
    $rules = [
      'username' => 'required',
      'password' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    if (Auth::guard('admin')->attempt([
      'username' => $request->username,
      'password' => $request->password
    ])) {
      return redirect()->route('admin.dashboard');
    } else {
      return redirect()->back()->with('alert', 'Username or Password does not match!');
    }
  }

  public function forgetPassword()
  {
    return view('backend.admin.forget_password');
  }

  public function sendMail(Request $request)
  {
    $rules = [
      'email' => [
        'required',
        'email:rfc,dns',
        new MatchEmailRule('admin')
      ]
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors())->withInput();
    }

    // create a new password and store it in db
    $newPassword = uniqid();

    $admin = Admin::where('email', $request->email)->firstOrFail();

    $admin->update([
      'password' => Hash::make($newPassword)
    ]);

    // send newly created password to admin via email
    $info = DB::table('basic_settings')->select('smtp_status', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name')
      ->where('uniqid', 12345)
      ->first();

    // initialize a new mail
    $mail = new PHPMailer(true);

    // if smtp status == 1, then set some value for PHPMailer
    if ($info->smtp_status == 1) {
      $mail->isSMTP();
      $mail->Host       = $info->smtp_host;
      $mail->SMTPAuth   = true;
      $mail->Username   = $info->smtp_username;
      $mail->Password   = $info->smtp_password;

      if ($info->encryption == 'TLS') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      }

      $mail->Port       = $info->smtp_port;
    }

    // finally add other informations and send the mail
    try {
      $mail->setFrom($info->from_mail, $info->from_name);
      $mail->addAddress($request->email);

      $mail->isHTML(true);
      $mail->Subject = 'Reset Password';
      $mail->Body = 'Hello ' . $admin->first_name . ',<br/><br/>Your password has been reset. Your new password is: <strong>' . $newPassword . '</strong><br/>Now, you can login with your new password. You can change your password from your Dashboard.<br/><br/>Thank you.';

      $mail->send();

      $request->session()->flash('success', 'A mail has been sent to your email address with new password.');
    } catch (Exception $e) {
      $request->session()->flash('warning', 'Mail could not be sent. Mailer Error: ' . $mail->ErrorInfo);
    }

    return redirect()->back();
  }

  public function redirectToDashboard()
  {
    $data['pbookings'] = PackageBooking::orderBy('id', 'desc')->limit(10)->get();
    $data['rbookings'] = RoomBooking::orderBy('id', 'desc')->limit(10)->get();

    $data['roomsCount'] = Room::count();
    $data['allRbCount'] = RoomBooking::count();
    $data['paidRbCount'] = RoomBooking::where('payment_status', 1)->count();
    $data['packagesCount'] = Package::count();
    $data['allPbCount'] = PackageBooking::count();
    $data['paidPbCount'] = PackageBooking::where('payment_status', 1)->count();

    return view('backend.admin.dashboard', $data);
  }

  public function editProfile()
  {
    $adminInfo = Auth::guard('admin')->user();

    return view('backend.admin.edit_profile', compact('adminInfo'));
  }

  public function updateProfile(Request $request)
  {
    $authAdminId = Auth::guard('admin')->user()->id;

    $rules = [
      'username' => [
        'required',
        Rule::unique('admins')->ignore($authAdminId)
      ],
      'email' => [
        'required',
        'email:rfc,dns',
        Rule::unique('admins')->ignore($authAdminId)
      ],
      'first_name' => 'required',
      'last_name' => 'required'
    ];

    $imgURL = $request->image;

    if ($request->filled('image')) {
      $allowedExtensions = array('jpg', 'jpeg', 'png', 'svg');
      $fileExtension = pathinfo($imgURL, PATHINFO_EXTENSION);

      $rules['image'] = function ($attribute, $value, $fail) use ($allowedExtensions, $fileExtension) {
        if (!in_array($fileExtension, $allowedExtensions)) {
          $fail('Only .jpg, .jpeg, .png and .svg file is allowed.');
        }
      };
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator);
    }

    $admin = Auth::guard('admin')->user();

    if ($request->filled('image')) {
      // first, delete the previous image from local storage
      if (!is_null($admin->image) &&
        file_exists('assets/img/admins/' . $admin->image)
      ) {
        unlink('assets/img/admins/' . $admin->image);
      }

      // second, set a name for the image and store it to local storage
      $imgName = time() . '.' . $fileExtension;
      $directory = './assets/img/admins/';

      copy($imgURL, $directory . $imgName);
    }

    $admin->update([
      'first_name' => $request->first_name,
      'last_name' => $request->last_name,
      'image' => $request->filled('image') ? $imgName : $admin->image,
      'username' => $request->username,
      'email' => $request->email
    ]);

    $request->session()->flash('success', 'Profile updated successfully!');

    return redirect()->back();
  }

  public function changePassword()
  {
    return view('backend.admin.change_password');
  }

  public function updatePassword(Request $request)
  {
    $rules = [
      'current_password' => [
        'required',
        new MatchOldPasswordRule('admin')
      ],
      'new_password' => 'required|confirmed',
      'new_password_confirmation' => 'required'
    ];

    $messages = [
      'new_password.confirmed' => 'Password confirmation does not match.',
      'new_password_confirmation.required' => 'The confirm new password field is required.'
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $admin = Auth::guard('admin')->user();

    $admin->update([
      'password' => Hash::make($request->new_password)
    ]);

    $request->session()->flash('success', 'Password updated successfully!');

    return 'success';
  }

  public function logout(Request $request)
  {
    Auth::guard('admin')->logout();

    // invalidate the admin's session
    $request->session()->invalidate();

    return redirect()->route('admin.login');
  }

  public function changeTheme(Request $request) {
    return redirect()->back()->withCookie(cookie()->forever('admin-theme', $request->theme));
  }

}
