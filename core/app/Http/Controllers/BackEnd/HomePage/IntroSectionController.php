<?php

namespace App\Http\Controllers\BackEnd\HomePage;

use App\Http\Controllers\Controller;
use App\Http\Requests\CounterInfoRequest;
use App\Http\Requests\IntroSectionRequest;
use App\Models\HomePage\IntroCountInfo;
use App\Models\HomePage\IntroSection;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IntroSectionController extends Controller
{
  public function introSection(Request $request)
  {
    // first, get the language info from db
    $language = Language::where('code', $request->language)->first();
    $information['language'] = $language;

    // then, get the intro section info of that language from db
    $information['data'] = IntroSection::where('language_id', $language->id)->first();

    // also, get the features of that language from db
    $information['counterInfos'] = IntroCountInfo::where('language_id', $language->id)
      ->orderBy('id', 'desc')
      ->get();

    // get all the languages from db
    $information['langs'] = Language::all();

    return view('backend.home_page.intro_section.index', $information);
  }

  public function updateIntroInfo(IntroSectionRequest $request, $language)
  {
    $introImgURL = $request->intro_img;

    if ($request->filled('intro_img')) {
      $allowedExtensions = array('jpg', 'jpeg', 'png', 'svg');
      $fileExtension = pathinfo($introImgURL, PATHINFO_EXTENSION);

      $rule = [
        'intro_img' => function ($attribute, $value, $fail) use ($allowedExtensions, $fileExtension) {
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

    $lang = Language::where('code', $language)->first();
    $data = IntroSection::where('language_id', $lang->id)->first();

    if ($data == null) {
      if ($request->filled('intro_img')) {
        // set a name for the intro section image and store it to local storage
        $introImgName = time() . '.' . $fileExtension;
        $directory = './assets/img/intro_section/';

        if (!file_exists($directory)) {
          mkdir($directory, 0777, true);
        }

        copy($introImgURL, $directory . $introImgName);
      }

      IntroSection::create($request->except('language_id', 'intro_img') + [
        'language_id' => $lang->id,
        'intro_img' => $request->filled('intro_img') ? $introImgName : null
      ]);
    } else {
      if ($request->filled('intro_img')) {
        // first, delete the previous image from local storage
        if (!is_null($data->intro_img) &&
          file_exists('assets/img/intro_section/' . $data->intro_img)
        ) {
          unlink('assets/img/intro_section/' . $data->intro_img);
        }

        // second, set a name for the image and store it to local storage
        $introImgName = time() . '.' . $fileExtension;
        $directory = './assets/img/intro_section/';

        copy($introImgURL, $directory . $introImgName);
      }

      $data->update($request->except('intro_img') + [
        'intro_img' => $request->filled('intro_img') ? $introImgName : $data->intro_img
      ]);
    }

    $request->session()->flash('success', 'Intro section info updated successfully!');

    return redirect()->back();
  }


  public function createCountInfo(Request $request)
  {
    // first, get the language info from db
    $information['language'] = Language::where('code', $request->language)->first();

    return view('backend.home_page.intro_section.create', $information);
  }

  public function storeCountInfo(CounterInfoRequest $request, $language)
  {
    $lang = Language::where('code', $language)->first();

    IntroCountInfo::create($request->except('language_id') + [
      'language_id' => $lang->id
    ]);

    $request->session()->flash('success', 'New counter info added successfully!');

    return 'success';
  }

  public function editCountInfo(Request $request, $id)
  {
    // first, get the language info from db
    $information['language'] = Language::where('code', $request->language)->first();

    $information['counterInfo'] = IntroCountInfo::findOrFail($id);

    return view('backend.home_page.intro_section.edit', $information);
  }

  public function updateCountInfo(CounterInfoRequest $request, $id)
  {
    IntroCountInfo::find($id)->update($request->all());

    $request->session()->flash('success', 'Counter info updated successfully!');

    return 'success';
  }

  public function deleteCountInfo(Request $request)
  {
    IntroCountInfo::find($request->counterInfo_id)->delete();

    $request->session()->flash('success', 'Counter info deleted successfully!');

    return redirect()->back();
  }
}
