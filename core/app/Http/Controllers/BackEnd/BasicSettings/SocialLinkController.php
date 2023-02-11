<?php

namespace App\Http\Controllers\BackEnd\BasicSettings;

use App\Http\Controllers\Controller;
use App\Models\BasicSettings\SocialLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SocialLinkController extends Controller
{
  public function socialLinks()
  {
    $socialLinks = SocialLink::orderBy('id', 'desc')->get();
    
    return view('backend.basic_settings.socials.social_links', compact('socialLinks'));
  }

  public function storeSocialLink(Request $request)
  {
    $rules = [
      'icon' => 'required',
      'url' => 'required',
      'serial_number' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors())->withInput();
    }

    SocialLink::create($request->all());

    $request->session()->flash('success', 'New social link added successfully!');

    return redirect()->back();
  }

  public function editSocialLink($id)
  {
    $socialLink = SocialLink::findOrFail($id);

    return view('backend.basic_settings.socials.edit_social_link', compact('socialLink'));
  }

  public function updateSocialLink(Request $request)
  {
    $rules = [
      'icon' => 'required',
      'url' => 'required',
      'serial_number' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors())->withInput();
    }

    SocialLink::findOrFail($request->id)->update($request->all());

    $request->session()->flash('success', 'Social link updated successfully!');

    return redirect()->back();
  }

  public function deleteSocialLink(Request $request)
  {
    SocialLink::findOrFail($request->id)->delete();

    $request->session()->flash('success', 'Social link deleted successfully!');

    return redirect()->back();
  }
}
