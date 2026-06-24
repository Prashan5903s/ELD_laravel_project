<?php

namespace App\Http\Controllers\SuperAdmin\role;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NotifyNotification;

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
        $role = new Role;
        $role->name = $request->name;
        $role->description = $request->description;
        $role->status = $request->status;
        $role->save();

        $user = User::where('user_type', 'SA')->first();
        $message = "New role has been added " . Auth::user()->first_name . " " . Auth::user()->last_name;
        $url = route('white-label.dashboard');
        $notification = new NotifyNotification($message, $url);
        $user->notify($notification);

        $request->session()->flash('success', 'Role updated successfully.');

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

        $user = User::where('user_type', 'SA')->first();
        $message = "New role has been edited " . Auth::user()->first_name . " " . Auth::user()->last_name;
        $url = route('white-label.dashboard');
        $notification = new NotifyNotification($message, $url);
        $user->notify($notification);

        $request->session()->flash('success', 'Role updated successfully.');

        return redirect('role');
    }
    public function delete(Request $request, $id)
    {
        $role = Role::find($id);
        $role->delete();
        return redirect('role');
    }

    public function rolesListing()
    {
        return view('roles.index');
    }
}
