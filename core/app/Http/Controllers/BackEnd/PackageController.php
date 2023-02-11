<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Http\Requests\CouponRequest;
use App\Models\BasicSettings\MailTemplate;
use App\Models\Language;
use App\Models\PackageManagement\Coupon;
use App\Models\PackageManagement\Package;
use App\Models\PackageManagement\PackageBooking;
use App\Models\PackageManagement\PackageCategory;
use App\Models\PackageManagement\PackageContent;
use App\Models\PackageManagement\PackageLocation;
use App\Models\PackageManagement\PackagePlan;
use App\Traits\MiscellaneousTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use PDF;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class PackageController extends Controller
{
  use MiscellaneousTrait;

  public function settings()
  {
    $data = DB::table('basic_settings')
      ->select('package_category_status', 'package_rating_status', 'package_guest_checkout_status')
      ->where('uniqid', 12345)
      ->first();

    return view('backend.packages.settings', ['data' => $data]);
  }

  public function updateSettings(Request $request)
  {
    $rules = [
      'package_category_status' => 'required',
      'package_rating_status' => 'required',
      'package_guest_checkout_status' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    DB::table('basic_settings')->update([
      'package_category_status' => $request->package_category_status,
      'package_rating_status' => $request->package_rating_status,
      'package_guest_checkout_status' => $request->package_guest_checkout_status
    ]);

    $request->session()->flash('success', 'Package settings updated successfully!');

    return redirect()->back();
  }


  public function coupons()
  {
    // get the coupons from db
    $information['coupons'] = Coupon::orderByDesc('id')->get();

    // also, get the currency information from db
    $information['currencyInfo'] = MiscellaneousTrait::getCurrencyInfo();

    $language = Language::where('is_default', 1)->first();

    $packages = Package::all();

    $packages->map(function ($package) use ($language) {
      $package['title'] = $package->packageContent()->where('language_id', $language->id)->pluck('title')->first();
    });

    $information['packages'] = $packages;

    return view('backend.packages.coupons', $information);
  }

  public function storeCoupon(CouponRequest $request)
  {
    $startDate = Carbon::parse($request->start_date);
    $endDate = Carbon::parse($request->end_date);

    if ($request->filled('packages')) {
      $packages = $request->packages;
    }

    Coupon::create($request->except('start_date', 'end_date', 'packages') + [
      'start_date' => date_format($startDate, 'Y-m-d'),
      'end_date' => date_format($endDate, 'Y-m-d'),
      'packages' => isset($packages) ? json_encode($packages) : null
    ]);

    $request->session()->flash('success', 'New coupon added successfully!');

    return 'success';
  }

  public function updateCoupon(CouponRequest $request)
  {
    $startDate = Carbon::parse($request->start_date);
    $endDate = Carbon::parse($request->end_date);

    if ($request->filled('packages')) {
      $packages = $request->packages;
    }

    Coupon::find($request->id)->update($request->except('start_date', 'end_date', 'packages') + [
      'start_date' => date_format($startDate, 'Y-m-d'),
      'end_date' => date_format($endDate, 'Y-m-d'),
      'packages' => isset($packages) ? json_encode($packages) : null
    ]);

    $request->session()->flash('success', 'Coupon updated successfully!');

    return 'success';
  }

  public function destroyCoupon($id)
  {
    Coupon::find($id)->delete();

    return redirect()->back()->with('success', 'Coupon deleted successfully!');
  }


  public function categories(Request $request)
  {
    // first, get the language info from db
    $language = Language::where('code', $request->language)->firstOrFail();
    $information['language'] = $language;

    // then, get the package categories of that language from db
    $information['packageCategories'] = PackageCategory::where('language_id', $language->id)
      ->orderBy('id', 'desc')
      ->paginate(10);

    // also, get all the languages from db
    $information['langs'] = Language::all();

    return view('backend.packages.categories', $information);
  }

  public function storeCategory(Request $request, $language)
  {
    $rules = [
      'name' => 'required',
      'status' => 'required',
      'serial_number' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $lang = Language::where('code', $language)->firstOrFail();

    PackageCategory::create($request->except('language_id') + [
      'language_id' => $lang->id
    ]);

    $request->session()->flash('success', 'New package category added successfully!');

    return 'success';
  }

  public function updateCategory(Request $request)
  {
    $rules = [
      'name' => 'required',
      'status' => 'required',
      'serial_number' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    PackageCategory::findOrFail($request->category_id)->update($request->all());

    $request->session()->flash('success', 'Package category updated successfully!');

    return 'success';
  }

  public function deleteCategory(Request $request)
  {
    $packageCategory = PackageCategory::findOrFail($request->category_id);

    if ($packageCategory->packageContentList()->count() > 0) {
      $request->session()->flash('warning', 'First delete all the packages of this category!');

      return redirect()->back();
    }

    $packageCategory->delete();

    $request->session()->flash('success', 'Package category deleted successfully!');

    return redirect()->back();
  }

  public function bulkDeleteCategory(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      $packageCategory = PackageCategory::findOrFail($id);

      if ($packageCategory->packageContentList()->count() > 0) {
        $request->session()->flash('warning', 'First delete all the packages of those category!');

        /**
         * this 'success' is returning for ajax call.
         * here, by returning the 'success' ajax will show the flash error message
         */
        return 'success';
      }

      $packageCategory->delete();
    }

    $request->session()->flash('success', 'Package categories deleted successfully!');

    /**
     * this 'success' is returning for ajax call.
     * if return == 'success' then ajax will reload the page.
     */
    return 'success';
  }


  public function packages()
  {
    $language = Language::where('is_default', 1)->firstOrFail();
    $information['language'] = $language;

    $information['packageContents'] = PackageContent::with('package')
      ->where('language_id', '=', $language->id)
      ->orderBy('package_id', 'desc')
      ->get();

    $information['currencyInfo'] = MiscellaneousTrait::getCurrencyInfo();

    return view('backend.packages.packages', $information);
  }

  public function createPackage()
  {
    // get all the languages from db
    $information['languages'] = Language::all();

    $information['basicSettings'] = DB::table('basic_settings')
      ->select('package_category_status')
      ->where('uniqid', 12345)
      ->first();

    return view('backend.packages.create_package', $information);
  }

  public function storePackage(Request $request)
  {
    $rules = [
      'number_of_days' => 'required|numeric|min:1',
      'plan_type' => 'required',
      'pricing_type' => 'required',
      'fixed_package_price' => 'required_if:pricing_type,==,fixed',
      'per_person_package_price' => 'required_if:pricing_type,==,per-person'
    ];

    $featuredImgURL = $request->featured_img;
    $sliderImgURLs = $request->filled('slider_imgs') ? explode(',', $request->slider_imgs) : [];

    $allowedExtensions = array('jpg', 'jpeg', 'png', 'svg');
    $featuredImgExt = pathinfo($featuredImgURL, PATHINFO_EXTENSION);

    $sliderImgExts = [];

    if (!empty($sliderImgURLs)) {
      foreach ($sliderImgURLs as $sliderImgURL) {
        $extension = pathinfo($sliderImgURL, PATHINFO_EXTENSION);
        array_push($sliderImgExts, $extension);
      }
    }

    $rules['featured_img'] = [
      'required',
      function ($attribute, $value, $fail) use ($allowedExtensions, $featuredImgExt) {
        if (!in_array($featuredImgExt, $allowedExtensions)) {
          $fail('Only .jpg, .jpeg, .png and .svg file is allowed for featured image.');
        }
      }
    ];

    $rules['slider_imgs'] = [
      'required',
      function ($attribute, $value, $fail) use ($allowedExtensions, $sliderImgExts) {
        if (!empty($sliderImgExts)) {
          foreach ($sliderImgExts as $sliderImgExt) {
            if (!in_array($sliderImgExt, $allowedExtensions)) {
              $fail('Only .jpg, .jpeg, .png and .svg file is allowed for slider image.');
              break;
            }
          }
        }
      }
    ];

    $messages = [
      'featured_img.required' => 'The package\'s featured image is required.',
      'slider_imgs.required' => 'The package\'s slider images is required.'
    ];

    $languages = Language::all();

    $settings = DB::table('basic_settings')->select('package_category_status')
      ->where('uniqid', 12345)
      ->first();

    foreach ($languages as $language) {
      $rules[$language->code . '_title'] = 'required|max:255';

      if ($settings->package_category_status == 1) {
        $rules[$language->code . '_category'] = 'required';
      }

      $rules[$language->code . '_description'] = 'required|min:15';

      $messages[$language->code . '_title.required'] = 'The title field is required for ' . $language->name . ' language';

      $messages[$language->code . '_title.max'] = 'The title field cannot contain more than 255 characters for ' . $language->name . ' language';

      if ($settings->package_category_status == 1) {
        $messages[$language->code . '_category.required'] = 'The category field is required for ' . $language->name . ' language';
      }

      $messages[$language->code . '_description.required'] = 'The description field is required for ' . $language->name . ' language';

      $messages[$language->code . '_description.min'] = 'The description field atleast have 15 characters for ' . $language->name . ' language';
    }

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $package = new Package();

    $sliderImgs = [];
    $sliderDir = './assets/img/packages/slider_images/';

    if (!file_exists($sliderDir)) {
      mkdir($sliderDir, 0777, true);
    }

    foreach ($sliderImgURLs as $sliderImgURL) {
      $ext = pathinfo($sliderImgURL, PATHINFO_EXTENSION);

      // set a name for the slider image and store it to local storage
      $sliderImgName = uniqid() . '.' . $ext;
      copy($sliderImgURL, $sliderDir . $sliderImgName);

      // push image name in array inorder to store those images in db
      array_push($sliderImgs, $sliderImgName);
    }

    $package->slider_imgs = json_encode($sliderImgs);

    // set a name for the featured image and store it to local storage
    $featuredImgName = time() . '.' . $featuredImgExt;
    $featuredDir = './assets/img/packages/';

    if (!file_exists($featuredDir)) {
      mkdir($featuredDir, 0777, true);
    }

    copy($featuredImgURL, $featuredDir . $featuredImgName);

    $package->featured_img = $featuredImgName;
    $package->plan_type = $request->plan_type;
    $package->number_of_days = $request->number_of_days;
    $package->max_persons = $request->max_persons;
    $package->pricing_type = $request->pricing_type;

    if ($request->pricing_type == 'fixed') {
      $package->package_price = $request->fixed_package_price;
    } elseif ($request->pricing_type == 'per-person') {
      $package->package_price = $request->per_person_package_price;
    }

    $package->email = $request->email;
    $package->phone = $request->phone;
    $package->save();

    foreach ($languages as $language) {
      $packageContent = new PackageContent();
      $packageContent->language_id = $language->id;
      $packageContent->package_category_id = $request[$language->code . '_category'];
      $packageContent->package_id = $package->id;
      $packageContent->title = $request[$language->code . '_title'];
      $packageContent->slug = createSlug($request[$language->code . '_title']);
      $packageContent->description = clean($request[$language->code . '_description']);
      $packageContent->meta_keywords = $request[$language->code . '_meta_keywords'];
      $packageContent->meta_description = $request[$language->code . '_meta_description'];
      $packageContent->save();
    }

    $request->session()->flash('success', 'New tour package added successfully!');

    return 'success';
  }

  public function updateFeaturedPackage(Request $request)
  {
    $package = Package::findOrFail($request->packageId);

    if ($request->is_featured == 1) {
      $package->update(['is_featured' => 1]);

      $request->session()->flash('success', 'Package featured successfully!');
    } else {
      $package->update(['is_featured' => 0]);

      $request->session()->flash('success', 'Package Unfeatured successfully!');
    }

    return redirect()->back();
  }

  public function editPackage($id)
  {
    $information['package'] = Package::findOrFail($id);

    // get all the languages from db
    $information['languages'] = Language::all();

    $information['basicSettings'] = DB::table('basic_settings')
      ->select('package_category_status')
      ->where('uniqid', 12345)
      ->first();

    return view('backend.packages.edit_package', $information);
  }

  public function getSliderImages($id)
  {
    $package = Package::findOrFail($id);
    $sliderImages = json_decode($package->slider_imgs);

    $images = [];

    // concatanate slider image with image location
    foreach ($sliderImages as $key => $sliderImage) {
      $data = url('assets/img/packages/slider_images/' . $sliderImage);
      array_push($images, $data);
    }

    return Response::json($images, 200);
  }

  public function updatePackage(Request $request, $id)
  {
    $rules = [
      'number_of_days' => 'required|numeric|min:1',
      'plan_type' => 'required',
      'pricing_type' => 'required',
      'fixed_package_price' => 'required_if:pricing_type,==,fixed',
      'per_person_package_price' => 'required_if:pricing_type,==,per-person'
    ];

    $featuredImgURL = $request->featured_img;
    $sliderImgURLs = $request->filled('slider_imgs') ? explode(',', $request->slider_imgs) : [];

    $allowedExtensions = array('jpg', 'jpeg', 'png', 'svg');
    $featuredImgExt = pathinfo($featuredImgURL, PATHINFO_EXTENSION);

    $sliderImgExts = [];

    if (!empty($sliderImgURLs)) {
      foreach ($sliderImgURLs as $sliderImgURL) {
        $extension = pathinfo($sliderImgURL, PATHINFO_EXTENSION);
        array_push($sliderImgExts, $extension);
      }
    }

    if ($request->filled('featured_img')) {
      $rules['featured_img'] = function ($attribute, $value, $fail) use ($allowedExtensions, $featuredImgExt) {
        if (!in_array($featuredImgExt, $allowedExtensions)) {
          $fail('Only .jpg, .jpeg, .png and .svg file is allowed for featured image.');
        }
      };
    }

    if (!$request->filled('slider_imgs')) {
      $rules['slider_imgs'] = 'required';
    } else {
      $rules['slider_imgs'] = function ($attribute, $value, $fail) use ($allowedExtensions, $sliderImgExts) {
        foreach ($sliderImgExts as $sliderImgExt) {
          if (!in_array($sliderImgExt, $allowedExtensions)) {
            $fail('Only .jpg, .jpeg, .png and .svg file is allowed for slider image.');
            break;
          }
        }
      };
    }

    $messages = [
      'slider_imgs.required' => 'The room\'s slider images field is required.'
    ];

    $languages = Language::all();

    $settings = DB::table('basic_settings')->select('package_category_status')
      ->where('uniqid', 12345)
      ->first();

    foreach ($languages as $language) {
      $rules[$language->code . '_title'] = 'required|max:255';

      if ($settings->package_category_status == 1) {
        $rules[$language->code . '_category'] = 'required';
      }

      $rules[$language->code . '_description'] = 'required|min:15';

      $messages[$language->code . '_title.required'] = 'The title field is required for ' . $language->name . ' language';

      $messages[$language->code . '_title.max'] = 'The title field cannot contain more than 255 characters for ' . $language->name . ' language';

      if ($settings->package_category_status == 1) {
        $messages[$language->code . '_category.required'] = 'The category field is required for ' . $language->name . ' language';
      }

      $messages[$language->code . '_description.required'] = 'The description field is required for ' . $language->name . ' language';

      $messages[$language->code . '_description.min'] = 'The description field atleast have 15 characters for ' . $language->name . ' language';
    }

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $package = Package::findOrFail($id);
    $packageSldImgs = json_decode($package->slider_imgs);

    $sliderImgs = [];
    $sliderDir = './assets/img/packages/slider_images/';

    // first, store the new slider images in local storage
    foreach ($sliderImgURLs as $sliderImgURL) {
      $ext = pathinfo($sliderImgURL, PATHINFO_EXTENSION);

      // set a name for the slider image
      $sliderImgName = uniqid() . '.' . $ext;
      copy($sliderImgURL, $sliderDir . $sliderImgName);

      // push image name in array inorder to store those images in db
      array_push($sliderImgs, $sliderImgName);
    }

    // second, delete the previous slider images from local storage
    if (!empty($packageSldImgs)) {
      foreach ($packageSldImgs as $key => $packageSldImg) {
        if (file_exists('assets/img/packages/slider_images/' . $packageSldImg)) {
          unlink('assets/img/packages/slider_images/' . $packageSldImg);
        }
      }
    }

    if ($request->filled('featured_img')) {
      // first, delete the previous featured image from local storage
      if (
        !is_null($package->featured_img) &&
        file_exists('assets/img/packages/' . $package->featured_img)
      ) {
        unlink('assets/img/packages/' . $package->featured_img);
      }

      // second, set a name for the image and store it to local storage
      $featuredImgName = time() . '.' . $featuredImgExt;
      $featuredDir = './assets/img/packages/';

      copy($featuredImgURL, $featuredDir . $featuredImgName);
    }

    // get the package price that admin has selected
    if ($request->pricing_type == 'negotiable') {
      $amount = null;
    } elseif ($request->pricing_type == 'fixed') {
      $amount = $request->fixed_package_price;
    } elseif ($request->pricing_type == 'per-person') {
      $amount = $request->per_person_package_price;
    }

    $package->update([
      'number_of_days' => $request->number_of_days,
      'slider_imgs' => json_encode($sliderImgs),
      'featured_img' => $request->filled('featured_img') ? $featuredImgName : $package->featured_img,
      'plan_type' => $request->plan_type,
      'max_persons' => $request->max_persons,
      'pricing_type' => $request->pricing_type,
      'package_price' => isset($amount) ? $amount : $package->package_price,
      'email' => $request->email,
      'phone' => $request->phone
    ]);

    foreach ($languages as $language) {
      $packageContent = PackageContent::where('package_id', $id)
        ->where('language_id', $language->id)
        ->first();

      $content = [
        'language_id' => $language->id,
        'package_id' => $package->id,
        'package_category_id' => $request[$language->code . '_category'],
        'title' => $request[$language->code . '_title'],
        'slug' => createSlug($request[$language->code . '_title']),
        'description' => clean($request[$language->code . '_description']),
        'meta_keywords' => $request[$language->code . '_meta_keywords'],
        'meta_description' => $request[$language->code . '_meta_description']
      ];

      if (!empty($packageContent)) {
        $packageContent->update($content);
      } else {
        PackageContent::create($content);
      }
    }

    $request->session()->flash('success', 'Tour package updated successfully!');

    return 'success';
  }

  public function deletePackage(Request $request)
  {
    $package = Package::findOrFail($request->package_id);

    if ($package->packageLocationList()->count() > 0) {
      $request->session()->flash('warning', 'First delete all the locations of this package!');

      return redirect()->back();
    }

    if ($package->packagePlanList()->count() > 0) {
      $request->session()->flash('warning', 'First delete all the plans of this package!');

      return redirect()->back();
    }

    // first, delete all the contents of this package
    $contents = $package->packageContent()->get();

    foreach ($contents as $content) {
      $content->delete();
    }

    // second, delete all the slider images of this package
    if (!is_null($package->slider_imgs)) {
      $images = json_decode($package->slider_imgs);

      foreach ($images as $image) {
        if (file_exists('./assets/img/packages/slider_images/' . $image)) {
          unlink('./assets/img/packages/slider_images/' . $image);
        }
      }
    }

    // third, delete featured image of this package
    if (!is_null($package->featured_img) && file_exists('./assets/img/packages/' . $package->featured_img)) {
      unlink('./assets/img/packages/' . $package->featured_img);
    }

    // finally, delete this package
    $package->delete();

    $request->session()->flash('success', 'Tour package deleted successfully!');

    return redirect()->back();
  }

  public function bulkDeletePackage(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      $package = Package::findOrFail($id);

      if ($package->packageLocationList()->count() > 0) {
        $request->session()->flash('warning', 'First delete all the locations of those packages!');

        /**
         * this 'success' is returning for ajax call.
         * here, by returning the 'success' ajax will show the flash error message
         */
        return 'success';
      }

      if ($package->packagePlanList()->count() > 0) {
        $request->session()->flash('warning', 'First delete all the plans of those packages!');

        /**
         * this 'success' is returning for ajax call.
         * here, by returning the 'success' ajax will show the flash error message
         */
        return 'success';
      }

      // first, delete all the contents of this package
      $contents = $package->packageContent()->get();

      foreach ($contents as $content) {
        $content->delete();
      }

      // second, delete all the slider images of this package
      if (!is_null($package->slider_imgs)) {
        $images = json_decode($package->slider_imgs);

        foreach ($images as $image) {
          if (file_exists('./assets/img/packages/slider_images/' . $image)) {
            unlink('./assets/img/packages/slider_images/' . $image);
          }
        }
      }

      // third, delete featured image of this package
      if (!is_null($package->featured_img) && file_exists('./assets/img/packages/' . $package->featured_img)) {
        unlink('./assets/img/packages/' . $package->featured_img);
      }

      // finally, delete this package
      $package->delete();
    }

    $request->session()->flash('success', 'Tour packages deleted successfully!');

    /**
     * this 'success' is returning for ajax call.
     * if return == 'success' then ajax will reload the page.
     */
    return 'success';
  }


  public function storeLocation(Request $request)
  {
    $rule = [
      'name' => 'required'
    ];

    $validator = Validator::make($request->all(), $rule);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $lang = Language::findOrFail($request->language_id);

    PackageLocation::create($request->except('language_id') + [
      'language_id' => $lang->id
    ]);

    $request->session()->flash('success', 'New location added successfully!');

    return 'success';
  }

  public function viewLocations(Request $request, $package_id)
  {
    // first, get the language info from db
    $information['langs'] = Language::all();
    $language = Language::where('code', $request->language)->firstOrFail();
    $information['language'] = $language;

    // then, get the locations of selected package
    $information['locations'] = PackageLocation::where('language_id', $language->id)
      ->where('package_id', $package_id)
      ->orderBy('id', 'desc')
      ->get();

    return view('backend.packages.locations', $information);
  }

  public function updateLocation(Request $request)
  {
    $rule = [
      'name' => 'required'
    ];

    $validator = Validator::make($request->all(), $rule);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    PackageLocation::findOrFail($request->location_id)->update($request->all());

    $request->session()->flash('success', 'Location updated successfully!');

    return 'success';
  }

  public function deleteLocation(Request $request)
  {
    PackageLocation::findOrFail($request->location_id)->delete();

    $request->session()->flash('success', 'Location deleted successfully!');

    return redirect()->back();
  }

  public function bulkDeleteLocation(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      PackageLocation::findOrFail($id)->delete();
    }

    $request->session()->flash('success', 'Locations deleted successfully!');

    /**
     * this 'success' is returning for ajax call.
     * if return == 'success' then ajax will reload the page.
     */
    return 'success';
  }


  public function storeDaywisePlan(Request $request)
  {
    $rules = [
      'day_number' => 'required',
      'plan' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $lang = Language::findOrFail($request->language_id);

    PackagePlan::create($request->except('language_id') + [
      'language_id' => $lang->id
    ]);

    $request->session()->flash('success', 'New plan added successfully!');

    return Response::json('success', 200);
  }

  public function storeTimewisePlan(Request $request)
  {
    $rules = [
      'start_time' => 'required',
      'end_time' => 'required',
      'plan' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $lang = Language::findOrFail($request->language_id);

    PackagePlan::create($request->except('language_id') + [
      'language_id' => $lang->id
    ]);

    $request->session()->flash('success', 'New plan added successfully!');

    return Response::json('success', 200);
  }

  public function viewPlans(Request $request, $package_id)
  {
    $information['langs'] = Language::all();
    // first, get the language info from db
    $language = Language::where('code', $request->language)->firstOrFail();
    $information['language'] = $language;

    // then, get the plans of selected package
    $information['plans'] = PackagePlan::where('language_id', $language->id)
      ->where('package_id', $package_id)
      ->orderBy('id', 'desc')
      ->paginate(10);

    $package = Package::findOrFail($package_id);
    $information['package'] = $package;

    if ($package->plan_type == 'daywise') {
      return view('backend.packages.daywise_plans', $information);
    } else if ($package->plan_type == 'timewise') {
      return view('backend.packages.timewise_plans', $information);
    }
  }

  public function updateDaywisePlan(Request $request)
  {
    $rules = [
      'day_number' => 'required',
      'plan' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    PackagePlan::findOrFail($request->plan_id)->update($request->all());

    $request->session()->flash('success', 'Plan updated successfully!');

    return 'success';
  }

  public function updateTimewisePlan(Request $request)
  {
    $rules = [
      'start_time' => 'required',
      'end_time' => 'required',
      'plan' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    PackagePlan::findOrFail($request->plan_id)->update($request->all());

    $request->session()->flash('success', 'Plan updated successfully!');

    return 'success';
  }

  public function deletePlan(Request $request)
  {
    PackagePlan::findOrFail($request->plan_id)->delete();

    $request->session()->flash('success', 'Plan deleted successfully!');

    return redirect()->back();
  }

  public function bulkDeletePlan(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      PackagePlan::findOrFail($id)->delete();
    }

    $request->session()->flash('success', 'Plans deleted successfully!');

    /**
     * this 'success' is returning for ajax call.
     * if return == 'success' then ajax will reload the page.
     */
    return 'success';
  }


  public function bookings(Request $request)
  {
    $booking_number = null;

    if ($request->filled('booking_no')) {
      $booking_number = $request['booking_no'];
    }

    if (URL::current() == Route::is('admin.package_bookings.all_bookings')) {
      $bookings = PackageBooking::when($booking_number, function ($query, $booking_number) {
        return $query->where('booking_number', 'like', '%' . $booking_number . '%');
      })->orderBy('id', 'desc')
        ->paginate(10);
    } else if (URL::current() == Route::is('admin.package_bookings.paid_bookings')) {
      $bookings = PackageBooking::when($booking_number, function ($query, $booking_number) {
        return $query->where('booking_number', 'like', '%' . $booking_number . '%');
      })->where('payment_status', 1)
        ->orderBy('id', 'desc')
        ->paginate(10);
    } else if (URL::current() == Route::is('admin.package_bookings.unpaid_bookings')) {
      $bookings = PackageBooking::when($booking_number, function ($query, $booking_number) {
        return $query->where('booking_number', 'like', '%' . $booking_number . '%');
      })->where('payment_status', 0)
        ->orderBy('id', 'desc')
        ->paginate(10);
    }

    return view('backend.packages.bookings', compact('bookings'));
  }

  public function updatePaymentStatus(Request $request)
  {
    $packageBooking = PackageBooking::findOrFail($request->booking_id);

    if ($request->payment_status == 1) {
      $packageBooking->update(['payment_status' => 1]);
    } else {
      $packageBooking->update(['payment_status' => 0]);
    }

    // delete previous invoice from local storage
    if (
      !is_null($packageBooking->invoice) &&
      file_exists('assets/invoices/packages/' . $packageBooking->invoice)
    ) {
      unlink('assets/invoices/packages/' . $packageBooking->invoice);
    }

    // then, generate an invoice in pdf format
    $invoice = $this->generateInvoice($packageBooking);

    // update the invoice field information in database
    $packageBooking->update(['invoice' => $invoice]);

    // finally, send a mail to the customer with the invoice
    $this->sendMailForPaymentStatus($packageBooking, $request->payment_status);

    $request->session()->flash('success', 'Payment status updated successfully!');

    return redirect()->back();
  }

  public function bookingDetails($id)
  {
    $details = PackageBooking::findOrFail($id);

    $language = Language::where('is_default', 1)->firstOrFail();

    /**
     * to get the package title first get the package info using eloquent relationship
     * then, get the package content info of that package using eloquent relationship
     * after that, we can access the package title
     * also, get the package category using eloquent relationship
     */
    $packageInfo = $details->tourPackage()->firstOrFail();

    $packageContentInfo = $packageInfo->packageContent()->where('language_id', $language->id)
      ->firstOrFail();
    $packageTitle = $packageContentInfo->title;

    $packageCategoryInfo = $packageContentInfo->packageCategory()->firstOrFail();

    if (!is_null($packageCategoryInfo)) {
      $packageCategoryName = $packageCategoryInfo->name;
    } else {
      $packageCategoryName = null;
    }

    return view(
      'backend.packages.booking_details',
      compact('details', 'packageTitle', 'packageCategoryName')
    );
  }

  public function sendMail(Request $request)
  {
    $rules = [
      'subject' => 'required',
      'message' => 'required',
    ];

    $messages = [
      'subject.required' => 'The email subject field is required.',
      'message.required' => 'The email message field is required.'
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    // get the mail's smtp information from db
    $mailInfo = DB::table('basic_settings')
      ->select('smtp_status', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name')
      ->firstOrFail();

    // initialize a new mail
    $mail = new PHPMailer(true);

    // if smtp status == 1, then set some value for PHPMailer
    if ($mailInfo->smtp_status == 1) {
      $mail->isSMTP();
      $mail->Host       = $mailInfo->smtp_host;
      $mail->SMTPAuth   = true;
      $mail->Username   = $mailInfo->smtp_username;
      $mail->Password   = $mailInfo->smtp_password;

      if ($mailInfo->encryption == 'TLS') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      }

      $mail->Port       = $mailInfo->smtp_port;
    }

    // finally add other informations and send the mail
    try {
      // Recipients
      $mail->setFrom($mailInfo->from_mail, $mailInfo->from_name);
      $mail->addAddress($request->customer_email);

      // Content
      $mail->isHTML(true);
      $mail->Subject = $request->subject;
      $mail->Body    = $request->message;

      $mail->send();

      $request->session()->flash('success', 'Mail has been sent!');

      /**
       * this 'success' is returning for ajax call.
       * if return == 'success' then ajax will reload the page.
       */
      return 'success';
    } catch (Exception $e) {
      $request->session()->flash('warning', 'Mail could not be sent!');

      /**
       * this 'success' is returning for ajax call.
       * if return == 'success' then ajax will reload the page.
       */
      return 'success';
    }
  }

  public function deleteBooking(Request $request, $id)
  {
    $packageBooking = PackageBooking::findOrFail($id);

    // first, delete the attachment
    if (
      !is_null($packageBooking->attachment) &&
      file_exists('assets/img/attachments/packages/' . $packageBooking->attachment)
    ) {
      unlink('assets/img/attachments/packages/' . $packageBooking->attachment);
    }

    // second, delete the invoice
    if (
      !is_null($packageBooking->invoice) &&
      file_exists('assets/invoices/packages/' . $packageBooking->invoice)
    ) {
      unlink('assets/invoices/packages/' . $packageBooking->invoice);
    }

    // finally, delete the package booking record from db
    $packageBooking->delete();

    $request->session()->flash('success', 'Package booking record deleted successfully!');

    return redirect()->back();
  }

  public function bulkDeleteBooking(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      $packageBooking = PackageBooking::findOrFail($id);

      // first, delete the attachment
      if (
        !is_null($packageBooking->attachment) &&
        file_exists('assets/img/attachments/packages/' . $packageBooking->attachment)
      ) {
        unlink('assets/img/attachments/packages/' . $packageBooking->attachment);
      }

      // second, delete the invoice
      if (
        !is_null($packageBooking->invoice) &&
        file_exists('assets/invoices/packages/' . $packageBooking->invoice)
      ) {
        unlink('assets/invoices/packages/' . $packageBooking->invoice);
      }

      // finally, delete the package booking record from db
      $packageBooking->delete();
    }

    $request->session()->flash('success', 'Package booking records deleted successfully!');

    /**
     * this 'success' is returning for ajax call.
     * if return == 'success' then ajax will reload the page.
     */
    return 'success';
  }


  private function generateInvoice($bookingInfo)
  {
    $fileName = $bookingInfo->booking_number . '.pdf';
    $directory = './assets/invoices/packages/';

    if (!file_exists($directory)) {
      mkdir($directory, 0777, true);
    }

    $fileLocated = $directory . $fileName;

    PDF::loadView('frontend.pdf.package_booking', compact('bookingInfo'))->save($fileLocated);

    return $fileName;
  }

  private function sendMailForPaymentStatus($packageBooking, $status)
  {
    // first get the mail template information from db
    if ($status == 1) {
      $mailTemplate = MailTemplate::where('mail_type', 'payment_received')->firstOrFail();
    } else {
      $mailTemplate = MailTemplate::where('mail_type', 'payment_cancelled')->firstOrFail();
    }
    $mailSubject = $mailTemplate->mail_subject;
    $mailBody = $mailTemplate->mail_body;

    // second get the website title & mail's smtp information from db
    $info = DB::table('basic_settings')
      ->select('website_title', 'smtp_status', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name')
      ->first();

    // replace template's curly-brace string with actual data
    $mailBody = str_replace('{customer_name}', $packageBooking->customer_name, $mailBody);
    $mailBody = str_replace('{website_title}', $info->website_title, $mailBody);

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
      // Recipients
      $mail->setFrom($info->from_mail, $info->from_name);
      $mail->addAddress($packageBooking->customer_email);

      // Attachments (Invoice)
      $mail->addAttachment('assets/invoices/packages/' . $packageBooking->invoice);

      // Content
      $mail->isHTML(true);
      $mail->Subject = $mailSubject;
      $mail->Body    = $mailBody;

      $mail->send();

      return;
    } catch (Exception $e) {
      return redirect()->back()->with('warning', 'Mail could not be sent!');
    }
  }
}
