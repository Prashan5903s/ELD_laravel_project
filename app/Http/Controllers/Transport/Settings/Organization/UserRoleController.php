<?php

namespace App\Http\Controllers\Transport\Settings\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\Module;
use App\Models\User;
use App\Models\Language;
use App\Notifications\NotifyNotification;

class UserRoleController extends Controller
{
    public function index(Request $request, $lang)
    {
        if (empty($lang)) {
            return redirect()->route('transport.dashboard', ['lang' => 'en']);
        }

        $language = Language::where('Short_name', $lang)->first();

        if (!$language) {
            App::setLocale('en');
            return redirect()->route('transport.dashboard', ['lang' => 'en']);
        } else {
            App::setLocale($lang);
        }


        $data['roles'] = Role::with('permissions')
        ->where(function ($query) {
            $query->where('master_id', 0)
                  ->orWhere('master_id', Auth::user()->master_id);
        })->get();

        return view('transport.settings.organization.user_role.index',$data);

    }

    public function create(Request $request, $lang)
    {
        if (empty($lang)) {
            return redirect()->route('transport.dashboard', ['lang' => 'en']);
        }

        $language = Language::where('Short_name', $lang)->first();

        if (!$language) {
            App::setLocale('en');
            return redirect()->route('transport.dashboard', ['lang' => 'en']);
        } else {
            App::setLocale($lang);
        }
        if (empty($lang)) {
            return redirect()->route('transport.dashboard', ['lang' => 'en']);
        }

        $language = Language::where('Short_name', $lang)->first();

        if (!$language) {
            App::setLocale('en');
            return redirect()->route('transport.dashboard', ['lang' => 'en']);
        } else {
            App::setLocale($lang);
        }
        $data['modules'] = Module::with('permissions')->get();
        return view('transport.settings.organization.user_role.create', $data);
    }

    public function store(Request $request, $lang)
    {
        if (empty($lang)) {
            return redirect()->route('transport.dashboard', ['lang' => 'en']);
        }

        $language = Language::where('Short_name', $lang)->first();

        if (!$language) {
            App::setLocale('en');
            return redirect()->route('transport.dashboard', ['lang' => 'en']);
        } else {
            App::setLocale($lang);
        }

        $rules = [
            'name' => 'required|string|unique:roles,name',
        ];

        // Validate the request
        $request->validate($rules);

        $role = Role::create([
            'name' => $request->name,
            'master_id' => Auth::user()->master_id,
            'master_company_id' => Auth::user()->master_company_id,
            'slug' => Str::slug($request->role_name, '-'),
            'Status' => 1,
        ]);

        if(isset($request->permissions) && count($request->permissions) > 0){
            foreach($request->permissions as $permission){
                RolePermission::create([
                    'role_id' => $role->id,
                    'permission_id' => $permission,
                ]);
            }
        }

        $user = User::where('user_type', 'SA')->first();
        $message = "New role has been added " . Auth::user()->first_name . " " . Auth::user()->last_name;
        $url = route('white-label.dashboard');
        $notification = new NotifyNotification($message, $url);
        $user->notify($notification);

        $request->session()->flash('success', 'Role created successfully.');

        return redirect()->route('settings.organisation.userRoles.index', [request()->lang]);

    }

    public function edit(Request $request, $lang, $id)
    {

        if (empty($lang)) {

            return redirect()->route('transport.dashboard', ['lang' => 'en']);

        }

        $language = Language::where('Short_name', $lang)->first();

        if (!$language) {
            App::setLocale('en');
            return redirect()->route('transport.dashboard', ['lang' => 'en']);
        } else {
            App::setLocale($lang);
        }

        $role = Role::find($id);
        if(!$role){
            $request->session()->flash('error', 'Unauthorised access');
            return redirect()->route('settings.organisation.userRoles.index', [$lang]);
        }
        if ($role->master_id != Auth::user()->master_id) {
            $request->session()->flash('error', 'Unauthorised access');
            return redirect()->route('settings.organisation.userRoles.index', [$lang]);
        }
        $data['role'] = Role::with('permissions')->find($id);
        $data['modules'] = Module::with('permissions')->get();

        if(!isset($data['role'])){
            return redirect(route('roles.index'));
        }
        return view('transport.settings.organization.user_role.edit', $data);

    }
    public function update(Request $request, $lang, $id)
    {
        // Check and set the language
        if (empty($lang)) {
            return redirect()->route('transport.dashboard', ['lang' => 'en']);
        }

        $language = Language::where('Short_name', $lang)->first();

        if (!$language) {
            App::setLocale('en');
            return redirect()->route('transport.dashboard', ['lang' => 'en']);
        } else {
            App::setLocale($lang);
        }

        // Find the role
        $role = Role::find($id);
        if(!$role){
            $request->session()->flash('error', 'Unauthorised access');
            return redirect()->route('settings.organisation.userRoles.index', [$lang]);
        }
        if ($role->master_id != Auth::user()->master_id) {
            $request->session()->flash('error', 'Unauthorised access');
            return redirect()->route('settings.organisation.userRoles.index', [$lang]);
        }
        // Validation rules
        $rules = [
            'name' => 'required|string|unique:roles,name,' . $role->id,
        ];

        // Validate the request
        $request->validate($rules);

        // Update the role
        $role->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'master_id' => Auth::user()->master_id,
            'master_company_id' => Auth::user()->master_company_id,
            'status' => 1,
        ]);

        if(isset($request->permissions) && count($request->permissions) > 0){
            RolePermission::where('role_id',$id)->delete();
            foreach($request->permissions as $permission){
                RolePermission::create([
                    'role_id' => $id,
                    'permission_id' => $permission,
                ]);
            }
        }

        $user = User::where('user_type', 'SA')->first();
        $message = "New role has been edited " . Auth::user()->first_name . " " . Auth::user()->last_name;
        $url = route('white-label.dashboard');
        $notification = new NotifyNotification($message, $url);
        $user->notify($notification);

        $request->session()->flash('success', 'Role updated successfully.');

        return redirect()->route('settings.organisation.userRoles.index', ['lang' => $request->lang]);
    }

}
