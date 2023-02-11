<?php

namespace App\Http\Controllers\BackEnd\HomePage;

use App\Http\Controllers\Controller;
use App\Models\HomePage\Brand;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class BrandSectionController extends Controller
{
  public function brandSection(Request $request)
  {
    // first, get the language info from db
    $language = Language::where('code', $request->language)->first();
    $information['language'] = $language;

    // also, get the brand info of that language from db
    $information['brands'] = Brand::where('language_id', $language->id)
      ->orderBy('id', 'desc')
      ->get();

    // get all the languages from db
    $information['langs'] = Language::all();

    return view('backend.home_page.brand_section.index', $information);
  }

  public function storeBrand(Request $request, $language)
  {
    $rules = [
      'brand_url' => 'required',
      'serial_number' => 'required'
    ];

    $brandImgURL = $request->brand_img;

    $allowedExtensions = array('jpg', 'jpeg', 'png', 'svg');
    $fileExtension = pathinfo($brandImgURL, PATHINFO_EXTENSION);

    $rules['brand_img'] = [
      'required',
      function ($attribute, $value, $fail) use ($allowedExtensions, $fileExtension) {
        if (!in_array($fileExtension, $allowedExtensions)) {
          $fail('Only .jpg, .jpeg, .png and .svg file is allowed.');
        }
      }
    ];

    $message = [
      'brand_img.required' => 'The brand image field is required.'
    ];

    $validator = Validator::make($request->all(), $rules, $message);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $lang = Language::where('code', $language)->first();

    // set a name for the image and store it to local storage
    $brandImgName = time() . '.' . $fileExtension;
    $directory = './assets/img/brands/';

    @mkdir($directory, 0777, true);

    @copy($brandImgURL, $directory . $brandImgName);

    Brand::create($request->except('language_id', 'brand_img') + [
      'language_id' => $lang->id,
      'brand_img' => $brandImgName
    ]);

    $request->session()->flash('success', 'New brand added successfully!');

    return 'success';
  }

  public function updateBrand(Request $request)
  {
    $rules = [
      'brand_url' => 'required',
      'serial_number' => 'required'
    ];

    $brandImgURL = $request->brand_img;

    if ($request->filled('brand_img')) {
      $allowedExtensions = array('jpg', 'jpeg', 'png', 'svg');
      $fileExtension = pathinfo($brandImgURL, PATHINFO_EXTENSION);

      $rules['brand_img'] = function ($attribute, $value, $fail) use ($allowedExtensions, $fileExtension) {
        if (!in_array($fileExtension, $allowedExtensions)) {
          $fail('Only .jpg, .jpeg, .png and .svg file is allowed.');
        }
      };
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $brand = Brand::where('id', $request->brand_id)->first();

    if ($request->filled('brand_img')) {
        @unlink('./assets/img/brands/' . $brand->brand_img);
        // set a name for the image and store it to local storage
        $brandImgName = time() . '.' . $fileExtension;
        $directory = './assets/img/brands/';

        @mkdir($directory, 0777, true);

        @copy($brandImgURL, $directory . $brandImgName);
    }

    $brand->update($request->except('brand_img') + [
        'brand_img' => $request->filled('brand_img') ? $brandImgName : $brand->brand_img
    ]);

    $request->session()->flash('success', 'Brand info updated successfully!');

    return 'success';
  }

  public function deleteBrand(Request $request)
  {
    $brand = Brand::where('id', $request->brand_id)->first();

    if (!is_null($brand->brand_img) && file_exists('./assets/img/brands/' . $brand->brand_img)) {
      unlink('./assets/img/brands/' . $brand->brand_img);
    }

    $brand->delete();

    $request->session()->flash('success', 'Brand deleted successfully!');

    return redirect()->back();
  }
}
