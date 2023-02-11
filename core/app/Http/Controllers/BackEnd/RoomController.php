<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRoomBookingRequest;
use App\Http\Requests\CouponRequest;
use App\Models\BasicSettings\MailTemplate;
use App\Models\Language;
use App\Models\PaymentGateway\OfflineGateway;
use App\Models\PaymentGateway\OnlineGateway;
use App\Models\RoomManagement\Coupon;
use App\Models\RoomManagement\Room;
use App\Models\RoomManagement\RoomAmenity;
use App\Models\RoomManagement\RoomBooking;
use App\Models\RoomManagement\RoomCategory;
use App\Models\RoomManagement\RoomContent;
use App\Traits\MiscellaneousTrait;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use PDF;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class RoomController extends Controller
{
  use MiscellaneousTrait;

  public function settings()
  {
    $data = DB::table('basic_settings')->select('room_rating_status', 'room_guest_checkout_status', 'room_category_status')
      ->where('uniqid', 12345)
      ->first();

    return view('backend.rooms.settings', ['data' => $data]);
  }

  public function updateSettings(Request $request)
  {
    $rules = [
      'room_category_status' => 'required',
      'room_rating_status' => 'required',
      'room_guest_checkout_status' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    DB::table('basic_settings')->update([
      'room_category_status' => $request->room_category_status,
      'room_rating_status' => $request->room_rating_status,
      'room_guest_checkout_status' => $request->room_guest_checkout_status
    ]);

    $request->session()->flash('success', 'Room settings updated successfully!');

    return 'success';
  }


  public function coupons()
  {
    // get the coupons from db
    $information['coupons'] = Coupon::orderByDesc('id')->get();

    // also, get the currency information from db
    $information['currencyInfo'] = MiscellaneousTrait::getCurrencyInfo();

    $language = Language::where('is_default', 1)->first();

    $rooms = Room::all();

    $rooms->map(function ($room) use ($language) {
      $room['title'] = $room->roomContent()->where('language_id', $language->id)->pluck('title')->first();
    });

    $information['rooms'] = $rooms;

    return view('backend.rooms.coupons', $information);
  }

  public function storeCoupon(CouponRequest $request)
  {
    $startDate = Carbon::parse($request->start_date);
    $endDate = Carbon::parse($request->end_date);

    if ($request->filled('rooms')) {
      $rooms = $request->rooms;
    }

    Coupon::create($request->except('start_date', 'end_date', 'rooms') + [
      'start_date' => date_format($startDate, 'Y-m-d'),
      'end_date' => date_format($endDate, 'Y-m-d'),
      'rooms' => isset($rooms) ? json_encode($rooms) : null
    ]);

    $request->session()->flash('success', 'New coupon added successfully!');

    return 'success';
  }

  public function updateCoupon(CouponRequest $request)
  {
    $startDate = Carbon::parse($request->start_date);
    $endDate = Carbon::parse($request->end_date);

    if ($request->filled('rooms')) {
      $rooms = $request->rooms;
    }

    Coupon::find($request->id)->update($request->except('start_date', 'end_date', 'rooms') + [
      'start_date' => date_format($startDate, 'Y-m-d'),
      'end_date' => date_format($endDate, 'Y-m-d'),
      'rooms' => isset($rooms) ? json_encode($rooms) : null
    ]);

    $request->session()->flash('success', 'Coupon updated successfully!');

    return 'success';
  }

  public function destroyCoupon($id)
  {
    Coupon::find($id)->delete();

    return redirect()->back()->with('success', 'Coupon deleted successfully!');
  }


  public function amenities(Request $request)
  {
    // first, get the language info from db
    $language = Language::where('code', $request->language)->first();
    $information['language'] = $language;

    // then, get the room amenities of that language from db
    $information['amenities'] = RoomAmenity::where('language_id', $language->id)
      ->orderBy('id', 'desc')
      ->get();

    // also, get all the languages from db
    $information['langs'] = Language::all();

    return view('backend.rooms.amenities', $information);
  }

  public function storeAmenity(Request $request, $language)
  {
    $rules = [
      'name' => 'required',
      'serial_number' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $lang = Language::where('code', $language)->first();

    RoomAmenity::create($request->except('language_id') + [
      'language_id' => $lang->id
    ]);

    $request->session()->flash('success', 'New room amenity added successfully!');

    return 'success';
  }

  public function updateAmenity(Request $request)
  {
    $rules = [
      'name' => 'required',
      'serial_number' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    RoomAmenity::findOrFail($request->amenity_id)->update($request->all());

    $request->session()->flash('success', 'Room amenity updated successfully!');

    return 'success';
  }

  public function deleteAmenity(Request $request)
  {
    RoomAmenity::findOrFail($request->amenity_id)->delete();

    $request->session()->flash('success', 'Room amenity deleted successfully!');

    return redirect()->back();
  }

  public function bulkDeleteAmenity(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      RoomAmenity::findOrFail($id)->delete();
    }

    $request->session()->flash('success', 'Room amenities deleted successfully!');

    /**
     * this 'success' is returning for ajax call.
     * if return == 'success' then ajax will reload the page.
     */
    return 'success';
  }


  public function categories(Request $request)
  {
    // first, get the language info from db
    $language = Language::where('code', $request->language)->first();
    $information['language'] = $language;

    // then, get the room categories of that language from db
    $information['roomCategories'] = RoomCategory::where('language_id', $language->id)
      ->orderBy('id', 'desc')
      ->paginate(10);

    // also, get all the languages from db
    $information['langs'] = Language::all();

    return view('backend.rooms.categories', $information);
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

    $lang = Language::where('code', $language)->first();

    RoomCategory::create($request->except('language_id') + [
      'language_id' => $lang->id
    ]);

    $request->session()->flash('success', 'New room category added successfully!');

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

    RoomCategory::findOrFail($request->category_id)->update($request->all());

    $request->session()->flash('success', 'Room category updated successfully!');

    return 'success';
  }

  public function deleteCategory(Request $request)
  {
    $roomCategory = RoomCategory::findOrFail($request->category_id);

    if ($roomCategory->roomContentList()->count() > 0) {
      $request->session()->flash('warning', 'First delete all the rooms of this category!');

      return redirect()->back();
    }

    $roomCategory->delete();

    $request->session()->flash('success', 'Room category deleted successfully!');

    return redirect()->back();
  }

  public function bulkDeleteCategory(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      $roomCategory = RoomCategory::findOrFail($id);

      if ($roomCategory->roomContentList()->count() > 0) {
        $request->session()->flash('warning', 'First delete all the rooms of those category!');

        /**
         * this 'success' is returning for ajax call.
         * here, by returning the 'success' ajax will show the flash error message
         */
        return 'success';
      }

      $roomCategory->delete();
    }

    $request->session()->flash('success', 'Room categories deleted successfully!');

    /**
     * this 'success' is returning for ajax call.
     * if return == 'success' then ajax will reload the page.
     */
    return 'success';
  }


  public function rooms()
  {
    $languageId = Language::where('is_default', 1)->pluck('id')->first();

    $roomContents = RoomContent::with('room')
      ->where('language_id', '=', $languageId)
      ->orderBy('room_id', 'desc')
      ->get();

    $currencyInfo = MiscellaneousTrait::getCurrencyInfo();

    return view('backend.rooms.rooms', compact('roomContents', 'currencyInfo'));
  }

  public function createRoom()
  {
    // get all the languages from db
    $information['languages'] = Language::all();

    return view('backend.rooms.create_room', $information);
  }

  public function storeRoom(Request $request)
  {
    $rules = [
      'status' => 'required',
      'bed' => 'required',
      'bath' => 'required',
      'rent' => 'required',
      'max_guests' => 'nullable|numeric',
      'quantity' => 'required|numeric'
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
      'featured_img.required' => 'The room\'s featured image is required.',
      'slider_imgs.required' => 'The room\'s slider images is required.'
    ];

    $languages = Language::all();
    $bs = DB::table('basic_settings')->select('room_category_status')->first();

    foreach ($languages as $language) {
      $rules[$language->code . '_title'] = 'required|max:255';

      if ($bs->room_category_status == 1) {
        $rules[$language->code . '_category'] = 'required';
      }

      $rules[$language->code . '_amenities'] = 'required';
      $rules[$language->code . '_summary'] = 'required';
      $rules[$language->code . '_description'] = 'required|min:15';

      $messages[$language->code . '_title.required'] = 'The title field is required for ' . $language->name . ' language';

      $messages[$language->code . '_title.max'] = 'The title field cannot contain more than 255 characters for ' . $language->name . ' language';

      $messages[$language->code . '_category.required'] = 'The category field is required for ' . $language->name . ' language';

      $messages[$language->code . '_amenities.required'] = 'The amenities field is required for ' . $language->name . ' language';

      $messages[$language->code . '_summary.required'] = 'The summary field is required for ' . $language->name . ' language';

      $messages[$language->code . '_description.required'] = 'The description field is required for ' . $language->name . ' language';

      $messages[$language->code . '_description.min'] = 'The description field atleast have 15 characters for ' . $language->name . ' language';
    }

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $room = new Room();

    $sliderImgs = [];
    $sliderDir = './assets/img/rooms/slider_images/';

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

    $room->slider_imgs = json_encode($sliderImgs);

    // set a name for the featured image and store it to local storage
    $featuredImgName = time() . '.' . $featuredImgExt;
    $featuredDir = './assets/img/rooms/';

    if (!file_exists($featuredDir)) {
      mkdir($featuredDir, 0777, true);
    }

    copy($featuredImgURL, $featuredDir . $featuredImgName);

    $room->featured_img = $featuredImgName;
    $room->status = $request->status;
    $room->bed = $request->bed;
    $room->bath = $request->bath;
    $room->rent = $request->rent;
    $room->max_guests = $request->max_guests;
    $room->latitude = $request->latitude;
    $room->longitude = $request->longitude;
    $room->address = $request->address;
    $room->phone = $request->phone;
    $room->email = $request->email;
    $room->quantity = $request->quantity;
    $room->save();

    foreach ($languages as $language) {
      $roomContent = new RoomContent();
      $roomContent->language_id = $language->id;
      if ($bs->room_category_status == 1) {
        $roomContent->room_category_id = $request[$language->code . '_category'];
      }
      $roomContent->room_id = $room->id;
      $roomContent->title = $request[$language->code . '_title'];
      $roomContent->slug = createSlug($request[$language->code . '_title']);
      $roomContent->amenities = json_encode($request[$language->code . '_amenities']);
      $roomContent->summary = $request[$language->code . '_summary'];
      $roomContent->description = clean($request[$language->code . '_description']);
      $roomContent->meta_keywords = $request[$language->code . '_meta_keywords'];
      $roomContent->meta_description = $request[$language->code . '_meta_description'];
      $roomContent->save();
    }

    $request->session()->flash('success', 'New room added successfully!');

    return 'success';
  }

  public function updateFeaturedRoom(Request $request)
  {
    $room = Room::findOrfail($request->roomId);

    if ($request->is_featured == 1) {
      $room->update(['is_featured' => 1]);

      $request->session()->flash('success', 'Room featured successfully!');
    } else {
      $room->update(['is_featured' => 0]);

      $request->session()->flash('success', 'Room unfeatured successfully!');
    }

    return redirect()->back();
  }

  public function editRoom($id)
  {
    // get all the languages from db
    $information['languages'] = Language::all();

    $information['room'] = Room::findOrfail($id);

    return view('backend.rooms.edit_room', $information);
  }

  public function getSliderImages($id)
  {
    $room = Room::findOrFail($id);
    $sliderImages = json_decode($room->slider_imgs);

    $images = [];

    // concatanate slider image with image location
    foreach ($sliderImages as $key => $sliderImage) {
      $data = url('assets/img/rooms/slider_images') . '/' . $sliderImage;
      array_push($images, $data);
    }

    return Response::json($images, 200);
  }

  public function updateRoom(Request $request, $id)
  {
    $rules = [
      'status' => 'required',
      'bed' => 'required',
      'bath' => 'required',
      'rent' => 'required',
      'max_guests' => 'nullable|numeric',
      'quantity' => 'required|numeric'
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
    $bs = DB::table('basic_settings')->select('room_category_status')->first();

    foreach ($languages as $language) {
      $rules[$language->code . '_title'] = 'required|max:255';
      if ($bs->room_category_status == 1) {
        $rules[$language->code . '_category'] = 'required';
      }
      $rules[$language->code . '_amenities'] = 'required';
      $rules[$language->code . '_summary'] = 'required';
      $rules[$language->code . '_description'] = 'required|min:15';

      $messages[$language->code . '_title.required'] = 'The title field is required for ' . $language->name . ' language';

      $messages[$language->code . '_title.max'] = 'The title field cannot contain more than 255 characters for ' . $language->name . ' language';

      if ($bs->room_category_status == 1) {
        $messages[$language->code . '_category.required'] = 'The category field is required for ' . $language->name . ' language';
      }

      $messages[$language->code . '_amenities.required'] = 'The amenities field is required for ' . $language->name . ' language';

      $messages[$language->code . '_summary.required'] = 'The summary field is required for ' . $language->name . ' language';

      $messages[$language->code . '_description.required'] = 'The description field is required for ' . $language->name . ' language';

      $messages[$language->code . '_description.min'] = 'The description field atleast have 15 characters for ' . $language->name . ' language';
    }

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $room = Room::findOrFail($id);
    $roomSldImgs = json_decode($room->slider_imgs);

    $sliderImgs = [];
    $sliderDir = './assets/img/rooms/slider_images/';

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
    if (!empty($roomSldImgs)) {
      foreach ($roomSldImgs as $key => $roomSldImg) {
        if (file_exists('assets/img/rooms/slider_images/' . $roomSldImg)) {
          unlink('assets/img/rooms/slider_images/' . $roomSldImg);
        }
      }
    }

    if ($request->filled('featured_img')) {
      // first, delete the previous featured image from local storage
      if (
        !is_null($room->featured_img) &&
        file_exists('assets/img/rooms/' . $room->featured_img)
      ) {
        unlink('assets/img/rooms/' . $room->featured_img);
      }

      // second, set a name for the image and store it to local storage
      $featuredImgName = time() . '.' . $featuredImgExt;
      $featuredDir = './assets/img/rooms/';

      copy($featuredImgURL, $featuredDir . $featuredImgName);
    }

    $room->update([
      'slider_imgs' => json_encode($sliderImgs),
      'featured_img' => $request->filled('featured_img') ? $featuredImgName : $room->featured_img,
      'status' => $request->status,
      'bed' => $request->bed,
      'bath' => $request->bath,
      'rent' => $request->rent,
      'max_guests' => $request->max_guests,
      'latitude' => $request->latitude,
      'longitude' => $request->longitude,
      'address' => $request->address,
      'phone' => $request->phone,
      'email' => $request->email,
      'quantity' => $request->quantity
    ]);

    foreach ($languages as $language) {
      $roomContent = RoomContent::where('room_id', $id)
        ->where('language_id', $language->id)
        ->first();

      $content = [
        'language_id' => $language->id,
        'room_id' => $id,
        'room_category_id' => $bs->room_category_status == 1 ? $request[$language->code . '_category'] : $roomContent->room_category_id,
        'title' => $request[$language->code . '_title'],
        'slug' => createSlug($request[$language->code . '_title']),
        'amenities' => json_encode($request[$language->code . '_amenities']),
        'summary' => $request[$language->code . '_summary'],
        'description' => clean($request[$language->code . '_description']),
        'meta_keywords' => $request[$language->code . '_meta_keywords'],
        'meta_description' => $request[$language->code . '_meta_description']
      ];

      if (!empty($roomContent)) {
        $roomContent->update($content);
      } else {
        RoomContent::create($content);
      }
    }

    $request->session()->flash('success', 'Room updated successfully!');

    return 'success';
  }

  public function deleteRoom(Request $request)
  {
    $room = Room::findOrFail($request->room_id);

    if ($room->roomContent()->count() > 0) {
      $contents = $room->roomContent()->get();

      foreach ($contents as $content) {
        $content->delete();
      }
    }

    if (!is_null($room->slider_imgs)) {
      $images = json_decode($room->slider_imgs);

      foreach ($images as $image) {
        if (file_exists('assets/img/rooms/slider_images/' . $image)) {
          unlink('assets/img/rooms/slider_images/' . $image);
        }
      }
    }

    if (!is_null($room->featured_img) && file_exists('assets/img/rooms/' . $room->featured_img)) {
      unlink('assets/img/rooms/' . $room->featured_img);
    }

    $room->delete();

    $request->session()->flash('success', 'Room deleted successfully!');

    return redirect()->back();
  }

  public function bulkDeleteRoom(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      $room = Room::findOrFail($id);

      if ($room->roomContent()->count() > 0) {
        $contents = $room->roomContent()->get();

        foreach ($contents as $content) {
          $content->delete();
        }
      }

      if (!is_null($room->slider_imgs)) {
        $images = json_decode($room->slider_imgs);

        foreach ($images as $image) {
          if (file_exists('assets/img/rooms/slider_images/' . $image)) {
            unlink('assets/img/rooms/slider_images/' . $image);
          }
        }
      }

      if (!is_null($room->featured_img) && file_exists('assets/img/rooms/' . $room->featured_img)) {
        unlink('assets/img/rooms/' . $room->featured_img);
      }

      $room->delete();
    }

    $request->session()->flash('success', 'Rooms deleted successfully!');

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

    if (URL::current() == Route::is('admin.room_bookings.all_bookings')) {
      $queryResult['bookings'] = RoomBooking::when($booking_number, function ($query, $booking_number) {
        return $query->where('booking_number', 'like', '%' . $booking_number . '%');
      })->orderBy('id', 'desc')
        ->paginate(10);
    } else if (URL::current() == Route::is('admin.room_bookings.paid_bookings')) {
      $queryResult['bookings'] = RoomBooking::when($booking_number, function ($query, $booking_number) {
        return $query->where('booking_number', 'like', '%' . $booking_number . '%');
      })->where('payment_status', 1)
        ->orderBy('id', 'desc')
        ->paginate(10);
    } else if (URL::current() == Route::is('admin.room_bookings.unpaid_bookings')) {
      $queryResult['bookings'] = RoomBooking::when($booking_number, function ($query, $booking_number) {
        return $query->where('booking_number', 'like', '%' . $booking_number . '%');
      })->where('payment_status', 0)
        ->orderBy('id', 'desc')
        ->paginate(10);
    }

    $language = Language::query()->where('is_default', '=', 1)->first();

    $queryResult['roomInfos'] = $language->roomDetails()->whereHas('room', function (Builder $query) {
      $query->where('status', '=', 1);
    })
      ->select('room_id', 'title')
      ->orderBy('title', 'ASC')
      ->get();

    return view('backend.rooms.bookings', $queryResult);
  }

  public function updatePaymentStatus(Request $request)
  {
    $roomBooking = RoomBooking::findOrFail($request->booking_id);

    if ($request->payment_status == 1) {
      $roomBooking->update(['payment_status' => 1]);
    } else {
      $roomBooking->update(['payment_status' => 0]);
    }

    // delete previous invoice from local storage
    if (
      !is_null($roomBooking->invoice) &&
      file_exists('assets/invoices/rooms/' . $roomBooking->invoice)
    ) {
      unlink('assets/invoices/rooms/' . $roomBooking->invoice);
    }

    // then, generate an invoice in pdf format
    $invoice = $this->generateInvoice($roomBooking);

    // update the invoice field information in database
    $roomBooking->update(['invoice' => $invoice]);

    // finally, send a mail to the customer with the invoice
    $this->sendMailForPaymentStatus($roomBooking, $request->payment_status);

    $request->session()->flash('success', 'Payment status updated successfully!');

    return redirect()->back();
  }

  public function editBookingDetails($id)
  {
    $details = RoomBooking::findOrFail($id);
    $queryResult['details'] = $details;

    // get the difference of two dates, date should be in 'YYYY-MM-DD' format
    $date1 = new DateTime($details->arrival_date);
    $date2 = new DateTime($details->departure_date);
    $queryResult['interval'] = $date1->diff($date2, true);

    $language = Language::where('is_default', 1)->first();

    /**
     * to get the room title first get the room info using eloquent relationship
     * then, get the room content info of that room using eloquent relationship
     * after that, we can access the room title
     * also, get the room category using eloquent relationship
     */
    $roomInfo = $details->hotelRoom()->first();

    $roomContentInfo = $roomInfo->roomContent()->where('language_id', $language->id)->first();
    $queryResult['roomTitle'] = $roomContentInfo->title;

    $roomCategoryInfo = $roomContentInfo->roomCategory()->first();
    $queryResult['roomCategoryName'] = $roomCategoryInfo->name;

    // get all the booked dates of this room
    $roomId = $details->room_id;
    $detailsId = $details->id;

    $queryResult['bookedDates'] = $this->getBookedDatesOfRoom($roomId, $detailsId);

    $queryResult['onlineGateways'] = OnlineGateway::query()
      ->where('status', '=', 1)
      ->select('name')
      ->get();

    $queryResult['offlineGateways'] = OfflineGateway::query()
      ->where('room_booking_status', '=', 1)
      ->select('name')
      ->orderBy('serial_number', 'asc')
      ->get();

    $queryResult['rent'] = $roomInfo->rent;

    return view('backend.rooms.booking_details', $queryResult);
  }

  public function updateBooking(AdminRoomBookingRequest $request)
  {
    $currencyInfo = MiscellaneousTrait::getCurrencyInfo();

    // update the room booking information in database
    $dateArray = explode(' ', $request->dates);

    $onlinePaymentGateway = ['PayPal', 'Stripe', 'Instamojo', 'Paystack', 'Flutterwave', 'Razorpay', 'MercadoPago', 'Mollie', 'Paytm'];

    $gatewayType = in_array($request->payment_method, $onlinePaymentGateway) ? 'online' : 'offline';

    $booking = RoomBooking::query()->findOrFail($request->booking_id);

    $booking->update([
      'customer_name' => $request->customer_name,
      'customer_email' => $request->customer_email,
      'customer_phone' => $request->customer_phone,
      'arrival_date' => $dateArray[0],
      'departure_date' => $dateArray[2],
      'guests' => $request->guests,
      'subtotal' => $request->subtotal,
      'discount' => $request->discount,
      'grand_total' => $request->total,
      'currency_symbol' => $currencyInfo->base_currency_symbol,
      'currency_symbol_position' => $currencyInfo->base_currency_symbol_position,
      'currency_text' => $currencyInfo->base_currency_text,
      'currency_text_position' => $currencyInfo->base_currency_text_position,
      'payment_method' => $request->payment_method,
      'gateway_type' => $gatewayType,
      'payment_status' => $request->payment_status
    ]);

    $request->session()->flash('success', 'Booking information has updated.');

    return redirect()->back();
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
      ->where('uniqid', 12345)
      ->first();

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
      $mail->Body    = clean($request->message);

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
    $roomBooking = RoomBooking::findOrFail($id);

    // first, delete the attachment
    if (
      !is_null($roomBooking->attachment) &&
      file_exists('assets/img/attachments/rooms/' . $roomBooking->attachment)
    ) {
      unlink('assets/img/attachments/rooms/' . $roomBooking->attachment);
    }

    // second, delete the invoice
    if (
      !is_null($roomBooking->invoice) &&
      file_exists('assets/invoices/rooms/' . $roomBooking->invoice)
    ) {
      unlink('assets/invoices/rooms/' . $roomBooking->invoice);
    }

    // finally, delete the room booking record from db
    $roomBooking->delete();

    $request->session()->flash('success', 'Room booking record deleted successfully!');

    return redirect()->back();
  }

  public function bulkDeleteBooking(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      $roomBooking = RoomBooking::findOrFail($id);

      // first, delete the attachment
      if (
        !is_null($roomBooking->attachment) &&
        file_exists('assets/img/attachments/rooms/' . $roomBooking->attachment)
      ) {
        unlink('assets/img/attachments/rooms/' . $roomBooking->attachment);
      }

      // second, delete the invoice
      if (
        !is_null($roomBooking->invoice) &&
        file_exists('assets/invoices/rooms/' . $roomBooking->invoice)
      ) {
        unlink('assets/invoices/rooms/' . $roomBooking->invoice);
      }

      // finally, delete the room booking record from db
      $roomBooking->delete();
    }

    $request->session()->flash('success', 'Room booking records deleted successfully!');

    /**
     * this 'success' is returning for ajax call.
     * if return == 'success' then ajax will reload the page.
     */
    return 'success';
  }


  private function generateInvoice($bookingInfo)
  {
    $fileName = $bookingInfo->booking_number . '.pdf';
    $directory = './assets/invoices/rooms/';

    if (!file_exists($directory)) {
      mkdir($directory, 0777, true);
    }

    $fileLocated = $directory . $fileName;

    PDF::loadView('frontend.pdf.room_booking', compact('bookingInfo'))->save($fileLocated);

    return $fileName;
  }

  private function sendMailForPaymentStatus($roomBooking, $status)
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
    $mailBody = str_replace('{customer_name}', $roomBooking->customer_name, $mailBody);
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
      $mail->addAddress($roomBooking->customer_email);

      // Attachments (Invoice)
      $mail->addAttachment('assets/invoices/rooms/' . $roomBooking->invoice);

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


  // room booking from admin panel
  public function bookedDates(Request $request)
  {
    $rule = [
      'room_id' => 'required'
    ];

    $message = [
      'room_id.required' => 'Please select a room.'
    ];

    $validator = Validator::make($request->all(), $rule, $message);

    if ($validator->fails()) {
      return response()->json([
        'error' => $validator->getMessageBag()
      ]);
    }

    // get all the booked dates of the selected room
    $roomId = $request['room_id'];

    $bookedDates = $this->getBookedDatesOfRoom($roomId);

    $request->session()->put('bookedDates', $bookedDates);

    return response()->json([
      'success' => route('admin.room_bookings.booking_form', ['room_id' => $roomId])
    ]);
  }

  public function getBookedDatesOfRoom($id, $bookingId = null)
  {
    $quantity = Room::query()->findOrFail($id)->quantity;

    $bookings = RoomBooking::query()->where('room_id', '=', $id)
      ->where('payment_status', '=', 1)
      ->select('arrival_date', 'departure_date')
      ->get();

    $bookedDates = [];

    foreach ($bookings as $booking) {
      // get all the dates between the booking arrival date & booking departure date
      $date_1 = $booking->arrival_date;
      $date_2 = $booking->departure_date;

      $allDates = $this->getAllDates($date_1, $date_2, 'Y-m-d');

      // loop through the list of dates, which we have found from the booking arrival date & booking departure date
      foreach ($allDates as $date) {
        $bookingCount = 0;

        // loop through all the bookings
        foreach ($bookings as $currentBooking) {
          $bookingStartDate = Carbon::parse($currentBooking->arrival_date);
          $bookingEndDate = Carbon::parse($currentBooking->departure_date);
          $currentDate = Carbon::parse($date);

          // check for each date, whether the date is present or not in any of the booking date range
          if ($currentDate->betweenIncluded($bookingStartDate, $bookingEndDate)) {
            $bookingCount++;
          }
        }

        // if the number of booking of a specific date is same as the room quantity, then mark that date as unavailable
        if ($bookingCount >= $quantity && !in_array($date, $bookedDates)) {
          array_push($bookedDates, $date);
        }
      }
    }

    if (is_null($bookingId)) {
      return $bookedDates;
    } else {
      $booking = RoomBooking::query()->findOrFail($bookingId);
      $arrivalDate = $booking->arrival_date;
      $departureDate = $booking->departure_date;

      // get all the dates between the booking arrival date & booking departure date
      $bookingAllDates = $this->getAllDates($arrivalDate, $departureDate, 'Y-m-d');

      // remove dates of this booking from 'bookedDates' array while editing a room booking
      foreach ($bookingAllDates as $date) {
        $key = array_search($date, $bookedDates);

        if ($key !== false) {
          unset($bookedDates[$key]);
        }
      }

      return array_values($bookedDates);
    }
  }

  public function getAllDates($startDate, $endDate, $format)
  {
    $dates = [];

    // convert string to timestamps
    $currentTimestamps = strtotime($startDate);
    $endTimestamps = strtotime($endDate);

    // set an increment value
    $stepValue = '+1 day';

    // push all the timestamps to the 'dates' array by formatting those timestamps into date
    while ($currentTimestamps <= $endTimestamps) {
      $formattedDate = date($format, $currentTimestamps);
      array_push($dates, $formattedDate);
      $currentTimestamps = strtotime($stepValue, $currentTimestamps);
    }

    return $dates;
  }

  public function bookingForm(Request $request)
  {
    if ($request->session()->has('bookedDates')) {
      $queryResult['dates'] = $request->session()->get('bookedDates');
    } else {
      $queryResult['dates'] = [];
    }

    $id = $request['room_id'];
    $queryResult['id'] = $id;

    $room = Room::query()->find($id);
    $queryResult['rent'] = $room->rent;

    $queryResult['currencyInfo'] = MiscellaneousTrait::getCurrencyInfo();

    $queryResult['onlineGateways'] = OnlineGateway::query()
      ->where('status', '=', 1)
      ->select('name')
      ->get();

    $queryResult['offlineGateways'] = OfflineGateway::query()
      ->where('room_booking_status', '=', 1)
      ->select('name')
      ->orderBy('serial_number', 'asc')
      ->get();

    return view('backend.rooms.booking_form', $queryResult);
  }

  public function makeBooking(AdminRoomBookingRequest $request)
  {
    $currencyInfo = MiscellaneousTrait::getCurrencyInfo();

    // store the room booking information in database
    $dateArray = explode(' ', $request->dates);

    $onlinePaymentGateway = ['PayPal', 'Stripe', 'Instamojo', 'Paystack', 'Flutterwave', 'Razorpay', 'MercadoPago', 'Mollie', 'Paytm'];

    $gatewayType = in_array($request->payment_method, $onlinePaymentGateway) ? 'online' : 'offline';

    $bookingInfo = RoomBooking::query()->create([
      'booking_number' => time(),
      'user_id' => null,
      'customer_name' => $request->customer_name,
      'customer_email' => $request->customer_email,
      'customer_phone' => $request->customer_phone,
      'room_id' => $request->room_id,
      'arrival_date' => $dateArray[0],
      'departure_date' => $dateArray[2],
      'guests' => $request->guests,
      'subtotal' => $request->subtotal,
      'discount' => $request->discount,
      'grand_total' => $request->total,
      'currency_symbol' => $currencyInfo->base_currency_symbol,
      'currency_symbol_position' => $currencyInfo->base_currency_symbol_position,
      'currency_text' => $currencyInfo->base_currency_text,
      'currency_text_position' => $currencyInfo->base_currency_text_position,
      'payment_method' => $request->payment_method,
      'gateway_type' => $gatewayType,
      'payment_status' => $request->payment_status
    ]);

    if ($request->payment_status == 1) {
      // generate an invoice in pdf format
      $invoice = $this->generateInvoice($bookingInfo);

      // update the invoice field information in database
      $bookingInfo->update(['invoice' => $invoice]);

      // send a mail to the customer with an invoice
      $this->sendMailForRoomBooking($bookingInfo);
    }

    $request->session()->flash('success', 'Room has booked.');

    return redirect()->back();
  }

  public function sendMailForRoomBooking($bookingInfo)
  {
    // first get the mail template information from db
    $mailTemplate = MailTemplate::query()->where('mail_type', '=', 'room_booking')->first();
    $mailSubject = $mailTemplate->mail_subject;
    $mailBody = replaceBaseUrl($mailTemplate->mail_body, 'summernote');

    // second get the website title & mail's smtp information from db
    $info = DB::table('basic_settings')
      ->select('website_title', 'smtp_status', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name')
      ->first();

    // get the difference of two dates, date should be in 'YYYY-MM-DD' format
    $date1 = new DateTime($bookingInfo->arrival_date);
    $date2 = new DateTime($bookingInfo->departure_date);
    $interval = $date1->diff($date2, true);

    // get the room category name according to language
    $language = Language::query()->where('is_default', '=', 1)->first();

    $roomContent = RoomContent::query()->where('language_id', '=', $language->id)
      ->where('room_id', '=', $bookingInfo->room_id)
      ->first();

    $roomCategoryName = $roomContent->roomCategory->name;

    $roomRent = ($bookingInfo->currency_text_position == 'left' ? $bookingInfo->currency_text . ' ' : '') . $bookingInfo->grand_total . ($bookingInfo->currency_text_position == 'right' ? ' ' . $bookingInfo->currency_text : '');

    // get the amenities of booked room
    $amenityIds = json_decode($roomContent->amenities);

    $amenityArray = [];

    foreach ($amenityIds as $id) {
      $amenity = RoomAmenity::query()->findOrFail($id);
      array_push($amenityArray, $amenity->name);
    }

    // now, convert amenity array into comma separated string
    $amenityString = implode(', ', $amenityArray);

    // replace template's curly-brace string with actual data
    $mailBody = str_replace('{customer_name}', $bookingInfo->customer_name, $mailBody);
    $mailBody = str_replace('{room_name}', $roomContent->title, $mailBody);
    $mailBody = str_replace('{room_rent}', $roomRent, $mailBody);
    $mailBody = str_replace('{booking_number}', $bookingInfo->booking_number, $mailBody);
    $mailBody = str_replace('{booking_date}', date_format($bookingInfo->created_at, 'F d, Y'), $mailBody);
    $mailBody = str_replace('{number_of_night}', $interval->days, $mailBody);
    $mailBody = str_replace('{website_title}', $info->website_title, $mailBody);
    $mailBody = str_replace('{check_in_date}', $bookingInfo->arrival_date, $mailBody);
    $mailBody = str_replace('{check_out_date}', $bookingInfo->departure_date, $mailBody);
    $mailBody = str_replace('{number_of_guests}', $bookingInfo->guests, $mailBody);
    $mailBody = str_replace('{room_type}', $roomCategoryName, $mailBody);
    $mailBody = str_replace('{room_amenities}', $amenityString, $mailBody);

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
      $mail->addAddress($bookingInfo->customer_email);

      // Attachments (Invoice)
      $mail->addAttachment('assets/invoices/rooms/' . $bookingInfo->invoice);

      // Content
      $mail->isHTML(true);
      $mail->Subject = $mailSubject;
      $mail->Body    = $mailBody;

      $mail->send();

      return;
    } catch (Exception $e) {
      return;
    }
  }
}
