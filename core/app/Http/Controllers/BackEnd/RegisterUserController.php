<?php

namespace App\Http\Controllers\BackEnd;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RoomManagement\RoomReview;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Session;

class RegisterUserController extends Controller
{
    public function index(Request $request)
    {
        $term = $request->term;

        $users = User::when($term, function($query, $term) {
            $query->where('username', 'like', '%' . $term . '%')->orWhere('email', 'like', '%' . $term . '%');
        })->paginate(10);
        return view('backend.register_user.index',compact('users'));
    }

    public function view($id)
    {
        $user = User::findOrFail($id);
        return view('backend.register_user.details',compact('user'));

    }


    public function userban(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $user->update([
            'status' => $request->status,
        ]);

        Session::flash('success', $user->username.' status update successfully!');
        return back();
    }


    public function emailStatus(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $user->update([
            'email_verified' => $request->email_verified,
        ]);

        Session::flash('success', 'Email status updated for ' . $user->username);
        return back();
    }

    public function delete(Request $request)
    {
        $user = User::findOrFail($request->user_id);

        if ($user->bookHotelRoom()->count() > 0) {
            $roomBookings = $user->bookHotelRoom()->get();
            foreach ($roomBookings as $key => $rb) {
                @unlink('assets/img/attachments/rooms/' . $rb->attachment);
                @unlink('assets/invoices/rooms/' . $rb->invoice);
                $rb->delete();
            }
        }

        if ($user->giveReviewForRoom()->count() > 0) {
            $user->giveReviewForRoom()->delete();
        }

        if ($user->bookTourPackage()->count() > 0) {
            $packageBookings = $user->bookTourPackage()->get();
            foreach ($packageBookings as $key => $pb) {
                @unlink('assets/img/attachments/packages/' . $pb->attachment);
                @unlink('assets/invoices/packages/' . $pb->invoice);
                $pb->delete();
            }
        }

        if ($user->giveReviewForPackage()->count() > 0) {
            $user->giveReviewForPackage()->delete();
        }

        @unlink('assets/img/users/' . $user->image);
        $user->delete();

        Session::flash('success', 'User deleted successfully!');
        return back();
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $user = User::findOrFail($id);

            if ($user->bookHotelRoom()->count() > 0) {
                $roomBookings = $user->bookHotelRoom()->get();
                foreach ($roomBookings as $key => $rb) {
                    @unlink('assets/img/attachments/rooms/' . $rb->attachment);
                    @unlink('assets/invoices/rooms/' . $rb->invoice);
                    $rb->delete();
                }
            }

            if ($user->giveReviewForRoom()->count() > 0) {
                $user->giveReviewForRoom()->delete();
            }

            if ($user->bookTourPackage()->count() > 0) {
                $packageBookings = $user->bookTourPackage()->get();
                foreach ($packageBookings as $key => $pb) {
                    @unlink('assets/img/attachments/packages/' . $pb->attachment);
                    @unlink('assets/invoices/packages/' . $pb->invoice);
                    $pb->delete();
                }
            }

            if ($user->giveReviewForPackage()->count() > 0) {
                $user->giveReviewForPackage()->delete();
            }

            @unlink('assets/img/users/' . $user->image);
            $user->delete();
        }

        Session::flash('success', 'Users deleted successfully!');
        return "success";
    }


    public function changePass($id) {
        $data['user'] = User::findOrFail($id);
        return view('backend.register_user.password', $data);
    }


    public function updatePassword(Request $request)
    {

        $messages = [
            'cpass.required' => 'Current password is required',
            'npass.required' => 'New password is required',
            'cfpass.required' => 'Confirm password is required',
        ];

        $request->validate([
            'cpass' => 'required',
            'npass' => 'required',
            'cfpass' => 'required',
        ], $messages);


        $user = User::findOrFail($request->user_id);
        if ($request->cpass) {
            if (Hash::check($request->cpass, $user->password)) {
                if ($request->npass == $request->cfpass) {
                    $input['password'] = Hash::make($request->npass);
                } else {
                    return back()->with('error', __('Confirm password does not match.'));
                }
            } else {
                return back()->with('error', __('Current password Does not match.'));
            }
        }

        $user->update($input);

        Session::flash('success', 'Password update for ' . $user->username);
        return back();
    }
}
