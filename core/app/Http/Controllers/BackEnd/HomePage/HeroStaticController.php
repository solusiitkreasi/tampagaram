<?php

namespace App\Http\Controllers\BackEnd\HomePage;

use App\Http\Controllers\Controller;
use App\Http\Requests\HeroStaticRequest;
use App\Models\HomePage\HeroStatic;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HeroStaticController extends Controller
{
  public function staticVersion(Request $request)
  {
    // first, get the language info from db
    $language = Language::where('code', $request->language)->first();
    $information['language'] = $language;

    // then, get the static version info of that language from db
    $information['data'] = HeroStatic::where('language_id', $language->id)->first();

    // get all the languages from db
    $information['langs'] = Language::all();

    return view('backend.home_page.hero_section.static_version', $information);
  }

  public function updateStaticInfo(HeroStaticRequest $request, $language)
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

    $lang = Language::where('code', $language)->first();
    $data = HeroStatic::where('language_id', $lang->id)->first();

    if ($data == null) {
      if ($request->filled('img')) {
        // set a name for the image and store it to local storage
        $imgName = time() . '.' . $fileExtension;
        $directory = './assets/img/hero_static/';

        if (!file_exists($directory)) {
          mkdir($directory, 0777, true);
        }

        copy($imgURL, $directory . $imgName);
      }

      HeroStatic::create($request->except('language_id', 'img') + [
        'language_id' => $lang->id,
        'img' => $request->filled('img') ? $imgName : null
      ]);
    } else {
      if ($request->filled('img')) {
        // first, delete the previous image from local storage
        if (!is_null($data->img) &&
          file_exists('assets/img/hero_static/' . $data->img)
        ) {
          unlink('assets/img/hero_static/' . $data->img);
        }

        // second, set a name for the image and store it to local storage
        $imgName = time() . '.' . $fileExtension;
        $directory = './assets/img/hero_static/';

        copy($imgURL, $directory . $imgName);
      }

      $data->update($request->except('img') + [
        'img' => $request->filled('img') ? $imgName : $data->img
      ]);
    }

    $request->session()->flash('success', 'Static info updated successfully!');

    return redirect()->back();
  }
}
