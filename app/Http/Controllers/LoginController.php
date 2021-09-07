<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function index() {
        if (Auth::guard()->check()) {
            return redirect()->route('customer.home');
        }
        else if (Auth::guard("employee")->check()) {
            if (request()->has("redirect") && Str::startsWith(request()->redirect, 'http://localhost:8000/dashboard'))
                return redirect(request()->redirect);
            return redirect()->route('dashboard.home');
        }
        if (request()->redirect) {
            Session::put("url.intended", request()->redirect);
        }
        return view("auth/login");
    }

    public function store(Request $request) {
        $this->validate($request, [
            "username" => "required|max:255",
            "password" => "required"
        ]);

        if (Auth::guard()->attempt($request->only('username', 'password'), $request->remember)) {
            return redirect()->route('customer.home');
        }

        if (Auth::guard('employee')->attempt($request->only('username', 'password'), $request->remember)) {
            $url = redirect()->intended()->getTargetUrl();
            if (Str::startsWith($url, 'http://localhost:8000/dashboard')) {
                Session::forget("url.intended");

                return redirect($url);
            }
            return redirect()->route('dashboard.home');
        }

        return redirect()->route("login")->withInput()->with("status", "Invalid login details");
    }
}
