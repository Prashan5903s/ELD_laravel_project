<?php

namespace App\Http\Controllers\SuperAdmin\Currency;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\User;
use App\Notifications\NotifyNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CurrenciesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['currencies'] = Currency::all();
        return view('super-admin.currency.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'name'         => ['required'],
            'country_code' => ['required'],
            'code'         => ['required'],
            'symbol'       => ['required'],
        ]);

        Currency::create([
            'name' => $request->name,
            'country_code' => $request->country_code,
            'code' => $request->code,
            'symbol' => $request->symbol,
        ]);

        $user = User::where('user_type', 'SA')->first();
        $message = "New currency has been added " . Auth::user()->first_name . " " . Auth::user()->last_name;
        $url = route('white-label.dashboard');
        $notification = new NotifyNotification($message, $url);
        $user->notify($notification);

        return response()->json(['success' => 'Currency added successfully']);
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
        //
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
            'name'         => ['required'],
            'country_code' => ['required'],
            'code'         => ['required'],
            'symbol'       => ['required'],
        ]);

        $currency = Currency::find($id);

        if(!isset($currency)){
            return response()->json(['error', 'Currency not found']);
        }

        $currency->update([
            'name' => $request->name,
            'country_code' => $request->country_code,
            'code' => $request->code,
            'symbol' => $request->symbol,
        ]);

        $user = User::where('user_type', 'SA')->first();
        $message = "New currency has been edited " . Auth::user()->first_name . " " . Auth::user()->last_name;
        $url = route('white-label.dashboard');
        $notification = new NotifyNotification($message, $url);
        $user->notify($notification);

        return response()->json(['success' => 'Currency updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $currency = Currency::find($id);

        if(!isset($currency)){
            return response()->json(['error' => 'Currency not found.']);
        }

        if($currency->status == "0"){

            $currency->update([
                'status' => '1',
            ]);

            return response()->json(['success' => 'Currency Activated successfully.']);

        }else{

            $currency->update([
                'status' => '0',
            ]);

            return response()->json(['success' => 'Currency De-activated successfully.']);
        }
    }
}
