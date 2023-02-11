<?php

namespace App\Http\Controllers\BackEnd\HomePage;

use App\Http\Controllers\Controller;
use App\Models\HomePage\Facility;
use App\Models\HomePage\SectionHeading;
use App\Models\HomePage\Testimonial;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class SectionHeadingController extends Controller
{
  public function roomSection(Request $request)
  {
    // first, get the language info from db
    $language = Language::where('code', $request->language)->first();
    $information['language'] = $language;

    // then, get the room section heading info of that language from db
    $information['data'] = SectionHeading::where('language_id', $language->id)->first();

    // get all the languages from db
    $information['langs'] = Language::all();

    return view('backend.home_page.room_section', $information);
  }

  public function updateRoomSection(Request $request, $language)
  {
    $rules = [
      'room_section_title' => 'required',
      'room_section_subtitle' => 'required',
      'room_section_text' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $lang = Language::where('code', $language)->first();
    $data = SectionHeading::where('language_id', $lang->id)->first();

    if ($data == null) {
      SectionHeading::create($request->except('language_id') + [
        'language_id' => $lang->id
      ]);
    } else {
      $data->update($request->all());
    }

    $request->session()->flash('success', 'Room section updated successfully!');

    return 'success';
  }


  public function serviceSection(Request $request)
  {
    // first, get the language info from db
    $language = Language::where('code', $request->language)->first();
    $information['language'] = $language;

    // then, get the service section heading info of that language from db
    $information['data'] = SectionHeading::where('language_id', $language->id)->first();

    // get all the languages from db
    $information['langs'] = Language::all();

    return view('backend.home_page.service_section', $information);
  }

  public function updateServiceSection(Request $request, $language)
  {
    $rules = [
      'service_section_title' => 'required',
      'service_section_subtitle' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $lang = Language::where('code', $language)->first();
    $data = SectionHeading::where('language_id', $lang->id)->first();

    if ($data == null) {
      SectionHeading::create($request->except('language_id') + [
        'language_id' => $lang->id
      ]);
    } else {
      $data->update($request->all());
    }

    $request->session()->flash('success', 'Service section updated successfully!');

    return 'success';
  }


  public function bookingSection(Request $request)
  {
    // first, get the language info from db
    $language = Language::where('code', $request->language)->first();
    $information['language'] = $language;

    // then, get the booking section heading info of that language from db
    $information['data'] = SectionHeading::where('language_id', $language->id)->first();

    // get all the languages from db
    $information['langs'] = Language::all();

    return view('backend.home_page.booking_section', $information);
  }

  public function updateBookingSection(Request $request, $language)
  {
    $rules = [
      'booking_section_title' => 'required',
      'booking_section_subtitle' => 'required',
      'booking_section_button' => 'required',
      'booking_section_button_url' => 'required',
      'booking_section_video_url' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $link = $request->booking_section_video_url;

    if (strpos($link, '&') != 0) {
      $link = substr($link, 0, strpos($link, '&'));
    }

    $lang = Language::where('code', $language)->first();
    $data = SectionHeading::where('language_id', $lang->id)->first();

    if ($data == null) {
      SectionHeading::create($request->except('language_id', 'booking_section_video_url') + [
        'language_id' => $lang->id,
        'booking_section_video_url' => $link
      ]);
    } else {
      $data->update($request->except('booking_section_video_url') + [
        'booking_section_video_url' => $link
      ]);
    }

    $request->session()->flash('success', 'Booking section updated successfully!');

    return 'success';
  }


  public function packageSection(Request $request)
  {
    // first, get the language info from db
    $language = Language::where('code', $request->language)->first();
    $information['language'] = $language;

    // then, get the package section heading info of that language from db
    $information['data'] = SectionHeading::where('language_id', $language->id)->first();

    // get all the languages from db
    $information['langs'] = Language::all();

    return view('backend.home_page.package_section', $information);
  }

  public function updatePackageSection(Request $request, $language)
  {
    $rules = [
      'package_section_title' => 'required',
      'package_section_subtitle' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $lang = Language::where('code', $language)->first();
    $data = SectionHeading::where('language_id', $lang->id)->first();

    if ($data == null) {
      SectionHeading::create($request->except('language_id') + [
        'language_id' => $lang->id
      ]);
    } else {
      $data->update($request->all());
    }

    $request->session()->flash('success', 'Package section updated successfully!');

    return 'success';
  }


  public function facilitySection(Request $request)
  {
    // first, get the language info from db
    $language = Language::where('code', $request->language)->first();
    $information['language'] = $language;

    // then, get the facility section heading info of that language from db
    $information['data'] = SectionHeading::where('language_id', $language->id)->first();

    // also, get the facilities of that language from db
    $information['facilityInfos'] = Facility::where('language_id', $language->id)
      ->orderby('id', 'desc')
      ->get();

    // get all the languages from db
    $information['langs'] = Language::all();

    return view('backend.home_page.facility_section.index', $information);
  }

  public function updateFacilitySection(Request $request, $language)
  {
    $rules = [
      'facility_section_title' => 'required',
      'facility_section_subtitle' => 'required'
    ];

    $facilitySecImgURL = $request->facility_section_image;

    if ($request->filled('facility_section_image')) {
      $allowedExtensions = array('jpg', 'jpeg', 'png', 'svg');
      $fileExtension = pathinfo($facilitySecImgURL, PATHINFO_EXTENSION);

      $rules['facility_section_image'] = function ($attribute, $value, $fail) use ($allowedExtensions, $fileExtension) {
        if (!in_array($fileExtension, $allowedExtensions)) {
          $fail('Only .jpg, .jpeg, .png and .svg file is allowed.');
        }
      };
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator);
    }

    $lang = Language::where('code', $language)->first();
    $data = SectionHeading::where('language_id', $lang->id)->first();

    if ($data == null) {
      if ($request->filled('facility_section_image')) {
        // set a name for the image and store it to local storage
        $facilityImgName = time() . '.' . $fileExtension;
        $directory = './assets/img/facility_section/';

        if (!file_exists($directory)) {
          mkdir($directory, 0777, true);
        }

        copy($facilitySecImgURL, $directory . $facilityImgName);
      }

      SectionHeading::create($request->except('language_id', 'facility_section_image') + [
        'language_id' => $lang->id,
        'facility_section_image' => $request->filled('facility_section_image') ? $facilityImgName : null
      ]);
    } else {
      if ($request->filled('facility_section_image')) {
        // first, delete the previous image from local storage
        if (!is_null($data->facility_section_image) &&
          file_exists('assets/img/facility_section/' . $data->facility_section_image)
        ) {
          unlink('assets/img/facility_section/' . $data->facility_section_image);
        }

        // second, set a name for the image and store it to local storage
        $facilityImgName = time() . '.' . $fileExtension;
        $directory = './assets/img/facility_section/';

        copy($facilitySecImgURL, $directory . $facilityImgName);
      }

      $data->update($request->except('facility_section_image') + [
        'facility_section_image' => $request->filled('facility_section_image') ? $facilityImgName : $data->facility_section_image
      ]);
    }

    $request->session()->flash('success', 'Facility section updated successfully!');

    return redirect()->back();
  }


  public function testimonialSection(Request $request)
  {
    // first, get the language info from db
    $language = Language::where('code', $request->language)->first();
    $information['language'] = $language;

    // then, get the testimonial section heading info of that language from db
    $information['data'] = SectionHeading::where('language_id', $language->id)->first();

    // also, get the testimonials of that language from db
    $information['testimonialInfos'] = Testimonial::where('language_id', $language->id)
      ->orderby('id', 'desc')
      ->get();

    // get all the languages from db
    $information['langs'] = Language::all();

    return view('backend.home_page.testimonial_section.index', $information);
  }

  public function updateTestimonialSection(Request $request, $language)
  {
    $rules = [
      'testimonial_section_title' => 'required',
      'testimonial_section_subtitle' => 'required'
    ];

    $basicSettingsData = DB::table('basic_settings')->select('theme_version')
      ->where('uniqid', 12345)
      ->first();

    $lang = Language::where('code', $language)->first();
    $data = SectionHeading::where('language_id', $lang->id)->first();

    if ($basicSettingsData->theme_version == 'theme_two') {
      if (is_null($data->testimonial_section_image) &&
        !$request->filled('testimonial_section_image')
      ) {
        $rules['testimonial_section_image'] = 'required';
      }
    }

    $testimonialSecImgURL = $request->testimonial_section_image;

    if ($request->filled('testimonial_section_image')) {
      $allowedExtensions = array('jpg', 'jpeg', 'png', 'svg');
      $fileExtension = pathinfo($testimonialSecImgURL, PATHINFO_EXTENSION);

      $rules['testimonial_section_image'] = function ($attribute, $value, $fail) use ($allowedExtensions, $fileExtension) {
        if (!in_array($fileExtension, $allowedExtensions)) {
          $fail('Only .jpg, .jpeg, .png and .svg file is allowed.');
        }
      };
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator);
    }

    if ($data == null) {
      // set a name for the image and store it to local storage
      $testimonialImgName = time() . '.' . $fileExtension;
      $directory = './assets/img/testimonial_section/';

      if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
      }

      copy($testimonialSecImgURL, $directory . $testimonialImgName);

      SectionHeading::create($request->except('language_id', 'testimonial_section_image') + [
        'language_id' => $lang->id,
        'testimonial_section_image' => $testimonialImgName
      ]);
    } else {
      if ($request->filled('testimonial_section_image')) {
        // first, delete the previous image from local storage
        if (!is_null($data->testimonial_section_image) &&
          file_exists('assets/img/testimonial_section/' . $data->testimonial_section_image)
        ) {
          unlink('assets/img/testimonial_section/' . $data->testimonial_section_image);
        }

        // second, set a name for the image and store it to local storage
        $testimonialImgName = time() . '.' . $fileExtension;
        $directory = './assets/img/testimonial_section/';

        copy($testimonialSecImgURL, $directory . $testimonialImgName);
      }

      $data->update($request->except('testimonial_section_image') + [
        'testimonial_section_image' => $request->filled('testimonial_section_image') ? $testimonialImgName : $data->testimonial_section_image
      ]);
    }

    $request->session()->flash('success', 'Testimonial section updated successfully!');

    return redirect()->back();
  }


  public function faqSection(Request $request)
  {
    // first, get the language info from db
    $language = Language::where('code', $request->language)->first();
    $information['language'] = $language;

    // then, get the blog section heading info of that language from db
    $information['data'] = SectionHeading::where('language_id', $language->id)->first();

    // get all the languages from db
    $information['langs'] = Language::all();

    return view('backend.home_page.faq_section', $information);
  }

  public function updateFAQSection(Request $request, $language)
  {
    $rules = [
      'faq_section_title' => 'required',
      'faq_section_subtitle' => 'required'
    ];

    $lang = Language::where('code', $language)->first();
    $data = SectionHeading::where('language_id', $lang->id)->first();

    if (empty($data->faq_section_image) &&
      !$request->filled('faq_section_image')
    ) {
      $rules['faq_section_image'] = 'required';
    }

    $faqSecImgURL = $request->faq_section_image;

    if ($request->filled('faq_section_image')) {
      $allowedExtensions = array('jpg', 'jpeg', 'png', 'svg');
      $fileExtension = pathinfo($faqSecImgURL, PATHINFO_EXTENSION);

      $rules['faq_section_image'] = function ($attribute, $value, $fail) use ($allowedExtensions, $fileExtension) {
        if (!in_array($fileExtension, $allowedExtensions)) {
          $fail('Only .jpg, .jpeg, .png and .svg file is allowed.');
        }
      };
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator);
    }

    if ($data == null) {
      // set a name for the image and store it to local storage
      $faqImgName = time() . '.' . $fileExtension;
      $directory = './assets/img/faq_section/';

      if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
      }

      copy($faqSecImgURL, $directory . $faqImgName);

      SectionHeading::create($request->except('language_id', 'faq_section_image') + [
        'language_id' => $lang->id,
        'faq_section_image' => $faqImgName
      ]);
    } else {
      if ($request->filled('faq_section_image')) {
        // first, delete the previous image from local storage
        if (!empty($data->faq_section_image) &&
          file_exists('assets/img/faq_section/' . $data->faq_section_image)
        ) {
          unlink('assets/img/faq_section/' . $data->faq_section_image);
        }

        // second, set a name for the image and store it to local storage
        $faqImgName = time() . '.' . $fileExtension;
        $directory = './assets/img/faq_section/';

        copy($faqSecImgURL, $directory . $faqImgName);
      }

      $data->update($request->except('faq_section_image') + [
        'faq_section_image' => $request->filled('faq_section_image') ? $faqImgName : $data->faq_section_image
      ]);
    }

    $request->session()->flash('success', 'FAQ section updated successfully!');

    return redirect()->back();
  }


  public function blogSection(Request $request)
  {
    // first, get the language info from db
    $language = Language::where('code', $request->language)->first();
    $information['language'] = $language;

    // then, get the blog section heading info of that language from db
    $information['data'] = SectionHeading::where('language_id', $language->id)->first();

    // get all the languages from db
    $information['langs'] = Language::all();

    return view('backend.home_page.blog_section', $information);
  }

  public function updateBlogSection(Request $request, $language)
  {
    $rules = [
      'blog_section_title' => 'required',
      'blog_section_subtitle' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $lang = Language::where('code', $language)->first();
    $data = SectionHeading::where('language_id', $lang->id)->first();

    if ($data == null) {
      SectionHeading::create($request->except('language_id') + [
        'language_id' => $lang->id
      ]);
    } else {
      $data->update($request->all());
    }

    $request->session()->flash('success', 'Blog section updated successfully!');

    return 'success';
  }
}
