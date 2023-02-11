<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\GalleryManagement\Gallery;
use App\Models\GalleryManagement\GalleryCategory;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class GalleryController extends Controller
{
  public function categories(Request $request)
  {
    // first, get the language info from db
    $language = Language::where('code', $request->language)->firstOrFail();
    $information['language'] = $language;

    // then, get the gallery categories of that language from db
    $information['galleryCategories'] = GalleryCategory::where('language_id', $language->id)
      ->orderBy('id', 'desc')
      ->paginate(10);

    // also, get all the languages from db
    $information['langs'] = Language::all();

    return view('backend.gallery.categories', $information);
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

    GalleryCategory::create($request->except('language_id') + [
      'language_id' => $lang->id
    ]);

    $request->session()->flash('success', 'New gallery category added successfully!');

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

    GalleryCategory::findOrFail($request->category_id)->update($request->all());

    $request->session()->flash('success', 'Gallery category updated successfully!');

    return 'success';
  }

  public function deleteCategory(Request $request)
  {
    $galleryCategory = GalleryCategory::findOrFail($request->category_id);

    if ($galleryCategory->galleryImgList()->count() > 0) {
      $request->session()->flash('warning', 'First delete all the images of this category!');

      return redirect()->back();
    }

    $galleryCategory->delete();

    $request->session()->flash('success', 'Gallery category deleted successfully!');

    return redirect()->back();
  }

  public function bulkDeleteCategory(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      $galleryCategory = GalleryCategory::findOrFail($id);

      if ($galleryCategory->galleryImgList()->count() > 0) {
        $request->session()->flash('warning', 'First delete all the images of those category!');

        /**
         * this 'success' is returning for ajax call.
         * if return == 'success' then ajax will reload the page.
         */
        return 'success';
      }

      $galleryCategory->delete();
    }

    $request->session()->flash('success', 'Gallery categories deleted successfully!');

    /**
     * this 'success' is returning for ajax call.
     * if return == 'success' then ajax will reload the page.
     */
    return 'success';
  }


  public function index(Request $request)
  {
    // first, get the language info from db
    $language = Language::where('code', $request->language)->firstOrFail();
    $information['language'] = $language;

    // get all the gallery categories of that language from db
    $information['categories'] = GalleryCategory::where('language_id', $language->id)
      ->where('status', 1)
      ->orderBy('serial_number', 'asc')
      ->get();

    // then, get the gallery images of that language from db
    $information['galleryInfos'] = Gallery::with('galleryCategory')
      ->where('language_id', $language->id)
      ->orderBy('id', 'desc')
      ->get();

    // also, get all the languages from db
    $information['langs'] = Language::all();

    return view('backend.gallery.index', $information);
  }

  public function storeInfo(Request $request, $language)
  {
    $rules = [
      'gallery_category_id' => 'required',
      'title' => 'required',
      'serial_number' => 'required'
    ];

    $galleryImgURL = $request->gallery_img;

    $allowedExtensions = array('jpg', 'jpeg', 'png', 'svg');
    $fileExtension = pathinfo($galleryImgURL, PATHINFO_EXTENSION);

    $rules['gallery_img'] = [
      'required',
      function ($attribute, $value, $fail) use ($allowedExtensions, $fileExtension) {
        if (!in_array($fileExtension, $allowedExtensions)) {
          $fail('Only .jpg, .jpeg, .png and .svg file is allowed.');
        }
      }
    ];

    $message = [
      'gallery_img.required' => 'The gallery image field is required.',
      'gallery_category_id.required' => 'The category field is required.'
    ];

    $validator = Validator::make($request->all(), $rules, $message);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $language = Language::where('code', $language)->firstOrFail();

    // set a name for the image and store it to local storage
    $galleryImgName = time() . '.' . $fileExtension;
    $directory = 'assets/img/gallery/';


    @mkdir($directory, 0775, true);

    copy($galleryImgURL, $directory . $galleryImgName);

    Gallery::create($request->except('language_id', 'gallery_img') + [
      'language_id' => $language->id,
      'gallery_img' => $galleryImgName
    ]);

    $request->session()->flash('success', 'Gallery info added successfully!');

    return 'success';
  }

  public function updateInfo(Request $request)
  {
    $rules = [
      'gallery_category_id' => 'required',
      'title' => 'required',
      'serial_number' => 'required'
    ];

    $galleryImgURL = $request->gallery_img;

    if ($request->filled('gallery_img')) {
      $allowedExtensions = array('jpg', 'jpeg', 'png', 'svg');
      $fileExtension = pathinfo($galleryImgURL, PATHINFO_EXTENSION);

      $rules['gallery_img'] = function ($attribute, $value, $fail) use ($allowedExtensions, $fileExtension) {
        if (!in_array($fileExtension, $allowedExtensions)) {
          $fail('Only .jpg, .jpeg, .png and .svg file is allowed.');
        }
      };
    }

    $message = [
      'gallery_category_id.required' => 'The category field is required.'
    ];

    $validator = Validator::make($request->all(), $rules, $message);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $info = Gallery::where('id', $request->gallery_id)->firstOrFail();

    if ($request->filled('gallery_img')) {
        @unlink('assets/img/gallery/' . $info->gallery_img);
        $galleryImgName = time() . '.' . $fileExtension;
        $directory = 'assets/img/gallery/';

        @copy($galleryImgURL, $directory . $galleryImgName);
    }

    $info->update($request->except('gallery_img') + [
        'gallery_img' => $request->filled('gallery_img') ? $galleryImgName : $info->gallery_img
    ]);

    $request->session()->flash('success', 'Gallery info updated successfully!');

    return 'success';
  }

  public function deleteInfo(Request $request)
  {
    $info = Gallery::where('id', $request->gallery_id)->firstOrFail();

    if (!is_null($info->gallery_img) && file_exists('assets/img/gallery/' . $info->gallery_img)) {
      unlink('assets/img/gallery/' . $info->gallery_img);
    }

    $info->delete();

    $request->session()->flash('success', 'Gallery info deleted successfully!');

    return redirect()->back();
  }

  public function bulkDeleteInfo(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      $info = Gallery::findOrFail($id);

      if (!is_null($info->gallery_img) && file_exists('assets/img/gallery/' . $info->gallery_img)) {
        unlink('assets/img/gallery/' . $info->gallery_img);
      }

      $info->delete();
    }

    $request->session()->flash('success', 'Gallery infos deleted successfully!');

    /**
     * this 'success' is returning for ajax call.
     * if return == 'success' then ajax will reload the page.
     */
    return 'success';
  }
}
