<?php

namespace App\Http\Controllers\BackEnd\BasicSettings;

use App\Http\Controllers\Controller;
use App\Http\Requests\AppearanceRequest;
use App\Http\Requests\CurrencyRequest;
use App\Http\Requests\MailFromAdminRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class BasicSettingsController extends Controller
{
  public function fileManager()
  {
    return view('backend.basic_settings.file-manager');
  }
  public function themeVersion()
  {
    $data = DB::table('basic_settings')->select('theme_version', 'home_version')
      ->where('uniqid', 12345)
      ->first();

    return view('backend.theme_version', ['data' => $data]);
  }

  public function updateThemeVersion(Request $request)
  {
    $rule = [
      'theme_version' => 'required'
    ];

    $validator = Validator::make($request->all(), $rule);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    DB::table('basic_settings')->update([
      'theme_version' => $request->theme_version,
      'home_version' => $request->home_version
    ]);

    $request->session()->flash('success', 'Version updated successfully!');

    return 'success';
  }


  public function favicon()
  {
    $data = DB::table('basic_settings')->select('favicon')->where('uniqid', 12345)
      ->first();

    return view('backend.basic_settings.favicon', ['data' => $data]);
  }

  public function updateFavicon(Request $request)
  {
    $faviconURL = $request->favicon;

    $allowedExtensions = array('jpg', 'jpeg', 'png', 'svg');
    $fileExtension = pathinfo($faviconURL, PATHINFO_EXTENSION);

    $rule = [
      'favicon' => [
        'required',
        function ($attribute, $value, $fail) use ($allowedExtensions, $fileExtension) {
          if (!in_array($fileExtension, $allowedExtensions)) {
            $fail('Only .jpg, .jpeg, .png and .svg file is allowed.');
          }
        }
      ]
    ];

    $validator = Validator::make($request->all(), $rule);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator);
    }

    // first, get the favicon from db
    $data = DB::table('basic_settings')->select('favicon')->where('uniqid', 12345)
      ->first();

    // second, delete the previous favicon from local storage
    if (!is_null($data->favicon) && file_exists('assets/img/' . $data->favicon)) {
      unlink('assets/img/' . $data->favicon);
    }

    // third, set a name for the favicon and store it to local storage
    $iconName = time() . '.' . $fileExtension;
    $directory = './assets/img/';

    if (!file_exists($directory)) {
      mkdir($directory, 0777, true);
    }

    copy($faviconURL, $directory . $iconName);

    // finally, store the favicon into db
    DB::table('basic_settings')->update(['favicon' => $iconName]);

    $request->session()->flash('success', 'Favicon updated successfully!');

    return redirect()->back();
  }


  public function logo()
  {
    $data = DB::table('basic_settings')->select('logo')->where('uniqid', 12345)
      ->first();

    return view('backend.basic_settings.logo', ['data' => $data]);
  }

  public function updateLogo(Request $request)
  {
    $logoURL = $request->logo;

    $allowedExtensions = array('jpg', 'jpeg', 'png', 'svg');
    $fileExtension = pathinfo($logoURL, PATHINFO_EXTENSION);

    $rule = [
      'logo' => [
        'required',
        function ($attribute, $value, $fail) use ($allowedExtensions, $fileExtension) {
          if (!in_array($fileExtension, $allowedExtensions)) {
            $fail('Only .jpg, .jpeg, .png and .svg file is allowed.');
          }
        }
      ]
    ];

    $validator = Validator::make($request->all(), $rule);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator);
    }

    // first, get the logo from db
    $data = DB::table('basic_settings')->select('logo')->where('uniqid', 12345)
      ->first();

    // second, delete the previous logo from local storage
    if (!is_null($data->logo) && file_exists('assets/img/' . $data->logo)) {
      unlink('assets/img/' . $data->logo);
    }

    // third, set a name for the logo and store it to local storage
    $logoName = time() . '.' . $fileExtension;
    $directory = './assets/img/';

    if (!file_exists($directory)) {
      mkdir($directory, 0777, true);
    }

    copy($logoURL, $directory . $logoName);

    // finally, store the logo into db
    DB::table('basic_settings')->update(['logo' => $logoName]);

    $request->session()->flash('success', 'Logo updated successfully!');

    return redirect()->back();
  }


  public function information()
  {
    $data = DB::table('basic_settings')
      ->select('website_title', 'support_email', 'support_contact', 'address', 'latitude', 'longitude')
      ->where('uniqid', 12345)
      ->first();

    return view('backend.basic_settings.information', ['data' => $data]);
  }

  public function updateInfo(Request $request)
  {
    $rules = [
      'website_title' => 'required',
      'support_email' => 'required',
      'support_contact' => 'required',
      'address' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    DB::table('basic_settings')->update([
      'website_title' => $request->website_title,
      'support_email' => $request->support_email,
      'support_contact' => $request->support_contact,
      'address' => $request->address,
      'latitude' => $request->latitude,
      'longitude' => $request->longitude
    ]);

    $request->session()->flash('success', 'Information updated successfully!');

    return redirect()->back();
  }


  public function currency()
  {
    $data = DB::table('basic_settings')
      ->select('base_currency_symbol', 'base_currency_symbol_position', 'base_currency_text', 'base_currency_text_position', 'base_currency_rate')
      ->where('uniqid', 12345)
      ->first();

    return view('backend.basic_settings.currency', ['data' => $data]);
  }

  public function updateCurrency(CurrencyRequest $request)
  {
    DB::table('basic_settings')->update([
      'base_currency_symbol' => $request->base_currency_symbol,
      'base_currency_symbol_position' => $request->base_currency_symbol_position,
      'base_currency_text' => $request->base_currency_text,
      'base_currency_text_position' => $request->base_currency_text_position,
      'base_currency_rate' => $request->base_currency_rate
    ]);

    $request->session()->flash('success', 'Currency updated successfully!');

    return redirect()->back();
  }


  public function appearance()
  {
    $data = DB::table('basic_settings')
      ->select('primary_color', 'secondary_color', 'breadcrumb_overlay_color', 'breadcrumb_overlay_opacity')
      ->where('uniqid', 12345)
      ->first();

    return view('backend.basic_settings.appearance', ['data' => $data]);
  }

  public function updateAppearance(AppearanceRequest $request)
  {
    DB::table('basic_settings')->update([
      'primary_color' => $request->primary_color,
      'secondary_color' => $request->secondary_color,
      'breadcrumb_overlay_color' => $request->breadcrumb_overlay_color,
      'breadcrumb_overlay_opacity' => $request->breadcrumb_overlay_opacity
    ]);

    $request->session()->flash('success', 'Appearance updated successfully!');

    return redirect()->back();
  }


  public function mailFromAdmin()
  {
    $data = DB::table('basic_settings')
      ->select('smtp_status', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name')
      ->where('uniqid', 12345)
      ->first();

    return view('backend.basic_settings.email.mail_from_admin', ['data' => $data]);
  }

  public function updateMailFromAdmin(MailFromAdminRequest $request)
  {
    DB::table('basic_settings')->update([
      'smtp_status' => $request->smtp_status,
      'smtp_host' => $request->smtp_host,
      'smtp_port' => $request->smtp_port,
      'encryption' => $request->encryption,
      'smtp_username' => $request->smtp_username,
      'smtp_password' => $request->smtp_password,
      'from_mail' => $request->from_mail,
      'from_name' => $request->from_name
    ]);

    $request->session()->flash('success', 'Mail info updated successfully!');

    return redirect()->back();
  }


  public function mailToAdmin()
  {
    $data = DB::table('basic_settings')->select('to_mail')->where('uniqid', 12345)
      ->first();

    return view('backend.basic_settings.email.mail_to_admin', ['data' => $data]);
  }

  public function updateMailToAdmin(Request $request)
  {
    $rule = [
      'to_mail' => 'required'
    ];

    $message = [
      'to_mail.required' => 'The mail address field is required.'
    ];

    $validator = Validator::make($request->all(), $rule, $message);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    DB::table('basic_settings')->update([
      'to_mail' => $request->to_mail
    ]);

    $request->session()->flash('success', 'Mail info updated successfully!');

    return redirect()->back();
  }


  public function breadcrumb()
  {
    $data = DB::table('basic_settings')->select('breadcrumb')->where('uniqid', 12345)
      ->first();

    return view('backend.basic_settings.breadcrumb', ['data' => $data]);
  }

  public function updateBreadcrumb(Request $request)
  {
    $breadcrumbURL = $request->breadcrumb;

    $allowedExtensions = array('jpg', 'jpeg', 'png', 'svg');
    $fileExtension = pathinfo($breadcrumbURL, PATHINFO_EXTENSION);

    $rule = [
      'breadcrumb' => [
        'required',
        function ($attribute, $value, $fail) use ($allowedExtensions, $fileExtension) {
          if (!in_array($fileExtension, $allowedExtensions)) {
            $fail('Only .jpg, .jpeg, .png and .svg file is allowed.');
          }
        }
      ]
    ];

    $validator = Validator::make($request->all(), $rule);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator);
    }

    // first, get the breadcrumb from db
    $data = DB::table('basic_settings')->select('breadcrumb')->where('uniqid', 12345)
      ->first();

    // second, delete the previous breadcrumb from local storage
    if (!is_null($data->breadcrumb) && file_exists('assets/img/' . $data->breadcrumb)) {
      unlink('assets/img/' . $data->breadcrumb);
    }

    // third, set a name for the breadcrumb and store it to local storage
    $breadcrumbName = time() . '.' . $fileExtension;
    $directory = './assets/img/';

    if (!file_exists($directory)) {
      mkdir($directory, 0777, true);
    }

    copy($breadcrumbURL, $directory . $breadcrumbName);

    // finally, store the breadcrumb into db
    DB::table('basic_settings')->update(['breadcrumb' => $breadcrumbName]);

    $request->session()->flash('success', 'Breadcrumb updated successfully!');

    return redirect()->back();
  }


  public function scripts()
  {
    $data = DB::table('basic_settings')
      ->where('uniqid', 12345)
      ->first();

    return view('backend.basic_settings.scripts', ['data' => $data]);
  }

  public function updateScript(Request $request)
  {
    DB::table('basic_settings')->update([
      'google_recaptcha_status' => $request->google_recaptcha_status,
      'google_recaptcha_site_key' => $request->google_recaptcha_site_key,
      'google_recaptcha_secret_key' => $request->google_recaptcha_secret_key,
      'is_disqus' => $request->is_disqus,
      'disqus_shortname' => $request->disqus_shortname,
      'is_tawkto' => $request->is_tawkto,
      'tawkto_property_id' => $request->tawkto_property_id,
      'is_whatsapp' => $request->is_whatsapp,
      'whatsapp_number' => $request->whatsapp_number,
      'whatsapp_header_title' => $request->whatsapp_header_title,
      'whatsapp_popup_message' => clean($request->whatsapp_popup_message),
      'whatsapp_popup' => $request->whatsapp_popup,
      'facebook_login_status' => $request->facebook_login_status,
      'facebook_app_id' => $request->facebook_app_id,
      'facebook_app_secret' => $request->facebook_app_secret,
      'google_login_status' => $request->google_login_status,
      'google_client_id' => $request->google_client_id,
      'google_client_secret' => $request->google_client_secret
    ]);

    $request->session()->flash('success', 'Plugins info updated successfully!');

    return redirect()->back();
  }


  public function maintenanceMode()
  {
    $data = DB::table('basic_settings')
      ->select('maintenance_img', 'maintenance_status', 'maintenance_msg', 'secret_path')
      ->first();

    return view('backend.basic_settings.maintenance', ['data' => $data]);
  }

  public function updateMaintenance(Request $request)
  {
    $rules = [
      'maintenance_status' => 'required',
      'maintenance_msg' => 'required'
    ];

    $message = [
      'maintenance_msg.required' => 'The maintenance message field is required.'
    ];

    $maintenanceImgURL = $request->maintenance_img;

    if ($request->filled('maintenance_img')) {
      $allowedExtensions = array('jpg', 'jpeg', 'png', 'svg');
      $fileExtension = pathinfo($maintenanceImgURL, PATHINFO_EXTENSION);

      $rules['maintenance_img'] = function ($attribute, $value, $fail) use ($allowedExtensions, $fileExtension) {
        if (!in_array($fileExtension, $allowedExtensions)) {
          $fail('Only .jpg, .jpeg, .png and .svg file is allowed.');
        }
      };
    }

    $validator = Validator::make($request->all(), $rules, $message);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator);
    }

    // first, get the maintenance image from db
    $data = DB::table('basic_settings')->select('maintenance_img')
      ->where('uniqid', 12345)
      ->first();

    if ($request->filled('maintenance_img')) {
      // second, delete the previous maintenance image from local storage
      if (
        !is_null($data->maintenance_img) &&
        file_exists('assets/img/' . $data->maintenance_img)
      ) {
        unlink('assets/img/' . $data->maintenance_img);
      }

      // third, set a name for the maintenance image and store it to local storage
      $maintenanceImgName = time() . '.' . $fileExtension;
      $directory = './assets/img/';

      if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
      }

      copy($maintenanceImgURL, $directory . $maintenanceImgName);
    }

    $down = "down";
    if ($request->filled('secret_path')) {
      $down .= " --secret=" . $request->secret_path;
    }

    if ($request->maintenance_status == 1) {
      @unlink('core/storage/framework/down');
      Artisan::call($down);
    } else {
      Artisan::call('up');
    }

    DB::table('basic_settings')->update([
      'maintenance_img' => $request->filled('maintenance_img') ? $maintenanceImgName : $data->maintenance_img,
      'maintenance_status' => $request->maintenance_status,
      'maintenance_msg' => clean($request->maintenance_msg),
      'secret_path' => $request->secret_path
    ]);

    $request->session()->flash('success', 'Maintenance Info updated successfully!');

    return redirect()->back();
  }


  public function footerLogo()
  {
    $data = DB::table('basic_settings')->select('footer_logo')->where('uniqid', 12345)
      ->first();

    return view('backend.basic_settings.footer_logo', ['data' => $data]);
  }

  public function updateFooterLogo(Request $request)
  {
    $footerLogoURL = $request->footer_logo;

    $allowedExtensions = array('jpg', 'jpeg', 'png', 'svg');
    $fileExtension = pathinfo($footerLogoURL, PATHINFO_EXTENSION);

    $rule = [
      'footer_logo' => [
        'required',
        function ($attribute, $value, $fail) use ($allowedExtensions, $fileExtension) {
          if (!in_array($fileExtension, $allowedExtensions)) {
            $fail('Only .jpg, .jpeg, .png and .svg file is allowed.');
          }
        }
      ]
    ];

    $validator = Validator::make($request->all(), $rule);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator);
    }

    // first, get the footer logo from db
    $data = DB::table('basic_settings')->select('footer_logo')->where('uniqid', 12345)
      ->first();

    // second, delete the previous footer logo from local storage
    if (!is_null($data->footer_logo) && file_exists('assets/img/' . $data->footer_logo)) {
      unlink('assets/img/' . $data->footer_logo);
    }

    // third, set a name for the footer logo and store it to local storage
    $footerLogoName = time() . '.' . $fileExtension;
    $directory = './assets/img/';

    if (!file_exists($directory)) {
      mkdir($directory, 0777, true);
    }

    copy($footerLogoURL, $directory . $footerLogoName);

    // finally, store the footer logo into db
    DB::table('basic_settings')->update(['footer_logo' => $footerLogoName]);

    $request->session()->flash('success', 'Footer logo updated successfully!');

    return redirect()->back();
  }

  public function preloader(Request $request)
  {
    $data['data'] = DB::table('basic_settings')->select('preloader_status', 'preloader')->first();
    return view('backend.basic_settings.preloader', $data);
  }

  public function updatepreloader(Request $request)
  {
    $preloader = $request->preloader;
    $allowedExts = array('jpg', 'png', 'jpeg', 'gif', 'svg');
    $extPreloader = pathinfo($preloader, PATHINFO_EXTENSION);

    $rules = [
      'preloader_status' => 'required'
    ];

    if ($request->filled('preloader')) {
      $rules['preloader'] = [
        function ($attribute, $value, $fail) use ($extPreloader, $allowedExts) {
          if (!in_array($extPreloader, $allowedExts)) {
            return $fail("Only png, jpg, jpeg, gif, svg images are allowed");
          }
        }
      ];
    }

    $request->validate($rules);



    if ($request->filled('preloader')) {
      $filename = uniqid() . '.' . $extPreloader;
      @copy($preloader, 'assets/img/' . $filename);
    }

    $bs = DB::table('basic_settings')->first();

    DB::table('basic_settings')->update([
      'preloader' => $request->filled('preloader') ? $filename : $bs->preloader,
      'preloader_status' => $request->preloader_status
    ]);

    Session::flash('success', 'Preloader updated successfully.');
    return back();
  }
}
