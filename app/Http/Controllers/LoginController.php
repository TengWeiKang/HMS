<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct() {
        $this->middleware("guest");
    }

    public function index() {
        return view("auth/login");
    }

    public function store(Request $request) {
        $this->validate($request, [
            "username" => "required|max:255",
            "password" => "required"
        ]);

        if (Auth::guard('customer')->attempt($request->only('username', 'password'), $request->remember)) {
            return redirect()->route('customer.home');
        }

        return redirect()->route("login")->withInput()->with("status", "Invalid login details");
    }
}
