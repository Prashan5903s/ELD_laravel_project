<?php

namespace App\Http\Controllers\SuperAdmin\language;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NotifyNotification;

class LanguageController extends Controller
{
    public function index(Request $request)
    {
        
        $user = User::where('user_type', "SA")->first();
        $request->session()->put('original_user_id', auth()->id());
        $request->session()->put('ids', $user->id);
        $request->session()->put('ut', 1);
        $request->session()->put('first', $user->first_name);
        $request->session()->put('last', $user->last_name);
        $request->session()->put('email', $user->email);
        $request->session()->put('avatar', $user->avatar_image);
        $request->session()->put('master_company_id', $user->master_company_id);
        $request->session()->put('master_id', $user->master_id);
        $request->session()->put('is_master', $user->is_master);

        $data = Language::all();

        return view('super-admin.language.index', compact("data"));

    }

    public function create()
    {

        return view('super-admin.language.create');

    }

    public function store(Request $request)
    {

        $rules = [
            'language_name' => 'required|string',
            'short_name' => 'required|string',
            'is_active' => 'required|string',
        ];

        $request->validate($rules);
        $language = new Language;
        $language->language_name = $request->language_name;
        $language->Short_name = $request->short_name;
        $language->is_active = $request->is_active;
        $language->save();

        $user = User::where('user_type', 'SA')->first();
        $message = "New language has been added " . Auth::user()->first_name . " " . Auth::user()->last_name;
        $url = route('white-label.dashboard');
        $notification = new NotifyNotification($message, $url);
        $user->notify($notification);

        $request->session()->flash('success', 'Language updated successfully.');

        return redirect()->route('language.index');

    }

    public function show(Request $request, $id)
    {

    }
    public function edit(request $request, $id)
    {
        
        $data = Language::find($id);
        
        if(!$data){
            $request->session()->flash('error', 'Language does not exist.');
            return redirect()->route('language.index');
        }
        
        return view('super-admin.language.edit', compact('data'));
    
        
    }
    
    public function update(Request $request, $id)
    {
        $rules = [
            'language_name' => 'required|string',
            'short_name' => 'required|string',
            'is_active' => 'required|string',
        ];

        $request->validate($rules);

        $lang = Language::find($id);
        
        if(!$lang){
           $request->session()->flash('error', 'Language does not exist.');
           return redirect()->route('language.index');
        }
        
        $lang->language_name = $request->language_name;
        $lang->Short_name = $request->short_name;
        $lang->is_active = $request->is_active;
        $lang->save();

        $user = User::where('user_type', 'SA')->first();
        $message = "New language has been edited " . Auth::user()->first_name . " " . Auth::user()->last_name;
        $url = route('white-label.dashboard');
        $notification = new NotifyNotification($message, $url);
        $user->notify($notification);

        $request->session()->flash('success', 'Language updated successfully.');

        return redirect()->route('language.index');
    }
}
