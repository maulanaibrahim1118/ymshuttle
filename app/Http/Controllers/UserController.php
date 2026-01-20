<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use App\Location;
use App\Helpers\Cleaner;
use App\Imports\UserImport;
use App\Helpers\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $data['title']= "User Lists";
        $data['breadcrumbs'] = [
            ['label' => 'Setting'],
            ['label' => 'User'],
        ];

        $data['locations'] = DB::table('locations')->orderBy('name', 'ASC')->get();
        $data['roles'] = DB::table('roles')->whereNotIn('name', ['super admin'])->orderBy('name', 'ASC')->get();
        
        $data['users'] = User::with(['location', 'role'])->get();
            
        return view('contents.setting.user.index', $data);
    }

    public function store(Request $request)
    {
        $username = Auth::user()->username;
        
        $validated = $request->validate([
            'username' => 'required|unique:users',
            'name' => 'required',
            'location_code' => 'required',
            'role' => 'required',
        ]);

        $cleaned = Cleaner::cleanAll($validated);

        $data = [
            'username' => strtolower($cleaned['username']),
            'name' => strtolower($cleaned['name']),
            'location_code' => $cleaned['location_code'],
            'password' => Hash::make(strtolower($cleaned['username'])),
            'is_active' => '1',
            'created_by' => $username,
            'updated_by' => $username,
        ];

        try {
            DB::beginTransaction();
            $user = User::create($data);
            $user->syncRoles($cleaned['role']);

            LogActivity::log('add-user', 'Successfully added user: '.$cleaned['username'], '', $username);
            DB::commit();
            return redirect()->back()->with('success', 'User successfully added!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error adding user: ' . $e->getMessage());
            LogActivity::log('add-user', 'Failed to add user: '.$cleaned['username'], $e->getMessage(), $username);
            return redirect()->back()->with('error', 'Failed to add user!');
        }
    }

    public function update(Request $request)
    {
        $id = decrypt($request->input('id'));
        $user = User::with('location', 'roles')->findOrFail($id);
        $username = Auth::user()->username;

        $rules = [
            'edit_name' => 'required',
            'edit_username' => 'required|unique:users,username,' . $id,
            'edit_location' => 'required|exists:locations,code',
            'edit_role' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            LogActivity::log('edit-user', 'Validation failed while editing user: ' . $user->username, json_encode($validator->errors()->all()), $username);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $cleaned = Cleaner::cleanAll($request->only([
            'edit_name',
            'edit_username',
            'edit_location',
            'edit_role',
        ]));

        $newData = [
            'name' => strtolower($cleaned['edit_name']),
            'username' => strtolower($cleaned['edit_username']),
            'location_code' => $cleaned['edit_location'],
            'updated_by' => $username,
            'updated_at' => now(),
        ];

        // Ambil data lama & role
        $oldLocationName = optional($user->location)->name;
        $oldRole = $user->roles->pluck('name')->first();
        $newRole = $cleaned['edit_role'];

        $newLocationName = Location::where('code', $newData['location_code'])->value('name');

        $changes = [];

        if ($user->name !== $newData['name']) {
            $changes[] = "Name changed from '{$user->name}' to '{$newData['name']}'";
        }

        if ($user->username !== $newData['username']) {
            $changes[] = "Username changed from '{$user->username}' to '{$newData['username']}'";
        }

        if ($oldLocationName !== $newLocationName) {
            $changes[] = "Location changed from '{$oldLocationName}' to '{$newLocationName}'";
        }

        if ($oldRole !== $newRole) {
            $changes[] = "Role changed from '{$oldRole}' to '{$newRole}'";
        }

        $logDetail = $changes ? implode(PHP_EOL, $changes) : 'No changes detected.';

        try {
            DB::beginTransaction();

            User::where('id', $id)->update($newData);
            $user->syncRoles($newRole);

            LogActivity::log('edit-user', "Successfully edited user: {$user->username}", $logDetail, $username);

            DB::commit();
            return redirect()->back()->with('success', 'User successfully updated!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error editing user: ' . $e->getMessage());
            LogActivity::log('edit-user', 'Failed to edit user: ' . $user->username, $e->getMessage(), $username);
            return redirect()->back()->with('error', 'Failed to update user.');
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            DB::beginTransaction();
            $result = UserImport::handle($request->file('import_file'));

            if ($result['status'] === 'fail') {
                DB::commit(); // Commit log error
                return redirect()->back()->with('error', 'Failed to import user!');
            }

            DB::commit();
            return redirect()->back()->with('success', 'Users successfully imported!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error import user: ' . $e->getMessage());
            LogActivity::log('import-user', 'Failed to import user', $e->getMessage(), Auth::user()->username);
            return redirect()->back()->with('error', 'Failed to import users!');
        }
    }

    public function resetPassword($id)
    {
        $id = decrypt($id);
        $username = Auth::user()->username;

        try {
            DB::beginTransaction();
            $user = User::findOrFail($id);
            
            $password = Hash::make($user->username);

            DB::table('users')->where('id', $id)->update([
                'password' => $password,
                'password_changed_at' => NULL,
                'updated_by' => $username,
                'updated_at' => now(),
            ]);
    
            LogActivity::log('reset-password-user', 'Successfully reset password user: '.$user->username, '', $username);

            DB::commit();
            return redirect()->back()->with('success', 'User password successfully reseted!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error reset password user: ' . $e->getMessage());
            LogActivity::log('reset-password-user', 'Failed to reset password user: '.$user->username, $e->getMessage(), $username);
            return redirect()->back()->with('error', 'Failed to reset password user!');
        }
    }

    public function deactivate($id)
    {
        $id = decrypt($id);
        $username = Auth::user()->username;

        try {
            DB::beginTransaction();
            $user = User::findOrFail($id);
            
            DB::table('users')->where('id', $id)->update([
                'is_active' => '0',
                'updated_by' => $username,
                'updated_at' => now(),
            ]);
    
            LogActivity::log('deactivate-user', 'Successfully deactivate user: '.$user->username, '', $username);

            DB::commit();
            return redirect()->back()->with('success', 'User successfully deactivated!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deactivate user: ' . $e->getMessage());
            LogActivity::log('deactivate-user', 'Failed to deactivate user: '.$user->username, $e->getMessage(), $username);
            return redirect()->back()->with('error', 'Failed to deactivate user!');
        }
    }

    public function activate($id)
    {
        $id = decrypt($id);
        $username = Auth::user()->username;

        try {
            DB::beginTransaction();
            $user = User::findOrFail($id);
            
            DB::table('users')->where('id', $id)->update([
                'is_active' => '1',
                'updated_by' => $username,
                'updated_at' => now(),
            ]);
    
            LogActivity::log('activate-user', 'Successfully activate user: '.$user->username, '', $username);

            DB::commit();
            return redirect()->back()->with('success', 'User successfully activated!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error activate user: ' . $e->getMessage());
            LogActivity::log('activate-user', 'Failed to activate user: '.$user->username, $e->getMessage(), $username);
            return redirect()->back()->with('error', 'Failed to activate user!');
        }
    }

    // Ajax Function
    public function checkUniqueUsername(Request $request)
    {
        $username = strtolower($request->query('username'));
        $id = decrypt($request->query('id'));
        $exists = User::where('username', $username)->where('id', '<>', $id)->exists();
        return response()->json(['unique' => !$exists]);
    }
}