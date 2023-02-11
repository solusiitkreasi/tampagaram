<?php

namespace App\Http\Controllers\BackEnd\HomePage;

use App\Http\Controllers\Controller;
use App\Http\Requests\HeroSliderRequest;
use App\Models\HomePage\HeroSlider;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HeroSliderController extends Controller
{
  public function sliderVersion(Request $request)
  {
    // first, get the language info from db
    $language = Language::where('code', $request->language)->first();
    $information['language'] = $language;

    // then, get the slider version info of that language from db
    $information['sliders'] = HeroSlider::where('language_id', $language->id)
      ->orderBy('id', 'desc')
      ->get();

    // get all the languages from db
    $information['langs'] = Language::all();

    return view('backend.home_page.hero_section.slider_version', $information);
  }

  public function createSlider(Request $request)
  {
    // get the language info from db
    $language = Language::where('code', $request->language)->first();
    $information['language'] = $language;

    return view('backend.home_page.hero_section.create_slider', $information);
  }

  public function storeSliderInfo(HeroSliderRequest $request, $language)
  {
    $imgURL = $request->img;

    $allowedExtensions = array('jpg', 'jpeg', 'png', 'svg');
    $fileExtension = pathinfo($imgURL, PATHINFO_EXTENSION);

    $rules = [
      'img' => [
        'required',
        function ($attribute, $value, $fail) use ($allowedExtensions, $fileExtension) {
          if (!in_array($fileExtension, $allowedExtensions)) {
            $fail('Only .jpg, .jpeg, .png and .svg file is allowed.');
          }
        }
      ]
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator);
    }

    $lang = Language::where('code', $language)->first();

    // set a name for the image and store it to local storage
    $imgName = time() . '.' . $fileExtension;
    $directory = './assets/img/hero_slider/';

    if (!file_exists($directory)) {
      mkdir($directory, 0777, true);
    }

    copy($imgURL, $directory . $imgName);

    HeroSlider::create($request->except('language_id', 'img') + [
      'language_id' => $lang->id,
      'img' => $imgName
    ]);

    $request->session()->flash('success', 'New slider added successfully!');

    return redirect()->back();
  }

  public function editSlider(Request $request, $id)
  {
    // get the language info from db
    $language = Language::where('code', $request->language)->first();
    $information['language'] = $language;

    // get the slider info from db for update
    $information['slider'] = HeroSlider::findOrFail($id);

    return view('backend.home_page.hero_section.edit_slider', $information);
  }

  public function updateSliderInfo(HeroSliderRequest $request, $id)
  {
    $imgURL = $request->img;

    if ($request->filled('img')) {
      $allowedExtensions = array('jpg', 'jpeg', 'png', 'svg');
      $fileExtension = pathinfo($imgURL, PATHINFO_EXTENSION);

      $rule = [
        'img' => function ($attribute, $value, $fail) use ($allowedExtensions, $fileExtension) {
          if (!in_array($fileExtension, $allowedExtensions)) {
            $fail('Only .jpg, .jpeg, .png and .svg file is allowed.');
          }
        }
      ];

      $validator = Validator::make($request->all(), $rule);

      if ($validator->fails()) {
        return redirect()->back()->withErrors($validator);
      }
    }

    $slider = HeroSlider::findOrFail($id);

    if ($request->filled('img')) {
      // first, delete the previous image from local storage
      if (!is_null($slider->img) &&
        file_exists('assets/img/hero_slider/' . $slider->img)
      ) {
        unlink('assets/img/hero_slider/' . $slider->img);
      }

      // second, set a name for the image and store it to local storage
      $imgName = time() . '.' . $fileExtension;
      $directory = './assets/img/hero_slider/';

      copy($imgURL, $directory . $imgName);
    }

    $slider->update($request->except('img') + [
      'img' => $request->filled('img') ? $imgName : $slider->img
    ]);

    $request->session()->flash('success', 'Slider info updated successfully!');

    return redirect()->back();
  }

  public function deleteSlider(Request $request)
  {
    $slider = HeroSlider::findOrFail($request->slider_id);

    if (!is_null($slider->img) &&
      file_exists('./assets/img/hero_slider/' . $slider->img)
    ) {
      unlink('./assets/img/hero_slider/' . $slider->img);
    }

    $slider->delete();

    $request->session()->flash('success', 'Slider deleted successfully!');

    return redirect()->back();
  }
}
