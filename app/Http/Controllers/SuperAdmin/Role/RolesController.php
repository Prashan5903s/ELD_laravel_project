<?php

namespace App\Http\Controllers\SuperAdmin\role;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NotifyNotification;

use function PHPUnit\Framework\isEmpty;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data['roles'] = Role::with('permissions')->get();

        return view('super-admin.roles.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['modules'] = Module::with('permissions')->get();
        return view('super-admin.roles.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validateWithBag('createRole',[
            'role_name' => ['required'],
        ]);

        $role = Role::create([
            'name' => $request->role_name,
            'slug' => Str::slug($request->role_name, '-'),
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

        return redirect(route('roles.index'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['role'] = Role::with('permissions')->find($id);
        $data['modules'] = Module::with('permissions')->get();

        if(!isset($data['role'])){
            return redirect(route('roles.index'));
        }

        return view('super-admin.roles.edit', $data);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $request->validate([
            'role_name' => ['required'],
        ]);

        $role = Role::find($id);

        if(!isset($role)){
            return redirect(route('roles.index'));
        }

        $role->update([
            'name' => $request->role_name,
            'slug' => Str::slug($request->role_name, '-'),
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
        return redirect(route('roles.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
