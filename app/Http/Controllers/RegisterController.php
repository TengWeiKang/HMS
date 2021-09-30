<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

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
            'username' => 'required|max:255|unique:customer,username|unique:employee,username',
            "passport" => "required|max:255",
            "firstName" => "required|max:255",
            "lastName" => "required|max:255",
            'email' => 'required|email|max:255|unique:customer,email|unique:employee,email',
            'phone' => 'required|regex:/^(\+6)?01[0-46-9]-[0-9]{7,8}$/|max:14',
            'password' => 'required|confirmed|min:8|max:255',
        ]);
        $passport = str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $request->passport);
        $customers = Customer::where("passport", "LIKE", $passport)->get();
        if ($customers->isEmpty()) {
            Customer::create([
                "username" => $request->username,
                "passport" => $request->passport,
                "first_name" => $request->firstName,
                "last_name" => $request->lastName,
                "email" => $request->email,
                "phone" => $request->phone,
                "password" => Hash::make($request->password)
            ]);
        }
        else if (!is_null($customers[0]->username)) {
            $this->validate($request, [
                "passport" => "unique:customer,passport"
            ]);
        }
        else {
            $customer = $customers[0];
            $customer->username = $request->username;
            $customer->first_name = $request->firstName;
            $customer->last_name = $request->lastName;
            $customer->email = $request->email;
            $customer->phone = $request->phone;
            $customer->password = Hash::make($request->password);
            $customer->save();
        }

        return redirect()->route('login')->with('message', 'Account registered successfully');
    }
}
