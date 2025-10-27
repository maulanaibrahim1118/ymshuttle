<?php

namespace App\Http\Controllers;

use Exception;
use App\Helpers\Cleaner;
use Illuminate\Support\Str;
use App\Helpers\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index()
    {
        $data['title']= "Role & Permission Lists";
        $data['breadcrumbs'] = [
            ['label' => 'Setting'],
            ['label' => 'Role & Permission'],
        ];

        $data['roles'] = DB::table('roles')->orderBy('name', 'ASC')->get();
        
        return view('contents.setting.role.index', $data);
    }

    public function store(Request $request)
    {
        $username = Auth::user()->username;

        $validated = $request->validate([
            'name' => 'required|unique:roles',
            'guard_name' => 'required',
        ]);

        // Bersihkan input menggunakan Cleaner
        $cleaned = Cleaner::cleanAll($request->only(['name', 'guard_name']));

        $data = [
            'name' => strtolower($cleaned['name']),
            'guard_name' => strtolower($cleaned['guard_name']),
        ];

        try {
            DB::beginTransaction();
            $role = Role::create($data);

            LogActivity::log('add-role', 'Successfully added role: ' . $role->name, '', $username);
            DB::commit();
            return redirect()->back()->with('success', 'Role successfully added!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error adding role: ' . $e->getMessage());
            LogActivity::log('add-role', 'Failed to add role: ' . ($role->name ?? $cleaned['name']), $e->getMessage(), $username);
            return redirect()->back()->with('error', 'Failed to add role!');
        }
    }

    public function update(Request $request)
    {
        $id = decrypt($request->input('id'));
        $role = Role::findOrFail($id);
        $username = Auth::user()->username;

        $rules = [
            'edit_name' => 'required|unique:roles,name,' . $id,
        ];

        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            LogActivity::log('edit-role', 'Validation failed while editing user: ' . $role->name, json_encode($validator->errors()->all()), $username);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $cleaned = Cleaner::cleanAll($request->only(['edit_name']));

        $data = [
            'name' => strtolower($cleaned['edit_name']),
        ];

        try {
            DB::beginTransaction();
            Role::where('id', $id)->update($data);

            LogActivity::log('edit-role', 'Successfully edited role: ' . $role->name, 'Name changed from '. $role->name . ' to ' . $cleaned['edit_name'], $username);
            DB::commit();
            return redirect()->back()->with('success', 'Role successfully edited!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error editing role: ' . $e->getMessage());
            LogActivity::log('edit-role', 'Failed to edit role: ' . $role->name, $e->getMessage(), $username);
            return redirect()->back()->with('error', 'Failed to edit role!');
        }
    }

    public function destroy($id)
    {
        $id = decrypt($id);
        $role = Role::findOrFail($id);
        $username = Auth::user()->username;

        $userCount = DB::table('model_has_roles')
            ->where('role_id', $id)
            ->count();

        if ($userCount > 0) {
            LogActivity::log('delete-role', 'Failed to delete role: '.$role->name, 'Role is being used by another user.', $username);
            return redirect()->back()->with('error', 'Role is being used by another user.');
        }

        try {
            DB::beginTransaction();
            DB::table('roles')
                ->where('id', $id)
                ->delete();
    
            LogActivity::log('delete-role', 'Successfully deleted role: '.$role->name, '', $username);
            DB::commit();
            return redirect()->back()->with('success', 'Role successfully deleted!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting role: ' . $e->getMessage());
            LogActivity::log('delete-role', 'Failed to delete role: '.$role->name, $e->getMessage(), $username);
            return redirect()->back()->with('error', 'Failed to delete role!');
        }
    }

    public function updatePermissions(Request $request, $encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $role = Role::findOrFail($id);
        $username = Auth::user()->username;

        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        LogActivity::log('edit-permission-role', 'Successfully edited permission role: '.$role->name, '', $username);

        return redirect()->back()->with('success', 'Permissions edited successfully.');
    }


    // Ajax Function
    public function checkUniqueName(Request $request)
    {
        $name = strtolower($request->query('name'));
        $id = decrypt($request->query('id'));
        $exists = Role::where('name', $name)->where('id', '<>', $id)->exists();
        return response()->json(['unique' => !$exists]);
    }
    
    public function getPermissions($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $role = Role::with('permissions')->findOrFail($id);

            $grouped = $role->permissions->sortBy('name')->groupBy(function ($permission) {
                if (Str::contains($permission->name, '-')) {
                    return ucwords(Str::before($permission->name, '-'));
                }
                return 'Other';
            })->map(function ($permissions) {
                return $permissions->map(function ($permission) {
                    return [
                        'name' => str_replace('-', ' ', Str::after($permission->name, '-')),
                    ];
                });
            });

            return response()->json($grouped->toArray());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getEditPermissions($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $role = Role::findOrFail($id);
        $assignedPermissions = $role->permissions->pluck('id')->toArray();

        $permissions = Permission::all()->sortBy('name')->groupBy(function ($permission) {
            return explode('-', ucwords($permission->name))[0];
        })->map(function ($group) use ($assignedPermissions) {
            return $group->map(function ($permission) use ($assignedPermissions) {
                $prefix = explode('-', $permission->name)[0];
                $action = str_replace('-', ' ', Str::after($permission->name, "{$prefix}-"));

                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'action' => $action,
                    'assigned' => in_array($permission->id, $assignedPermissions),
                ];
            });
        });

        return response()->json($permissions);
    }
}