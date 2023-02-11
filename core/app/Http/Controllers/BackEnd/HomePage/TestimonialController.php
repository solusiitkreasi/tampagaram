<?php

namespace App\Http\Controllers\BackEnd\HomePage;

use App\Http\Controllers\Controller;
use App\Models\HomePage\Testimonial;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class TestimonialController extends Controller
{
  public function createTestimonial(Request $request)
  {
    // first, get the language info from db
    $information['language'] = Language::where('code', $request->language)->first();

    return view('backend.home_page.testimonial_section.create', $information);
  }

  public function storeTestimonial(Request $request, $language)
  {
    $rules = [
      'client_name' => 'required',
      'comment' => 'required',
      'serial_number' => 'required'
    ];

    $basicSettingsData = DB::table('basic_settings')->select('theme_version')
      ->where('uniqid', 12345)
      ->first();

    if ($basicSettingsData->theme_version == 'theme_two') {
      if (!$request->filled('client_image')) {
        $rules['client_image'] = 'required';
      }
    }

    $clientImgURL = $request->client_image;

    if ($request->filled('client_image')) {
      $allowedExtensions = array('jpg', 'jpeg', 'png', 'svg');
      $fileExtension = pathinfo($clientImgURL, PATHINFO_EXTENSION);

      $rules['client_image'] = function ($attribute, $value, $fail) use ($allowedExtensions, $fileExtension) {
        if (!in_array($fileExtension, $allowedExtensions)) {
          $fail('Only .jpg, .jpeg, .png and .svg file is allowed.');
        }
      };
    }

    if ($basicSettingsData->theme_version == 'theme_two') {
      $rules['client_designation'] = 'required';
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator);
    }

    $lang = Language::where('code', $language)->first();

    if ($request->filled('client_image')) {
        // set a name for the image and store it to local storage
        $clientImgName = time() . '.' . $fileExtension;
        $directory = './assets/img/testimonial_section/';

        if (!file_exists($directory)) {
          mkdir($directory, 0777, true);
        }

        copy($clientImgURL, $directory . $clientImgName);
    }

    Testimonial::create($request->except('language_id', 'client_image') + [
      'language_id' => $lang->id,
      'client_image' => $request->filled('client_image') ? $clientImgName : NULL
    ]);

    $request->session()->flash('success', 'New testimonial added successfully!');

    return redirect()->back();
  }

  public function editTestimonial(Request $request, $id)
  {
    // first, get the language info from db
    $information['language'] = Language::where('code', $request->language)->first();

    $information['testimonialInfo'] = Testimonial::findOrFail($id);

    return view('backend.home_page.testimonial_section.edit', $information);
  }

  public function updateTestimonial(Request $request, $id)
  {
    $rules = [
      'client_name' => 'required',
      'comment' => 'required',
      'serial_number' => 'required'
    ];

    $basicSettingsData = DB::table('basic_settings')->select('theme_version')
      ->where('uniqid', 12345)
      ->first();

    $clientImgURL = $request->client_image;

    if ($request->filled('client_image')) {
      $allowedExtensions = array('jpg', 'jpeg', 'png', 'svg');
      $fileExtension = pathinfo($clientImgURL, PATHINFO_EXTENSION);

      $rules['client_image'] = function ($attribute, $value, $fail) use ($allowedExtensions, $fileExtension) {
        if (!in_array($fileExtension, $allowedExtensions)) {
          $fail('Only .jpg, .jpeg, .png and .svg file is allowed.');
        }
      };
    }

    if ($basicSettingsData->theme_version == 'theme_two') {
      $rules['client_designation'] = 'required';
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator);
    }

    $testimonial = Testimonial::findOrFail($id);

    if ($request->filled('client_image')) {
      // first, delete the previous image from local storage
      if (!is_null($testimonial->client_image) &&
        file_exists('assets/img/testimonial_section/' . $testimonial->client_image)
      ) {
        unlink('assets/img/testimonial_section/' . $testimonial->client_image);
      }

      // second, set a name for the image and store it to local storage
      $clientImgName = time() . '.' . $fileExtension;
      $directory = './assets/img/testimonial_section/';

      copy($clientImgURL, $directory . $clientImgName);
    }

    $testimonial->update($request->except('client_image') + [
      'client_image' => $request->filled('client_image') ? $clientImgName : $testimonial->client_image
    ]);

    $request->session()->flash('success', 'Testimonial updated successfully!');

    return redirect()->back();
  }

  public function deleteTestimonial(Request $request)
  {
    $data = DB::table('basic_settings')->select('theme_version')
      ->where('uniqid', 12345)
      ->first();

    $testimonial = Testimonial::findOrFail($request->testimonial_id);

    if ($data->theme_version == 'theme_two') {
      if (!is_null($testimonial->client_image) &&
        file_exists('./assets/img/testimonial_section/' . $testimonial->client_image)
      ) {
        unlink('./assets/img/testimonial_section/' . $testimonial->client_image);
      }
    }

    $testimonial->delete();

    $request->session()->flash('success', 'Testimonial deleted successfully!');

    return redirect()->back();
  }
}
