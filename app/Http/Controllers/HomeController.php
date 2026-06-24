<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            if (Auth::user()->user_type == "SA") {
                return redirect('admin/dashboard');
            }
            if (Auth::user()->user_type == "WC") {
                return redirect('white-label/dashboard');
            }
        } else {
            return redirect('login');
        }
    }

    public function conn(Request $request)
    {
        $user = User::find(4);
        $descendants = $user->descendants()->get();
        echo json_encode($descendants);
        exit();
    }
}
