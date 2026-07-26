<?php

namespace App\Http\Controllers\API\Transport\Settings\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Support\Str;
use App\Models\Module;
use Illuminate\Support\Facades\Auth;

class UserRoleAPIController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $data['roles'] = Role::with(['permissions', 'users'])
                ->where(function ($query) {
                    $query->where('master_id', 0)
                        ->orWhere('master_id', Auth::user()->id);
                })->get();

            return response()->json($data);
        }

        return response()->json(['error' => 'Not authenticated'], 401);

    }

    public function create()
    {

        $data['modules'] = Module::with('permissions')->get();

        return response()->json($data);

    }

    public function store(Request $request)
    {

        $role = Role::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
            'master_id' => Auth::user()->id,
            'master_company_id' => Auth::user()->master_company_id,
        ]);

        if (isset($request->modules) && count($request->modules) > 0) {
            foreach ($request->modules as $modules) {
                if (isset($modules['permissions']) && count($modules['permissions']) > 0) {
                    foreach ($modules['permissions'] as $value) {
                        RolePermission::create([
                            'role_id' => $role->id,
                            'permission_id' => $value['id'],
                        ]);
                    }
                }
            }
        }

        return response()->json(["success" => "Added successfully"], 200);

    }
    public function check_role($id)
    {

        $role = Role::find($id);

        if ($role) {

            if ($role->master_id == 0) {
                return response()->json(false);
            }

            return response()->json(true);

        }

        return response()->json(false);
    }

    public function edit(Request $request, $id)
    {
        $data['role'] = Role::with('permissions')->find($id);
        $data['modules'] = Module::with('permissions')->get();

        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $role = Role::find($id);

        $role->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if (isset($request->modules) && count($request->modules) > 0) {
            RolePermission::where('role_id', $id)->delete();
            foreach ($request->modules as $modules) {
                if (isset($modules['permissions']) && count($modules['permissions']) > 0) {
                    foreach ($modules['permissions'] as $value) {
                        RolePermission::create([
                            'role_id' => $role->id,
                            'permission_id' => $value['id'],
                        ]);
                    }
                }
            }
        }

        return response()->json(["success" => "Added successfully"], 200);

    }
}
