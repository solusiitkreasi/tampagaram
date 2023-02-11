<?php

namespace App\Http\Controllers\BackEnd\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway\OfflineGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class OfflineGatewayController extends Controller
{
  public function index()
  {
    $offlineGateways = OfflineGateway::orderBy('id', 'desc')->get();

    return view('backend.payment_gateways.offline_gateways.index', compact('offlineGateways'));
  }

  public function store(Request $request)
  {
    $rules = [
      'name' => 'required',
      'attachment_status' => 'required',
      'serial_number' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    OfflineGateway::create($request->except('instructions') + [
        'instructions' => clean($request->instructions)
    ]);

    $request->session()->flash('success', 'New offline payment gateway added successfully!');

    return 'success';
  }

  public function updateRoomBookingStatus(Request $request)
  {
    $offlineGateway = OfflineGateway::findOrFail($request->offline_gateway_id);

    if ($request->room_booking_status == 1) {
      $offlineGateway->update(['room_booking_status' => 1]);
    } else {
      $offlineGateway->update(['room_booking_status' => 0]);
    }

    $request->session()->flash('success', 'Room booking status updated successfully!');

    return redirect()->back();
  }

  public function update(Request $request)
  {
    $rules = [
      'name' => 'required',
      'attachment_status' => 'required',
      'serial_number' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    OfflineGateway::findOrFail($request->offline_gateway_id)->update($request->except('instructions') + [
        'instructions' => clean($request->instructions)
    ]);

    $request->session()->flash('success', 'Offline payment gateway updated successfully!');

    return 'success';
  }

  public function delete(Request $request)
  {
    OfflineGateway::findOrFail($request->offline_gateway_id)->delete();

    $request->session()->flash('success', 'Offline payment gateway deleted successfully!');

    return redirect()->back();
  }
}
