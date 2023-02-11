<?php

namespace App\Http\Controllers\BackEnd\HomePage;

use App\Http\Controllers\Controller;
use App\Models\HomePage\Facility;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class FacilityController extends Controller
{
  public function createFacility(Request $request)
  {
    // first, get the language info from db
    $information['language'] = Language::where('code', $request->language)->first();

    return view('backend.home_page.facility_section.create', $information);
  }

  public function storeFacility(Request $request, $language)
  {
    $rules = [
      'facility_icon' => 'required',
      'facility_title' => 'required',
      'facility_text' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $lang = Language::where('code', $language)->first();

    Facility::create($request->except('language_id') + [
      'language_id' => $lang->id
    ]);

    $request->session()->flash('success', 'New facility added successfully!');

    return 'success';
  }

  public function editFacility(Request $request, $id)
  {
    // first, get the language info from db
    $information['language'] = Language::where('code', $request->language)->first();

    $information['facilityInfo'] = Facility::findOrFail($id);

    return view('backend.home_page.facility_section.edit', $information);
  }

  public function updateFacility(Request $request, $id)
  {
    $rules = [
      'facility_icon' => 'required',
      'facility_title' => 'required',
      'facility_text' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    Facility::find($id)->update($request->all());

    $request->session()->flash('success', 'Facility updated successfully!');

    return 'success';
  }

  public function deleteFacility(Request $request)
  {
    Facility::find($request->facilityInfo_id)->delete();

    $request->session()->flash('success', 'Facility deleted successfully!');

    return redirect()->back();
  }
}
