<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function index()
    {
        auth()->logout();
        auth("employee")->logout();
        return redirect()->route("login");
    }
}
