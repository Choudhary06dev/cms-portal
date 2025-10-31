<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function profile()
    {
        $user = Auth::guard('frontend')->user();
        return view('frontend.account.profile', compact('user'));
    }

    public function settings()
    {
        $user = Auth::guard('frontend')->user();
        return view('frontend.account.settings', compact('user'));
    }
}


