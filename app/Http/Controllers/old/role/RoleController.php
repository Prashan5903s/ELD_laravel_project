<?php

namespace App\Http\Controllers\role;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $role = Role::all();
        return view('role.index', compact('role'));
    }
    public function add()
    {
        return view('role.add');
    }
    public function addForm(Request $request)
    {
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'status' => 'required|in:active,inactive',
        ];

        // Custom error messages
        $messages = [
            'status.in' => 'The status must be either "active" or "inactive".',
        ];

        // Validate the request data
        $request->validate($rules, $messages);

        // If validation passes, proceed to save data
        $role = new Role;
        $role->name = $request->name;
        $role->description = $request->description;
        $role->status = $request->status;
        $role->save();

        return redirect('role');

    }
    public function edit(Request $request, $id)
    {
        $role = Role::find($id);
        return view('role.edit', compact('role'));

    }
    public function editForm(Request $request, $id)
    {
        $role = Role::find($id);
        $role->name = $request->name;
        $role->description = $request->description;
        $role->status = $request->status;
        $role->save();
        return redirect('role');
    }
    public function delete(Request $request, $id)
    {
        $role = Role::find($id);
        $role->delete();
        return redirect('role');
    }
}
