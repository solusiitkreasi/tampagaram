<?php

use App\Http\Controllers\BackEnd\AdminController;
use App\Http\Controllers\BackEnd\BasicSettings\BasicSettingsController;
use App\Http\Controllers\BackEnd\BasicSettings\CookieAlertController;
use App\Http\Controllers\BackEnd\BasicSettings\MailTemplateController;
use App\Http\Controllers\BackEnd\BasicSettings\PageHeadingController;
use App\Http\Controllers\BackEnd\BasicSettings\SEOController;
use App\Http\Controllers\BackEnd\BasicSettings\SocialLinkController;
use App\Http\Controllers\BackEnd\BlogController as AdminBlogController;
use App\Http\Controllers\BackEnd\FAQController as AdminFAQController;
use App\Http\Controllers\BackEnd\FooterController;
use App\Http\Controllers\BackEnd\GalleryController as AdminGalleryController;
use App\Http\Controllers\BackEnd\HomePage\BrandSectionController;
use App\Http\Controllers\BackEnd\HomePage\FacilityController;
use App\Http\Controllers\BackEnd\HomePage\HeroSliderController;
use App\Http\Controllers\BackEnd\HomePage\HeroStaticController;
use App\Http\Controllers\BackEnd\HomePage\HeroVideoController;
use App\Http\Controllers\BackEnd\HomePage\IntroSectionController;
use App\Http\Controllers\BackEnd\HomePage\SectionHeadingController;
use App\Http\Controllers\BackEnd\HomePage\SectionsController;
use App\Http\Controllers\BackEnd\HomePage\TestimonialController;
use App\Http\Controllers\BackEnd\LanguageController;
use App\Http\Controllers\BackEnd\PackageController as AdminPackageController;
use App\Http\Controllers\BackEnd\PaymentGateway\OfflineGatewayController;
use App\Http\Controllers\BackEnd\PaymentGateway\OnlineGatewayController;
use App\Http\Controllers\BackEnd\PushNotificationController;
use App\Http\Controllers\BackEnd\RoomController as AdminRoomController;
use App\Http\Controllers\BackEnd\ServiceController as AdminServiceController;
use App\Http\Controllers\BackEnd\SummernoteController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\BlogController;
use App\Http\Controllers\FrontEnd\ContactController;
use App\Http\Controllers\FrontEnd\FAQController;
use App\Http\Controllers\FrontEnd\GalleryController;
use App\Http\Controllers\FrontEnd\HomeController;
use App\Http\Controllers\FrontEnd\Package\FlutterwaveController as PackageFlutterwaveController;
use App\Http\Controllers\FrontEnd\Package\InstamojoController as PackageInstamojoController;
use App\Http\Controllers\FrontEnd\Package\MercadoPagoController as PackageMercadoPagoController;
use App\Http\Controllers\FrontEnd\Package\MollieController as PackageMollieController;
use App\Http\Controllers\FrontEnd\Package\PackageBookingController;
use App\Http\Controllers\FrontEnd\Package\PackageController;
use App\Http\Controllers\FrontEnd\Package\PayPalController as PackagePayPalController;
use App\Http\Controllers\FrontEnd\Package\PaystackController as PackagePaystackController;
use App\Http\Controllers\FrontEnd\Package\PaytmController as PackagePaytmController;
use App\Http\Controllers\FrontEnd\Package\RazorpayController as PackageRazorpayController;
use App\Http\Controllers\FrontEnd\Package\StripeController as PackageStripeController;
use App\Http\Controllers\FrontEnd\PushNotificationController as UiPushNotificationController;
use App\Http\Controllers\FrontEnd\Room\FlutterwaveController;
use App\Http\Controllers\FrontEnd\Room\InstamojoController;
use App\Http\Controllers\FrontEnd\Room\MercadoPagoController;
use App\Http\Controllers\FrontEnd\Room\MollieController;
use App\Http\Controllers\FrontEnd\Room\PayPalController;
use App\Http\Controllers\FrontEnd\Room\PaystackController;
use App\Http\Controllers\FrontEnd\Room\PaytmController;
use App\Http\Controllers\FrontEnd\Room\RazorpayController;
use App\Http\Controllers\FrontEnd\Room\RoomBookingController;
use App\Http\Controllers\FrontEnd\Room\RoomController;
use App\Http\Controllers\FrontEnd\ServiceController;
use App\Http\Controllers\FrontEnd\UserController;
use Illuminate\Support\Facades\Route;

Route::fallback(function () {
  return view('errors.404');
});

/*
|--------------------------------------------------------------------------
| FrontEnd Routes
|--------------------------------------------------------------------------
*/
Route::post('/push-notification/store-endpoint', [UiPushNotificationController::class, 'store']);

Route::get('/change_language', [Controller::class, 'changeLanguage'])->name('change_language');

Route::middleware('language')->group(function () {
  Route::get('/', [HomeController::class, 'index'])->name('index');

  Route::get('/rooms', [RoomController::class, 'rooms'])->name('rooms');

  Route::get('/room_details/{id}/{slug}', [RoomController::class, 'roomDetails'])->name('room_details');
});

Route::post('/subscribe', [HomeController::class, 'subscribe'])->name('subscribe');

Route::post('/room_booking/apply_coupon', [RoomController::class, 'applyCoupon']);

Route::post('/room/store_review/{id}', [RoomController::class, 'storeReview'])->name('room.store_review');

Route::post('/room_booking', [RoomBookingController::class, 'makeRoomBooking'])->name('room_booking');

Route::get('/room_booking/paypal/notify', [PayPalController::class, 'notify'])->name('room_booking.paypal.notify');

Route::post('/room_booking/paytm/notify', [PaytmController::class, 'notify'])->name('room_booking.paytm.notify');

Route::get('/room_booking/instamojo/notify', [InstamojoController::class, 'notify'])->name('room_booking.instamojo.notify');

Route::get('/room_booking/paystack/notify', [PaystackController::class, 'notify'])->name('room_booking.paystack.notify');

Route::post('/room_booking/flutterwave/notify', [FlutterwaveController::class, 'notify'])->name('room_booking.flutterwave.notify');

Route::get('/room_booking/mollie/notify', [MollieController::class, 'notify'])->name('room_booking.mollie.notify');

Route::post('/room_booking/razorpay/notify', [RazorpayController::class, 'notify'])->name('room_booking.razorpay.notify');

Route::post('/room_booking/mercadopago/notify', [MercadoPagoController::class, 'notify'])->name('room_booking.mercadopago.notify');

Route::middleware('language')->group(function () {
  Route::get('/room_booking/complete', [RoomBookingController::class, 'complete'])->name('room_booking.complete');

  Route::get('/room_booking/cancel', [RoomBookingController::class, 'cancel'])->name('room_booking.cancel');

  Route::get('/services', [ServiceController::class, 'services'])->name('services');

  Route::get('/service_details/{id}/{slug}', [ServiceController::class, 'serviceDetails'])->name('service_details');

  Route::get('/blogs', [BlogController::class, 'blogs'])->name('blogs');

  Route::get('/blog_details/{id}/{slug}', [BlogController::class, 'blogDetails'])->name('blog_details');

  Route::get('/gallery', [GalleryController::class, 'gallery'])->name('gallery');

  Route::get('/packages', [PackageController::class, 'packages'])->name('packages');

  Route::get('/package_details/{id}/{slug}', [PackageController::class, 'packageDetails'])->name('package_details');
});

Route::post('/package_booking/apply_coupon', [PackageController::class, 'applyCoupon']);

Route::post('/package/store_review/{id}', [PackageController::class, 'storeReview'])->name('package.store_review');

Route::post('/package_booking', [PackageBookingController::class, 'makePackageBooking'])->name('package_booking');

Route::get('/package_booking/paypal/notify', [PackagePayPalController::class, 'notify'])->name('package_booking.paypal.notify');

Route::get('/package_booking/stripe/notify', [PackageStripeController::class, 'notify'])->name('package_booking.stripe.notify');

Route::get('/package_booking/instamojo/notify', [PackageInstamojoController::class, 'notify'])->name('package_booking.instamojo.notify');

Route::get('/package_booking/paystack/notify', [PackagePaystackController::class, 'notify'])->name('package_booking.paystack.notify');

Route::post('/package_booking/razorpay/notify', [PackageRazorpayController::class, 'notify'])->name('package_booking.razorpay.notify');

Route::get('/package_booking/mollie/notify', [PackageMollieController::class, 'notify'])->name('package_booking.mollie.notify');

Route::post('/package_booking/paytm/notify', [PackagePaytmController::class, 'notify'])->name('package_booking.paytm.notify');

Route::post('/package_booking/mercadopago/notify', [PackageMercadoPagoController::class, 'notify'])->name('package_booking.mercadopago.notify');

Route::post('/package_booking/flutterwave/notify', [PackageFlutterwaveController::class, 'notify'])->name('package_booking.flutterwave.notify');

Route::middleware('language')->group(function () {
  Route::get('/package_booking/complete', [PackageBookingController::class, 'complete'])->name('package_booking.complete');

  Route::get('/package_booking/cancel', [PackageBookingController::class, 'cancel'])->name('package_booking.cancel');

  Route::get('/faqs', [FAQController::class, 'faqs'])->name('faqs');

  Route::get('/contact', [ContactController::class, 'contact'])->name('contact');
});

Route::post('/contact/send_mail', [ContactController::class, 'sendMail'])->name('contact.send_mail');

Route::middleware(['guest:web'])->group(function () {
  Route::get('/login/facebook/callback', [UserController::class, 'handleFacebookCallback']);
  Route::get('/login/google/callback', [UserController::class, 'handleGoogleCallback']);
});

Route::prefix('/user')->middleware(['guest:web'])->group(function () {
  Route::get('/login/facebook', [UserController::class, 'redirectToFacebook'])->name('user.facebook_login');

  Route::get('/login/google', [UserController::class, 'redirectToGoogle'])->name('user.google_login');

  // user redirect to login page route
  Route::get('/login', [UserController::class, 'login'])->name('user.login')->middleware('language');

  // user login submit route
  Route::post('/login_submit', [UserController::class, 'loginSubmit'])->name('user.login_submit');

  // user forget password route
  Route::get('/forget_password', [UserController::class, 'forgetPassword'])->name('user.forget_password')->middleware('language');

  // send mail to user for forget password route
  Route::post('/mail_for_forget_password', [UserController::class, 'sendMail'])->name('user.mail_for_forget_password');

  // reset password route
  Route::get('/reset_password/{code}', [UserController::class, 'resetPassword'])->middleware('language');

  // user reset password submit route
  Route::post('/reset_password_submit', [UserController::class, 'resetPasswordSubmit'])->name('user.reset_password_submit');

  // user redirect to signup page route
  Route::get('/signup', [UserController::class, 'signup'])->name('user.signup')->middleware('language');

  // user signup submit route
  Route::post('/signup_submit', [UserController::class, 'signupSubmit'])->name('user.signup_submit');

  // signup verify route
  Route::get('/signup_verify/{token}', [UserController::class, 'signupVerify']);
});

Route::prefix('/user')->middleware(['auth:web', 'language', 'userstatus'])->group(function () {
  // user redirect to dashboard route
  Route::get('/dashboard', [UserController::class, 'redirectToDashboard'])->name('user.dashboard');

  // all room bookings of user route
  Route::get('/room_bookings', [UserController::class, 'roomBookings'])->name('user.room_bookings');

  // room booking details route
  Route::get('/room_booking_details/{id}', [UserController::class, 'roomBookingDetails'])->name('user.room_booking_details');

  // all package bookings of user route
  Route::get('/package_bookings', [UserController::class, 'packageBookings'])->name('user.package_bookings');

  // package booking details route
  Route::get('/package_booking_details/{id}', [UserController::class, 'packageBookingDetails'])->name('user.package_booking_details');

  // edit profile route
  Route::get('/edit_profile', [UserController::class, 'editProfile'])->name('user.edit_profile');

  // update profile route
  Route::post('/update_profile', [UserController::class, 'updateProfile'])->name('user.update_profile')->withoutMiddleware('language');

  // change password route
  Route::get('/change_password', [UserController::class, 'changePassword'])->name('user.change_password');

  // update password route
  Route::post('/update_password', [UserController::class, 'updatePassword'])->name('user.update_password')->withoutMiddleware('language');

  // user logout attempt route
  Route::get('/logout', [UserController::class, 'logoutSubmit'])->name('user.logout')->withoutMiddleware('language');
});

/*
|--------------------------------------------------------------------------
| BackEnd Routes
|--------------------------------------------------------------------------
*/

// laravel filemanager routes
Route::prefix('/laravel-filemanager')->middleware(['web', 'auth:admin', 'lfm.path'])->group(function () {
  \UniSharp\LaravelFilemanager\Lfm::routes();

  Route::post('/summernote_upload', [SummernoteController::class, 'uploadFileManager'])->name('lfm.summernote_upload');
});


Route::prefix('/admin')->middleware('guest:admin')->group(function () {

  // admin redirect to login page route
  Route::get('/', [AdminController::class, 'login'])->name('admin.login');

  // admin login attempt route
  Route::post('/auth', [AdminController::class, 'authentication'])->name('admin.auth');

  // admin forget password route
  Route::get('/forget_password', [AdminController::class, 'forgetPassword'])->name('admin.forget_password');

  // send mail to admin for forget password route
  Route::post('/mail_for_forget_password', [AdminController::class, 'sendMail'])->name('admin.mail_for_forget_password');
});


Route::prefix('/admin')->middleware(['auth:admin', 'lfm.path'])->group(function () {
  Route::get('/rtlcheck/{langid}', [LanguageController::class, 'rtlcheck'])->name('admin.rtlcheck');

  // admin redirect to dashboard route
  Route::get('/change-theme', [AdminController::class, 'changeTheme'])->name('admin.theme.change');

  // admin redirect to dashboard route
  Route::get('/dashboard', [AdminController::class, 'redirectToDashboard'])->name('admin.dashboard');

  // Summernote image upload
  Route::post('/summernote/upload', [SummernoteController::class, 'upload'])->name('admin.summernote.upload');

  // admin profile settings route start
  Route::get('/edit_profile', [AdminController::class, 'editProfile'])->name('admin.edit_profile');

  Route::post('/update_profile', [AdminController::class, 'updateProfile'])->name('admin.update_profile');

  Route::get('/change_password', [AdminController::class, 'changePassword'])->name('admin.change_password');

  Route::post('/update_password', [AdminController::class, 'updatePassword'])->name('admin.update_password');
  // admin profile settings route end


  // admin logout attempt route
  Route::get('/logout', [AdminController::class, 'logout'])->name('admin.logout');


  // theme version route
  Route::group(['middleware' => 'checkpermission:Theme & Home'], function () {
    Route::get('/theme/version', [BasicSettingsController::class, 'themeVersion'])->name('admin.theme.version');

    Route::post('/theme/update_version', [BasicSettingsController::class, 'updateThemeVersion'])->name('admin.theme.update_version');
  });


  Route::group(['middleware' => 'checkpermission:Menu Builder'], function () {
    // Menus Builder Management Routes
    Route::get('/menu-builder', 'App\Http\Controllers\BackEnd\MenuBuilderController@index')->name('admin.menu_builder.index');
    Route::post('/menu-builder/update', 'App\Http\Controllers\BackEnd\MenuBuilderController@update')->name('admin.menu_builder.update');
  });


  // language management route start
  Route::group(['middleware' => 'checkpermission:Language Management'], function () {
    Route::get('/language_management', [LanguageController::class, 'index'])->name('admin.languages');

    Route::post('/language_management/store_language', [LanguageController::class, 'store'])->name('admin.languages.store_language');

    Route::post('/language_management/make_default_language/{id}', [LanguageController::class, 'makeDefault'])->name('admin.languages.make_default_language');

    Route::post('/language_management/update_language', [LanguageController::class, 'update'])->name('admin.languages.update_language');

    Route::get('/language_management/edit_keyword/{id}', [LanguageController::class, 'editKeyword'])->name('admin.languages.edit_keyword');

    Route::post('/language_management/update_keyword/{id}', [LanguageController::class, 'updateKeyword'])->name('admin.languages.update_keyword');

    Route::post('/language_management/delete_language/{id}', [LanguageController::class, 'destroy'])->name('admin.languages.delete_language');
  });
  // language management route end


  // payment gateways management route start
  Route::group(['middleware' => 'checkpermission:Payment Gateways'], function () {
    Route::get('/payment_gateways/online_gateways', [OnlineGatewayController::class, 'onlineGateways'])->name('admin.payment_gateways.online_gateways');

    Route::post('/payment_gateways/update_paypal_info', [OnlineGatewayController::class, 'updatePayPalInfo'])->name('admin.payment_gateways.update_paypal_info');

    Route::post('/payment_gateways/update_stripe_info', [OnlineGatewayController::class, 'updateStripeInfo'])->name('admin.payment_gateways.update_stripe_info');

    Route::post('/payment_gateways/update_instamojo_info', [OnlineGatewayController::class, 'updateInstamojoInfo'])->name('admin.payment_gateways.update_instamojo_info');

    Route::post('/payment_gateways/update_paystack_info', [OnlineGatewayController::class, 'updatePaystackInfo'])->name('admin.payment_gateways.update_paystack_info');

    Route::post('/payment_gateways/update_flutterwave_info', [OnlineGatewayController::class, 'updateFlutterwaveInfo'])->name('admin.payment_gateways.update_flutterwave_info');

    Route::post('/payment_gateways/update_razorpay_info', [OnlineGatewayController::class, 'updateRazorpayInfo'])->name('admin.payment_gateways.update_razorpay_info');

    Route::post('/payment_gateways/update_mercadopago_info', [OnlineGatewayController::class, 'updateMercadoPagoInfo'])->name('admin.payment_gateways.update_mercadopago_info');

    Route::post('/payment_gateways/update_mollie_info', [OnlineGatewayController::class, 'updateMollieInfo'])->name('admin.payment_gateways.update_mollie_info');

    Route::post('/payment_gateways/update_paytm_info', [OnlineGatewayController::class, 'updatePaytmInfo'])->name('admin.payment_gateways.update_paytm_info');

    Route::get('/payment_gateways/offline_gateways', [OfflineGatewayController::class, 'index'])->name('admin.payment_gateways.offline_gateways');

    Route::post('/payment_gateways/store_offline_gateway', [OfflineGatewayController::class, 'store'])->name('admin.payment_gateways.store_offline_gateway');

    Route::post('/payment_gateways/update_room_booking_status', [OfflineGatewayController::class, 'updateRoomBookingStatus'])->name('admin.payment_gateways.update_room_booking_status');

    Route::post('/payment_gateways/update_offline_gateway', [OfflineGatewayController::class, 'update'])->name('admin.payment_gateways.update_offline_gateway');

    Route::post('/payment_gateways/delete_offline_gateway', [OfflineGatewayController::class, 'delete'])->name('admin.payment_gateways.delete_offline_gateway');
  });
  // payment gateways management route end


  Route::group(['middleware' => 'checkpermission:Settings'], function () {
    // basic settings favicon route
    Route::get('/basic_settings/favicon', [BasicSettingsController::class, 'favicon'])->name('admin.basic_settings.favicon');

    Route::post('/basic_settings/update_favicon', [BasicSettingsController::class, 'updateFavicon'])->name('admin.basic_settings.update_favicon');

    // basic settings logo route
    Route::get('/basic_settings/logo', [BasicSettingsController::class, 'logo'])->name('admin.basic_settings.logo');
    Route::post('/basic_settings/update_logo', [BasicSettingsController::class, 'updateLogo'])->name('admin.basic_settings.update_logo');


    // Admin Preloader Routes
    Route::get('/preloader', [BasicSettingsController::class, 'preloader'])->name('admin.preloader');
    Route::post('/preloader/post', [BasicSettingsController::class, 'updatepreloader'])->name('admin.preloader.update');


    // basic settings information route
    Route::get('/basic_settings/information', [BasicSettingsController::class, 'information'])->name('admin.basic_settings.information');

    Route::post('/basic_settings/update_info', [BasicSettingsController::class, 'updateInfo'])->name('admin.basic_settings.update_info');

    // basic settings currency route
    Route::get('/basic_settings/currency', [BasicSettingsController::class, 'currency'])->name('admin.basic_settings.currency');

    Route::post('/basic_settings/update_currency', [BasicSettingsController::class, 'updateCurrency'])->name('admin.basic_settings.update_currency');

    // basic settings appearance route
    Route::get('/basic_settings/appearance', [BasicSettingsController::class, 'appearance'])->name('admin.basic_settings.appearance');

    Route::post('/basic_settings/update_appearance', [BasicSettingsController::class, 'updateAppearance'])->name('admin.basic_settings.update_appearance');

    // basic settings mail route start
    Route::get('/basic_settings/mail_from_admin', [BasicSettingsController::class, 'mailFromAdmin'])->name('admin.basic_settings.mail_from_admin');

    Route::post('/basic_settings/update_mail_from_admin', [BasicSettingsController::class, 'updateMailFromAdmin'])->name('admin.basic_settings.update_mail_from_admin');

    Route::get('/basic_settings/mail_to_admin', [BasicSettingsController::class, 'mailToAdmin'])->name('admin.basic_settings.mail_to_admin');

    Route::post('/basic_settings/update_mail_to_admin', [BasicSettingsController::class, 'updateMailToAdmin'])->name('admin.basic_settings.update_mail_to_admin');

    // Admin File Manager Routes
    Route::get('/file-manager', [BasicSettingsController::class, 'fileManager'])->name('admin.file-manager');

    Route::get('/basic_settings/mail_templates', [MailTemplateController::class, 'mailTemplates'])->name('admin.basic_settings.mail_templates');

    Route::get('/basic_settings/edit_mail_template/{id}', [MailTemplateController::class, 'editMailTemplate'])->name('admin.basic_settings.edit_mail_template');

    Route::post('/basic_settings/update_mail_template/{id}', [MailTemplateController::class, 'updateMailTemplate'])->name('admin.basic_settings.update_mail_template');
    // basic settings mail route end

    // basic settings social-links route start
    Route::get('/basic_settings/social_links', [SocialLinkController::class, 'socialLinks'])->name('admin.basic_settings.social_links');

    Route::post('/basic_settings/store_social_link', [SocialLinkController::class, 'storeSocialLink'])->name('admin.basic_settings.store_social_link');

    Route::get('/basic_settings/edit_social_link/{id}', [SocialLinkController::class, 'editSocialLink'])->name('admin.basic_settings.edit_social_link');

    Route::post('/basic_settings/update_social_link', [SocialLinkController::class, 'updateSocialLink'])->name('admin.basic_settings.update_social_link');

    Route::post('/basic_settings/delete_social_link', [SocialLinkController::class, 'deleteSocialLink'])->name('admin.basic_settings.delete_social_link');
    // basic settings social-links route end

    // basic settings breadcrumb route
    Route::get('/basic_settings/breadcrumb', [BasicSettingsController::class, 'breadcrumb'])->name('admin.basic_settings.breadcrumb');

    Route::post('/basic_settings/update_breadcrumb', [BasicSettingsController::class, 'updateBreadcrumb'])->name('admin.basic_settings.update_breadcrumb');

    // basic settings page-headings route
    Route::get('/basic_settings/page_headings', [PageHeadingController::class, 'pageHeadings'])->name('admin.basic_settings.page_headings');

    Route::post('/basic_settings/update_page_headings', [PageHeadingController::class, 'updatePageHeadings'])->name('admin.basic_settings.update_page_headings');

    // basic settings scripts route
    Route::get('/basic_settings/scripts', [BasicSettingsController::class, 'scripts'])->name('admin.basic_settings.scripts');

    Route::post('/basic_settings/update_script', [BasicSettingsController::class, 'updateScript'])->name('admin.basic_settings.update_script');

    // basic settings seo route
    Route::get('/basic_settings/seo', [SEOController::class, 'seo'])->name('admin.basic_settings.seo');

    Route::post('/basic_settings/update_seo_informations', [SEOController::class, 'updateSEO'])->name('admin.basic_settings.update_seo_informations');

    // basic settings maintenance-mode route
    Route::get('/basic_settings/maintenance_mode', [BasicSettingsController::class, 'maintenanceMode'])->name('admin.basic_settings.maintenance_mode');

    Route::post('/basic_settings/update_maintenance', [BasicSettingsController::class, 'updateMaintenance'])->name('admin.basic_settings.update_maintenance');

    // basic settings cookie-alert route
    Route::get('/basic_settings/cookie_alert', [CookieAlertController::class, 'cookieAlert'])->name('admin.basic_settings.cookie_alert');

    Route::post('/basic_settings/update_cookie_alert/{language}', [CookieAlertController::class, 'updateCookieAlert'])->name('admin.basic_settings.update_cookie_alert');

    // basic settings footer-logo route
    Route::get('/basic_settings/footer_logo', [BasicSettingsController::class, 'footerLogo'])->name('admin.basic_settings.footer_logo');

    Route::post('/basic_settings/update_footer_logo', [BasicSettingsController::class, 'updateFooterLogo'])->name('admin.basic_settings.update_footer_logo');
  });


  Route::group(['middleware' => 'checkpermission:Home Page Sections'], function () {
    // home page hero-section static-version route
    Route::get('/home_page/hero/static_version', [HeroStaticController::class, 'staticVersion'])->name('admin.home_page.hero.static_version');

    Route::post('/home_page/hero/static_version/update_static_info/{language}', [HeroStaticController::class, 'updateStaticInfo'])->name('admin.home_page.hero.update_static_info');

    // home page hero-section slider-version route start
    Route::get('/home_page/hero/slider_version', [HeroSliderController::class, 'sliderVersion'])->name('admin.home_page.hero.slider_version');

    Route::get('/home_page/hero/slider_version/create_slider', [HeroSliderController::class, 'createSlider'])->name('admin.home_page.hero.create_slider');

    Route::post('/home_page/hero/slider_version/store_slider_info/{language}', [HeroSliderController::class, 'storeSliderInfo'])->name('admin.home_page.hero.store_slider_info');

    Route::get('/home_page/hero/slider_version/edit_slider/{id}', [HeroSliderController::class, 'editSlider'])->name('admin.home_page.hero.edit_slider');

    Route::post('/home_page/hero/slider_version/update_slider_info/{id}', [HeroSliderController::class, 'updateSliderInfo'])->name('admin.home_page.hero.update_slider_info');

    Route::post('/home_page/hero/slider_version/delete_slider', [HeroSliderController::class, 'deleteSlider'])->name('admin.home_page.hero.delete_slider');
    // home page hero-section slider-version route end

    // home page hero-section video-version route
    Route::get('/home_page/hero/video_version', [HeroVideoController::class, 'videoVersion'])->name('admin.home_page.hero.video_version');

    Route::post('/home_page/hero/video_version/update_video_info', [HeroVideoController::class, 'updateVideoInfo'])->name('admin.home_page.hero.update_video_info');

    // home page intro-section route start
    Route::get('/home_page/intro_section', [IntroSectionController::class, 'introSection'])->name('admin.home_page.intro_section');

    Route::post('/home_page/update_intro_section/{language}', [IntroSectionController::class, 'updateIntroInfo'])->name('admin.home_page.update_intro_section');

    Route::get('/home_page/intro_section/create_count_info', [IntroSectionController::class, 'createCountInfo'])->name('admin.home_page.intro_section.create_count_info');

    Route::post('/home_page/intro_section/store_count_info/{language}', [IntroSectionController::class, 'storeCountInfo'])->name('admin.home_page.intro_section.store_count_info');

    Route::get('/home_page/intro_section/edit_count_info/{id}', [IntroSectionController::class, 'editCountInfo'])->name('admin.home_page.intro_section.edit_count_info');

    Route::post('/home_page/intro_section/update_count_info/{id}', [IntroSectionController::class, 'updateCountInfo'])->name('admin.home_page.intro_section.update_count_info');

    Route::post('/home_page/intro_section/delete_count_info', [IntroSectionController::class, 'deleteCountInfo'])->name('admin.home_page.intro_section.delete_count_info');
    // home page intro-section route end

    // home page section-heading route start
    Route::get('/home_page/room_section', [SectionHeadingController::class, 'roomSection'])->name('admin.home_page.room_section');

    Route::post('/home_page/update_room_section/{language}', [SectionHeadingController::class, 'updateRoomSection'])->name('admin.home_page.update_room_section');

    Route::get('/home_page/service_section', [SectionHeadingController::class, 'serviceSection'])->name('admin.home_page.service_section');

    Route::post('/home_page/update_service_section/{language}', [SectionHeadingController::class, 'updateServiceSection'])->name('admin.home_page.update_service_section');

    Route::get('/home_page/booking_section', [SectionHeadingController::class, 'bookingSection'])->name('admin.home_page.booking_section');

    Route::post('/home_page/update_booking_section/{language}', [SectionHeadingController::class, 'updateBookingSection'])->name('admin.home_page.update_booking_section');

    Route::get('/home_page/package_section', [SectionHeadingController::class, 'packageSection'])->name('admin.home_page.package_section');

    Route::post('/home_page/update_package_section/{language}', [SectionHeadingController::class, 'updatePackageSection'])->name('admin.home_page.update_package_section');

    Route::get('/home_page/facility_section', [SectionHeadingController::class, 'facilitySection'])->name('admin.home_page.facility_section');

    Route::post('/home_page/update_facility_section/{language}', [SectionHeadingController::class, 'updateFacilitySection'])->name('admin.home_page.update_facility_section');
    // home page section-heading route end

    // home page facility-section->facilities route start
    Route::get('/home_page/facility_section/create_facility', [FacilityController::class, 'createFacility'])->name('admin.home_page.facility_section.create_facility');

    Route::post('/home_page/facility_section/store_facility/{language}', [FacilityController::class, 'storeFacility'])->name('admin.home_page.facility_section.store_facility');

    Route::get('/home_page/facility_section/edit_facility/{id}', [FacilityController::class, 'editFacility'])->name('admin.home_page.facility_section.edit_facility');

    Route::post('/home_page/facility_section/update_facility/{id}', [FacilityController::class, 'updateFacility'])->name('admin.home_page.facility_section.update_facility');

    Route::post('/home_page/facility_section/delete_facility', [FacilityController::class, 'deleteFacility'])->name('admin.home_page.facility_section.delete_facility');
    // home page facility-section->facilities route end

    // home page section-heading route start
    Route::get('/home_page/testimonial_section', [SectionHeadingController::class, 'testimonialSection'])->name('admin.home_page.testimonial_section');

    Route::post('/home_page/update_testimonial_section/{language}', [SectionHeadingController::class, 'updateTestimonialSection'])->name('admin.home_page.update_testimonial_section');
    // home page section-heading route end

    // home page testimonial-section->testimonials route start
    Route::get('/home_page/testimonial_section/create_testimonial', [TestimonialController::class, 'createTestimonial'])->name('admin.home_page.testimonial_section.create_testimonial');

    Route::post('/home_page/testimonial_section/store_testimonial/{language}', [TestimonialController::class, 'storeTestimonial'])->name('admin.home_page.testimonial_section.store_testimonial');

    Route::get('/home_page/testimonial_section/edit_testimonial/{id}', [TestimonialController::class, 'editTestimonial'])->name('admin.home_page.testimonial_section.edit_testimonial');

    Route::post('/home_page/testimonial_section/update_testimonial/{id}', [TestimonialController::class, 'updateTestimonial'])->name('admin.home_page.testimonial_section.update_testimonial');

    Route::post('/home_page/testimonial_section/delete_testimonial', [TestimonialController::class, 'deleteTestimonial'])->name('admin.home_page.testimonial_section.delete_testimonial');
    // home page testimonial-section->testimonials route end

    // home page brand-section route start
    Route::get('/home_page/brand_section', [BrandSectionController::class, 'brandSection'])->name('admin.home_page.brand_section');

    Route::post('/home_page/brand_section/store_brand/{language}', [BrandSectionController::class, 'storeBrand'])->name('admin.home_page.brand_section.store_brand');

    Route::post('/home_page/brand_section/update_brand', [BrandSectionController::class, 'updateBrand'])->name('admin.home_page.brand_section.update_brand');

    Route::post('/home_page/brand_section/delete_brand', [BrandSectionController::class, 'deleteBrand'])->name('admin.home_page.brand_section.delete_brand');
    // home page brand-section route end

    // home page section-heading route start
    Route::get('/home_page/faq_section', [SectionHeadingController::class, 'faqSection'])->name('admin.home_page.faq_section');

    Route::post('/home_page/update_faq_section/{language}', [SectionHeadingController::class, 'updateFAQSection'])->name('admin.home_page.update_faq_section');

    Route::get('/home_page/blog_section', [SectionHeadingController::class, 'blogSection'])->name('admin.home_page.blog_section');

    Route::post('/home_page/update_blog_section/{language}', [SectionHeadingController::class, 'updateBlogSection'])->name('admin.home_page.update_blog_section');


    // Admin Section Customization Routes
    Route::get('/sections', [SectionsController::class, 'sections'])->name('admin.sections.index');
    Route::post('/sections/update', [SectionsController::class, 'updatesections'])->name('admin.sections.update');
  });


  // rooms management route start
  Route::group(['middleware' => 'checkpermission:Rooms Management'], function () {
    Route::get('/rooms_management/settings', [AdminRoomController::class, 'settings'])->name('admin.rooms_management.settings');

    Route::post('/rooms_management/update_settings', [AdminRoomController::class, 'updateSettings'])->name('admin.rooms_management.update_settings');

    Route::get('/rooms_management/coupons', [AdminRoomController::class, 'coupons'])->name('admin.rooms_management.coupons');

    Route::post('/rooms_management/store-coupon', [AdminRoomController::class, 'storeCoupon'])->name('admin.rooms_management.store_coupon');

    Route::post('/rooms_management/update-coupon', [AdminRoomController::class, 'updateCoupon'])->name('admin.rooms_management.update_coupon');

    Route::post('/rooms_management/delete-coupon/{id}', [AdminRoomController::class, 'destroyCoupon'])->name('admin.rooms_management.delete_coupon');

    Route::get('/rooms_management/amenities', [AdminRoomController::class, 'amenities'])->name('admin.rooms_management.amenities');

    Route::post('/rooms_management/store_amenity/{language}', [AdminRoomController::class, 'storeAmenity'])->name('admin.rooms_management.store_amenity');

    Route::post('/rooms_management/update_amenity', [AdminRoomController::class, 'updateAmenity'])->name('admin.rooms_management.update_amenity');

    Route::post('/rooms_management/delete_amenity', [AdminRoomController::class, 'deleteAmenity'])->name('admin.rooms_management.delete_amenity');

    Route::post('/rooms_management/bulk_delete_amenity', [AdminRoomController::class, 'bulkDeleteAmenity'])->name('admin.rooms_management.bulk_delete_amenity');

    Route::get('/rooms_management/categories', [AdminRoomController::class, 'categories'])->name('admin.rooms_management.categories');

    Route::post('/rooms_management/store_category/{language}', [AdminRoomController::class, 'storeCategory'])->name('admin.rooms_management.store_category');

    Route::post('/rooms_management/update_category', [AdminRoomController::class, 'updateCategory'])->name('admin.rooms_management.update_category');

    Route::post('/rooms_management/delete_category', [AdminRoomController::class, 'deleteCategory'])->name('admin.rooms_management.delete_category');

    Route::post('/rooms_management/bulk_delete_category', [AdminRoomController::class, 'bulkDeleteCategory'])->name('admin.rooms_management.bulk_delete_category');

    Route::get('/rooms_management/rooms', [AdminRoomController::class, 'rooms'])->name('admin.rooms_management.rooms');

    Route::get('/rooms_management/create_room', [AdminRoomController::class, 'createRoom'])->name('admin.rooms_management.create_room');

    Route::post('/rooms_management/store_room', [AdminRoomController::class, 'storeRoom'])->name('admin.rooms_management.store_room');

    Route::post('/rooms_management/update_featured_room', [AdminRoomController::class, 'updateFeaturedRoom'])->name('admin.rooms_management.update_featured_room');

    Route::get('/rooms_management/edit_room/{id}', [AdminRoomController::class, 'editRoom'])->name('admin.rooms_management.edit_room');

    Route::get('/rooms_management/slider_images/{id}', [AdminRoomController::class, 'getSliderImages']);

    Route::post('/rooms_management/update_room/{id}', [AdminRoomController::class, 'updateRoom'])->name('admin.rooms_management.update_room');

    Route::post('/rooms_management/delete_room', [AdminRoomController::class, 'deleteRoom'])->name('admin.rooms_management.delete_room');

    Route::post('/rooms_management/bulk_delete_room', [AdminRoomController::class, 'bulkDeleteRoom'])->name('admin.rooms_management.bulk_delete_room');
  });
  // rooms management route end


  // Room Bookings Routes
  Route::group(['middleware' => 'checkpermission:Room Bookings'], function () {
    Route::get('/room_bookings/all_bookings', [AdminRoomController::class, 'bookings'])->name('admin.room_bookings.all_bookings');

    Route::get('/room_bookings/paid_bookings', [AdminRoomController::class, 'bookings'])->name('admin.room_bookings.paid_bookings');

    Route::get('/room_bookings/unpaid_bookings', [AdminRoomController::class, 'bookings'])->name('admin.room_bookings.unpaid_bookings');

    Route::post('/room_bookings/update_payment_status', [AdminRoomController::class, 'updatePaymentStatus'])->name('admin.room_bookings.update_payment_status');

    Route::get('/room_bookings/booking_details_and_edit/{id}', [AdminRoomController::class, 'editBookingDetails'])->name('admin.room_bookings.booking_details_and_edit');

    Route::post('/room_bookings/update_booking', [AdminRoomController::class, 'updateBooking'])->name('admin.room_bookings.update_booking');

    Route::post('/room_bookings/send_mail', [AdminRoomController::class, 'sendMail'])->name('admin.room_bookings.send_mail');

    Route::post('/room_bookings/delete_booking/{id}', [AdminRoomController::class, 'deleteBooking'])->name('admin.room_bookings.delete_booking');

    Route::post('/room_bookings/bulk_delete_booking', [AdminRoomController::class, 'bulkDeleteBooking'])->name('admin.room_bookings.bulk_delete_booking');

    Route::get('/room_bookings/get_booked_dates', [AdminRoomController::class, 'bookedDates'])->name('admin.room_bookings.get_booked_dates');

    Route::get('/room_bookings/booking_form', [AdminRoomController::class, 'bookingForm'])->name('admin.room_bookings.booking_form');

    Route::post('/room_bookings/make_booking', [AdminRoomController::class, 'makeBooking'])->name('admin.room_bookings.make_booking');
  });


  // services management route start
  Route::group(['middleware' => 'checkpermission:Services Management'], function () {
    Route::get('/services_management', [AdminServiceController::class, 'services'])->name('admin.services_management');

    Route::get('/services_management/create_service', [AdminServiceController::class, 'createService'])->name('admin.services_management.create_service');

    Route::post('/services_management/store_service', [AdminServiceController::class, 'storeService'])->name('admin.services_management.store_service');

    Route::post('/services_management/update_featured_service', [AdminServiceController::class, 'updateFeaturedService'])->name('admin.services_management.update_featured_service');

    Route::get('/services_management/edit_service/{id}', [AdminServiceController::class, 'editService'])->name('admin.services_management.edit_service');

    Route::post('/services_management/update_service/{id}', [AdminServiceController::class, 'updateService'])->name('admin.services_management.update_service');

    Route::post('/services_management/delete_service', [AdminServiceController::class, 'deleteService'])->name('admin.services_management.delete_service');

    Route::post('/services_management/bulk_delete_service', [AdminServiceController::class, 'bulkDeleteService'])->name('admin.services_management.bulk_delete_service');
  });
  // services management route end


  // custom pages route start
  Route::group(['middleware' => 'checkpermission:Custom Pages'], function () {
    Route::get('/pages', 'App\Http\Controllers\BackEnd\PageController@index')->name('admin.page.index');
    Route::get('/page/create', 'App\Http\Controllers\BackEnd\PageController@create')->name('admin.page.create');
    Route::post('/page/store', 'App\Http\Controllers\BackEnd\PageController@store')->name('admin.page.store');
    Route::get('/page/{menuID}/edit', 'App\Http\Controllers\BackEnd\PageController@edit')->name('admin.page.edit');
    Route::post('/page/update', 'App\Http\Controllers\BackEnd\PageController@update')->name('admin.page.update');
    Route::post('/page/delete', 'App\Http\Controllers\BackEnd\PageController@delete')->name('admin.page.delete');
    Route::post('/page/bulk-delete', 'App\Http\Controllers\BackEnd\PageController@bulkDelete')->name('admin.page.bulk.delete');
  });
  // custom pages route end


  // blogs management route start
  Route::group(['middleware' => 'checkpermission:Blogs Management'], function () {
    Route::get('/blogs_management/categories', [AdminBlogController::class, 'blogCategories'])->name('admin.blogs_management.categories');

    Route::post('/blogs_management/store_category/{language}', [AdminBlogController::class, 'storeCategory'])->name('admin.blogs_management.store_category');

    Route::post('/blogs_management/update_category', [AdminBlogController::class, 'updateCategory'])->name('admin.blogs_management.update_category');

    Route::post('/blogs_management/delete_category', [AdminBlogController::class, 'deleteCategory'])->name('admin.blogs_management.delete_category');

    Route::post('/blogs_management/bulk_delete_category', [AdminBlogController::class, 'bulkDeleteCategory'])->name('admin.blogs_management.bulk_delete_category');

    Route::get('/blogs_management/blogs', [AdminBlogController::class, 'blogs'])->name('admin.blogs_management.blogs');

    Route::get('/blogs_management/create_blog', [AdminBlogController::class, 'createBlog'])->name('admin.blogs_management.create_blog');

    Route::post('/blogs_management/store_blog', [AdminBlogController::class, 'storeBlog'])->name('admin.blogs_management.store_blog');

    Route::get('/blogs_management/edit_blog/{id}', [AdminBlogController::class, 'editBlog'])->name('admin.blogs_management.edit_blog');

    Route::post('/blogs_management/update_blog/{id}', [AdminBlogController::class, 'updateBlog'])->name('admin.blogs_management.update_blog');

    Route::post('/blogs_management/delete_blog', [AdminBlogController::class, 'deleteBlog'])->name('admin.blogs_management.delete_blog');

    Route::post('/blogs_management/bulk_delete_blog', [AdminBlogController::class, 'bulkDeleteBlog'])->name('admin.blogs_management.bulk_delete_blog');
  });
  // blogs management route end


  // gallery management route start
  Route::group(['middleware' => 'checkpermission:Gallery Management'], function () {
    Route::get('/gallery_management/categories', [AdminGalleryController::class, 'categories'])->name('admin.gallery_management.categories');

    Route::post('/gallery_management/store_category/{language}', [AdminGalleryController::class, 'storeCategory'])->name('admin.gallery_management.store_category');

    Route::post('/gallery_management/update_category', [AdminGalleryController::class, 'updateCategory'])->name('admin.gallery_management.update_category');

    Route::post('/gallery_management/delete_category', [AdminGalleryController::class, 'deleteCategory'])->name('admin.gallery_management.delete_category');

    Route::post('/gallery_management/bulk_delete_category', [AdminGalleryController::class, 'bulkDeleteCategory'])->name('admin.gallery_management.bulk_delete_category');

    Route::get('/gallery_management/images', [AdminGalleryController::class, 'index'])->name('admin.gallery_management.images');

    Route::post('/gallery_management/store_gallery_info/{language}', [AdminGalleryController::class, 'storeInfo'])->name('admin.gallery_management.store_gallery_info');

    Route::post('/gallery_management/update_gallery_info', [AdminGalleryController::class, 'updateInfo'])->name('admin.gallery_management.update_gallery_info');

    Route::post('/gallery_management/delete_gallery_info', [AdminGalleryController::class, 'deleteInfo'])->name('admin.gallery_management.delete_gallery_info');

    Route::post('/gallery_management/bulk_delete_gallery_info', [AdminGalleryController::class, 'bulkDeleteInfo'])->name('admin.gallery_management.bulk_delete_gallery_info');
  });
  // gallery management route end


  // faq management route start
  Route::group(['middleware' => 'checkpermission:FAQ Management'], function () {
    Route::get('/faq_management', [AdminFAQController::class, 'index'])->name('admin.faq_management');

    Route::post('/faq_management/store_faq/{language}', [AdminFAQController::class, 'store'])->name('admin.faq_management.store_faq');

    Route::post('/faq_management/update_faq', [AdminFAQController::class, 'update'])->name('admin.faq_management.update_faq');

    Route::post('/faq_management/delete_faq', [AdminFAQController::class, 'delete'])->name('admin.faq_management.delete_faq');

    Route::post('/faq_management/bulk_delete_faq', [AdminFAQController::class, 'bulkDelete'])->name('admin.faq_management.bulk_delete_faq');
  });
  // faq management route end


  // packages management route start
  Route::group(['middleware' => 'checkpermission:Packages Management'], function () {
    Route::get('/packages_management/settings', [AdminPackageController::class, 'settings'])->name('admin.packages_management.settings');

    Route::post('/packages_management/update_settings', [AdminPackageController::class, 'updateSettings'])->name('admin.packages_management.update_settings');

    Route::get('/packages_management/coupons', [AdminPackageController::class, 'coupons'])->name('admin.packages_management.coupons');

    Route::post('/packages_management/store-coupon', [AdminPackageController::class, 'storeCoupon'])->name('admin.packages_management.store_coupon');

    Route::post('/packages_management/update-coupon', [AdminPackageController::class, 'updateCoupon'])->name('admin.packages_management.update_coupon');

    Route::post('/packages_management/delete-coupon/{id}', [AdminPackageController::class, 'destroyCoupon'])->name('admin.packages_management.delete_coupon');

    Route::get('/packages_management/categories', [AdminPackageController::class, 'categories'])->name('admin.packages_management.categories');

    Route::post('/packages_management/store_category/{language}', [AdminPackageController::class, 'storeCategory'])->name('admin.packages_management.store_category');

    Route::post('/packages_management/update_category', [AdminPackageController::class, 'updateCategory'])->name('admin.packages_management.update_category');

    Route::post('/packages_management/delete_category', [AdminPackageController::class, 'deleteCategory'])->name('admin.packages_management.delete_category');

    Route::post('/packages_management/bulk_delete_category', [AdminPackageController::class, 'bulkDeleteCategory'])->name('admin.packages_management.bulk_delete_category');

    Route::get('/packages_management/packages', [AdminPackageController::class, 'packages'])->name('admin.packages_management.packages');

    Route::get('/packages_management/create_package', [AdminPackageController::class, 'createPackage'])->name('admin.packages_management.create_package');

    Route::post('/packages_management/store_package', [AdminPackageController::class, 'storePackage'])->name('admin.packages_management.store_package');

    Route::post('/packages_management/update_featured_package', [AdminPackageController::class, 'updateFeaturedPackage'])->name('admin.packages_management.update_featured_package');

    Route::get('/packages_management/edit_package/{id}', [AdminPackageController::class, 'editPackage'])->name('admin.packages_management.edit_package');

    Route::get('/packages_management/slider_images/{id}', [AdminPackageController::class, 'getSliderImages']);

    Route::post('/packages_management/update_package/{id}', [AdminPackageController::class, 'updatePackage'])->name('admin.packages_management.update_package');

    Route::post('/packages_management/delete_package', [AdminPackageController::class, 'deletePackage'])->name('admin.packages_management.delete_package');

    Route::post('/packages_management/bulk_delete_package', [AdminPackageController::class, 'bulkDeletePackage'])->name('admin.packages_management.bulk_delete_package');

    Route::post('/packages_management/store_location', [AdminPackageController::class, 'storeLocation'])->name('admin.packages_management.store_location');

    Route::get('/packages_management/view_locations/{package_id}', [AdminPackageController::class, 'viewLocations'])->name('admin.packages_management.view_locations');

    Route::post('/packages_management/update_location', [AdminPackageController::class, 'updateLocation'])->name('admin.packages_management.update_location');

    Route::post('/packages_management/delete_location', [AdminPackageController::class, 'deleteLocation'])->name('admin.packages_management.delete_location');

    Route::post('/packages_management/bulk_delete_location', [AdminPackageController::class, 'bulkDeleteLocation'])->name('admin.packages_management.bulk_delete_location');

    Route::post('/packages_management/store_daywise_plan', [AdminPackageController::class, 'storeDaywisePlan'])->name('admin.packages_management.store_daywise_plan');

    Route::post('/packages_management/store_timewise_plan', [AdminPackageController::class, 'storeTimewisePlan'])->name('admin.packages_management.store_timewise_plan');

    Route::get('/packages_management/view_plans/{package_id}', [AdminPackageController::class, 'viewPlans'])->name('admin.packages_management.view_plans');

    Route::post('/packages_management/update_daywise_plan', [AdminPackageController::class, 'updateDaywisePlan'])->name('admin.packages_management.update_daywise_plan');

    Route::post('/packages_management/update_timewise_plan', [AdminPackageController::class, 'updateTimewisePlan'])->name('admin.packages_management.update_timewise_plan');

    Route::post('/packages_management/delete_plan', [AdminPackageController::class, 'deletePlan'])->name('admin.packages_management.delete_plan');

    Route::post('/packages_management/bulk_delete_plan', [AdminPackageController::class, 'bulkDeletePlan'])->name('admin.packages_management.bulk_delete_plan');
  });
  // packages management route end


  // Package Bookings Routes
  Route::group(['middleware' => 'checkpermission:Package Bookings'], function () {
    Route::get('/package_bookings/all_bookings', [AdminPackageController::class, 'bookings'])->name('admin.package_bookings.all_bookings');

    Route::get('/package_bookings/paid_bookings', [AdminPackageController::class, 'bookings'])->name('admin.package_bookings.paid_bookings');

    Route::get('/package_bookings/unpaid_bookings', [AdminPackageController::class, 'bookings'])->name('admin.package_bookings.unpaid_bookings');

    Route::post('/package_bookings/update_payment_status', [AdminPackageController::class, 'updatePaymentStatus'])->name('admin.package_bookings.update_payment_status');

    Route::get('/package_bookings/booking_details/{id}', [AdminPackageController::class, 'bookingDetails'])->name('admin.package_bookings.booking_details');

    Route::post('/package_bookings/send_mail', [AdminPackageController::class, 'sendMail'])->name('admin.package_bookings.send_mail');

    Route::post('/package_bookings/delete_booking/{id}', [AdminPackageController::class, 'deleteBooking'])->name('admin.package_bookings.delete_booking');

    Route::post('/package_bookings/bulk_delete_booking', [AdminPackageController::class, 'bulkDeleteBooking'])->name('admin.package_bookings.bulk_delete_booking');
  });


  // footer route start
  Route::group(['middleware' => 'checkpermission:Footer'], function () {
    Route::get('/footer/text', [FooterController::class, 'footerText'])->name('admin.footer.text');

    Route::post('/footer/update_footer_info/{language}', [FooterController::class, 'updateFooterInfo'])->name('admin.footer.update_footer_info');

    Route::get('/footer/quick_links', [FooterController::class, 'quickLinks'])->name('admin.footer.quick_links');

    Route::post('/footer/store_quick_link/{language}', [FooterController::class, 'storeQuickLink'])->name('admin.footer.store_quick_link');

    Route::post('/footer/update_quick_link', [FooterController::class, 'updateQuickLink'])->name('admin.footer.update_quick_link');

    Route::post('/footer/delete_quick_link', [FooterController::class, 'deleteQuickLink'])->name('admin.footer.delete_quick_link');
  });
  // footer route end


  // Announcement Popup Routes
  Route::group(['middleware' => 'checkpermission:Announcement Popup'], function () {
    Route::get('popups', 'App\Http\Controllers\BackEnd\PopupController@index')->name('admin.popup.index');
    Route::get('popup/types', 'App\Http\Controllers\BackEnd\PopupController@types')->name('admin.popup.types');
    Route::get('popup/{id}/edit', 'App\Http\Controllers\BackEnd\PopupController@edit')->name('admin.popup.edit');
    Route::get('popup/create', 'App\Http\Controllers\BackEnd\PopupController@create')->name('admin.popup.create');
    Route::post('popup/store', 'App\Http\Controllers\BackEnd\PopupController@store')->name('admin.popup.store');;
    Route::post('popup/delete', 'App\Http\Controllers\BackEnd\PopupController@delete')->name('admin.popup.delete');
    Route::post('popup/bulk-delete', 'App\Http\Controllers\BackEnd\PopupController@bulkDelete')->name('admin.popup.bulk.delete');
    Route::post('popup/status', 'App\Http\Controllers\BackEnd\PopupController@status')->name('admin.popup.status');
    Route::post('popup/update', 'App\Http\Controllers\BackEnd\PopupController@update')->name('admin.popup.update');;
  });


  Route::group(['middleware' => 'checkpermission:Users Management'], function () {
    // Admin Subscriber Routes
    Route::get('/subscribers', 'App\Http\Controllers\BackEnd\SubscriberController@index')->name('admin.subscriber.index');
    Route::get('/mailsubscriber', 'App\Http\Controllers\BackEnd\SubscriberController@mailsubscriber')->name('admin.mailsubscriber');
    Route::post('/subscribers/sendmail', 'App\Http\Controllers\BackEnd\SubscriberController@subscsendmail')->name('admin.subscribers.sendmail');
    Route::post('/subscriber/delete', 'App\Http\Controllers\BackEnd\SubscriberController@delete')->name('admin.subscriber.delete');
    Route::post('/subscriber/bulk-delete', 'App\Http\Controllers\BackEnd\SubscriberController@bulkDelete')->name('admin.subscriber.bulk.delete');


    // Register User start
    Route::get('register/users', 'App\Http\Controllers\BackEnd\RegisterUserController@index')->name('admin.register.user');
    Route::post('register/users/ban', 'App\Http\Controllers\BackEnd\RegisterUserController@userban')->name('register.user.ban');
    Route::post('register/users/email', 'App\Http\Controllers\BackEnd\RegisterUserController@emailStatus')->name('register.user.email');
    Route::get('register/user/details/{id}', 'App\Http\Controllers\BackEnd\RegisterUserController@view')->name('register.user.view');
    Route::post('register/user/delete', 'App\Http\Controllers\BackEnd\RegisterUserController@delete')->name('register.user.delete');
    Route::post('register/user/bulk-delete', 'App\Http\Controllers\BackEnd\RegisterUserController@bulkDelete')->name('register.user.bulk.delete');
    Route::get('register/user/{id}/changePassword', 'App\Http\Controllers\BackEnd\RegisterUserController@changePass')->name('register.user.changePass');
    Route::post('register/user/updatePassword', 'App\Http\Controllers\BackEnd\RegisterUserController@updatePassword')->name('register.user.updatePassword');
    //Register User end


    // push notification route
    Route::prefix('/push-notification')->group(function () {
      Route::get('/settings', [PushNotificationController::class, 'settings'])->name('admin.user_management.push_notification.settings');

      Route::post('/update-settings', [PushNotificationController::class, 'updateSettings'])->name('admin.user_management.push_notification.update_settings');

      Route::get('/notification-for-visitors', [PushNotificationController::class, 'writeNotification'])->name('admin.user_management.push_notification.notification_for_visitors');

      Route::post('/send', [PushNotificationController::class, 'sendNotification'])->name('admin.user_management.push_notification.send');
    });
  });


  Route::group(['middleware' => 'checkpermission:Admins Management'], function () {
    // Admin Users Routes
    Route::get('/users', 'App\Http\Controllers\BackEnd\UserController@index')->name('admin.user.index');
    Route::post('/user/upload', 'App\Http\Controllers\BackEnd\UserController@upload')->name('admin.user.upload');
    Route::post('/user/store', 'App\Http\Controllers\BackEnd\UserController@store')->name('admin.user.store');
    Route::get('/user/{id}/edit', 'App\Http\Controllers\BackEnd\UserController@edit')->name('admin.user.edit');
    Route::post('/user/update', 'App\Http\Controllers\BackEnd\UserController@update')->name('admin.user.update');
    Route::post('/user/{id}/uploadUpdate', 'App\Http\Controllers\BackEnd\UserController@uploadUpdate')->name('admin.user.uploadUpdate');
    Route::post('/user/delete', 'App\Http\Controllers\BackEnd\UserController@delete')->name('admin.user.delete');

    // Admin Roles Routes
    Route::get('/roles', 'App\Http\Controllers\BackEnd\RoleController@index')->name('admin.role.index');
    Route::post('/role/store', 'App\Http\Controllers\BackEnd\RoleController@store')->name('admin.role.store');
    Route::post('/role/update', 'App\Http\Controllers\BackEnd\RoleController@update')->name('admin.role.update');
    Route::post('/role/delete', 'App\Http\Controllers\BackEnd\RoleController@delete')->name('admin.role.delete');
    Route::get('role/{id}/permissions/manage', 'App\Http\Controllers\BackEnd\RoleController@managePermissions')->name('admin.role.permissions.manage');
    Route::post('role/permissions/update', 'App\Http\Controllers\BackEnd\RoleController@updatePermissions')->name('admin.role.permissions.update');
  });


  // Sitemap Routes start
  Route::group(['middleware' => 'checkpermission:Sitemap'], function () {
    Route::get('/sitemap', 'App\Http\Controllers\BackEnd\SitemapController@index')->name('admin.sitemap.index');
    Route::post('/sitemap/store', 'App\Http\Controllers\BackEnd\SitemapController@store')->name('admin.sitemap.store');
    Route::get('/sitemap/{id}/update', 'App\Http\Controllers\BackEnd\SitemapController@update')->name('admin.sitemap.update');
    Route::post('/sitemap/{id}/delete', 'App\Http\Controllers\BackEnd\SitemapController@delete')->name('admin.sitemap.delete');
    Route::post('/sitemap/download', 'App\Http\Controllers\BackEnd\SitemapController@download')->name('admin.sitemap.download');
  });
  // Sitemap Routes end


  // Admin Cache Clear Routes
  Route::get('/cache-clear', 'App\Http\Controllers\BackEnd\CacheController@clear')->name('admin.cache.clear');


  // QR Code Builder Routes
  Route::group(['middleware' => 'checkpermission:QR Builder'], function () {
    Route::get('/saved/qrs', 'App\Http\Controllers\BackEnd\QrController@index')->name('admin.qrcode.index');
    Route::post('/saved/qr/delete', 'App\Http\Controllers\BackEnd\QrController@delete')->name('admin.qrcode.delete');
    Route::post('/saved/qr/bulk-delete', 'App\Http\Controllers\BackEnd\QrController@bulkDelete')->name('admin.qrcode.bulk.delete');
    Route::get('/qr-code', 'App\Http\Controllers\BackEnd\QrController@qrCode')->name('admin.qrcode');
    Route::post('/qr-code/generate', 'App\Http\Controllers\BackEnd\QrController@generate')->name('admin.qrcode.generate');
    Route::get('/qr-code/clear', 'App\Http\Controllers\BackEnd\QrController@clear')->name('admin.qrcode.clear');
    Route::post('/qr-code/save', 'App\Http\Controllers\BackEnd\QrController@save')->name('admin.qrcode.save');
  });
});

Route::get('/{slug}', 'App\Http\Controllers\FrontEnd\PageController@dynamicPage')->name('front.dynamicPage')->middleware('language');
