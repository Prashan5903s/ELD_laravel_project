<?php

namespace App\Http\Controllers\Transport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ErrorLog;
use App\Models\Language;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class ErrorController extends Controller
{
    public static function error_list(Request $request)
    {
        $lang = $request->route('lang');

        if (empty($lang)) {
            return redirect()->route('transport.dashboard', ['en']);
        }

        $language = Language::where('Short_name', $lang)->first();

        if (!$language) {
            App::setLocale('en');
            return redirect()->route('transport.dashboard', ['en']);
        }

        App::setLocale($lang);

        $driverIds = User::where('master_id', Auth::user()->id)
            ->pluck('id');

        $errorLog = ErrorLog::whereIn('created_by', $driverIds)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('transport.report.error', compact('errorLog'));
    }
}
