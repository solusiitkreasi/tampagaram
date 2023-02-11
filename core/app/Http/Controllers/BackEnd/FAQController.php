<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\FAQ;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class FAQController extends Controller
{
  public function index(Request $request)
  {
    // first, get the language info from db
    $language = Language::where('code', $request->language)->firstOrFail();
    $information['language'] = $language;

    // then, get the faqs of that language from db
    $information['faqs'] = FAQ::where('language_id', $language->id)
      ->orderBy('id', 'desc')
      ->get();

    // also, get all the languages from db
    $information['langs'] = Language::all();

    return view('backend.faq.index', $information);
  }

  public function store(Request $request, $language)
  {
    $rules = [
      'question' => 'required',
      'answer' => 'required',
      'serial_number' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $language = Language::where('code', $language)->firstOrFail();

    FAQ::create($request->except('language_id') + [
      'language_id' => $language->id
    ]);

    $request->session()->flash('success', 'New FAQ added successfully!');

    return 'success';
  }

  public function update(Request $request)
  {
    $rules = [
      'question' => 'required',
      'answer' => 'required',
      'serial_number' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    FAQ::findOrFail($request->faq_id)->update($request->all());

    $request->session()->flash('success', 'FAQ updated successfully!');

    return 'success';
  }

  public function delete(Request $request)
  {
    FAQ::findOrFail($request->faq_id)->delete();

    $request->session()->flash('success', 'FAQ deleted successfully!');

    return redirect()->back();
  }

  public function bulkDelete(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      FAQ::findOrFail($id)->delete();
    }

    $request->session()->flash('success', 'FAQs deleted successfully!');

    /**
     * this 'success' is returning for ajax call.
     * if return == 'success' then ajax will reload the page.
     */
    return 'success';
  }
}
