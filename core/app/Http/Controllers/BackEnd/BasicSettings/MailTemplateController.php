<?php

namespace App\Http\Controllers\BackEnd\BasicSettings;

use App\Http\Controllers\Controller;
use App\Models\BasicSettings\MailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MailTemplateController extends Controller
{
  public function mailTemplates()
  {
    $templates = MailTemplate::all();

    return view('backend.basic_settings.email.mail_templates', compact('templates'));
  }

  public function editMailTemplate($id)
  {
    $templateInfo = MailTemplate::findOrFail($id);

    return view('backend.basic_settings.email.edit_mail_template', compact('templateInfo'));
  }

  public function updateMailTemplate(Request $request, $id)
  {
    $rules = [
      'mail_subject' => 'required',
      'mail_body' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    MailTemplate::findOrFail($id)->update($request->except('mail_type', 'mail_body') + [
        'mail_body' => clean($request->mail_body)
    ]);

    $request->session()->flash('success', 'Mail template updated successfully!');

    return redirect()->back();
  }
}
