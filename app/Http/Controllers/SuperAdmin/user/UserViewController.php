<?php

namespace App\Http\Controllers\SuperAdmin\user;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserViewController extends Controller
{
    public function index()
    {
        $user = User::find(4);
        $users = $user->descendants()->get();
        return view('super-admin.user.view', compact('users'));
    }

}
