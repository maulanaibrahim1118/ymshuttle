<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use App\Helpers\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function index()
    {
        $data['title'] = "Account Setting";
        $data['breadcrumbs'] = [
            ['label' => 'Account Setting'],
        ];

        $userId = auth()->user()->id;
        $data['user'] = User::find($userId);

        return view('contents.accountSetting', $data);
    }
    
    public function changePassword(Request $request)
    {
        $username = Auth::user()->username;

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            LogActivity::log('change-password', 'Failed to change password', 'Current Password is incorrect!', $username);
            return redirect()->back()->with('error', 'Current Password is incorrect!');
        }

        $messages = [
            'current_password.required' => 'Current Password required!',
            'new_password.required' => 'New Password required!',
            'new_password.confirmed' => 'Confirm password does not match!',
        ];

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|confirmed',
        ], $messages);

        if ($request->current_password == $request->new_password) {
            LogActivity::log('change-password', 'Failed to change password', 'New password must be different from current password.', $username);
            return redirect()->back()->with('error', 'New password must be different from current password.');
        }

        DB::beginTransaction();

        try {
            Auth::user()->update([
                'password' => Hash::make($request->new_password),
                'password_changed_at' => now(),
                'updated_by' => $username,
                'updated_at' => now(),
            ]);

            LogActivity::log('change-password', 'Successfully changed password', '', $username);

            DB::commit();
            return redirect()->route('home')->with('success', 'Password successfully changed!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error change password: ' . $e->getMessage());
            LogActivity::log('change-password', 'Failed to change password', $e->getMessage(), $username);
            return redirect()->back()->with('error', 'Failed to change password!');
        }
    }
}