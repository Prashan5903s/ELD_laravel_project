<?php

namespace App\Http\Controllers\SuperAdmin\permission;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NotifyNotification;

class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['permissions'] = Permission::with(['roles', 'module'])->get();
        $data['modules'] = Module::select('id', 'name')->get();
        return view('super-admin.permissions.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['permissions'] = Permission::with('roles')->get();
        return view('super-admin.permissions.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'permission_name' => ['required'],
            'module_id' => ['required'],
        ]);
        // dd($request->all());

        Permission::create([
            'name' => $request->permission_name,
            'slug' => Str::slug($request->permission_name, '-'),
            'module_id' => $request->module_id,
        ]);

        $user = User::where('user_type', 'SA')->first();
        $message = "New permission has been added " . Auth::user()->first_name . " " . Auth::user()->last_name;
        $url = route('white-label.dashboard');
        $notification = new NotifyNotification($message, $url);
        $user->notify($notification);

        $request->session()->flash('success', 'Permission created successfully');

        return redirect(route('permissions.index'));
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
    public function edit(Request $request, $id)
    {
        $data['permission'] = Permission::find($id);
        $data['modules'] = Module::select('id', 'name')->get();
        if(!isset($data['permission'])){
            $request->session()->flash('error', 'Permission not found');
            return redirect(route('permissions.index'));
        }
        return view('super-admin.permissions.edit', $data);
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
            'permission_name' => ['required'],
            'module_id' => ['required'],
        ]);

        $permission = Permission::find($id);
        if(!isset($permission)){
            $request->session()->flash('error', 'Permission not found');
            return redirect(route('permissions.index'));
        }

        $permission->update([
            'name' => $request->permission_name,
            'slug' => Str::slug($request->permission_name, '-'),
            'module_id' => $request->module_id,
        ]);
        
        $user = User::where('user_type', 'SA')->first();
        $message = "Permission has been edited " . Auth::user()->first_name . " " . Auth::user()->last_name;
        $url = route('white-label.dashboard');
        $notification = new NotifyNotification($message, $url);
        $user->notify($notification);

        $request->session()->flash('success', 'Permission updated successfully');
        return redirect(route('permissions.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = Permission::find($id);
        if(!isset($permission)){
            return response()->json(['error' => 'Permission not deleted.']);
        }

        $permission->delete();
        return response()->json(['success' => 'Permission deleted successfully.']);
    }

}
