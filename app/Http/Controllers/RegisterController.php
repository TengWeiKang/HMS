<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function __construct() {
        $this->middleware("guest");
    }

    public function index() {
        return view("auth/register");
    }

    public function store(Request $request) {
        $this->validate($request, [
            'username' => 'required|max:255|unique:customers,username',
            'email' => 'required|email|max:255|unique:customers,email',
            'phone' => 'required|regex:/^(\+6)?01[0-46-9]-[0-9]{7,8}$/|max:14',
            'password' => 'required|confirmed|min:8|max:255',
        ]);

        Customers::create([
            "username" => $request->username,
            "email" => $request->email,
            "phone" => $request->phone,
            "password" => Hash::make($request->password)
        ]);

        return redirect()->route('customer.home')->with('message', 'Account registered successfully');
    }
}
