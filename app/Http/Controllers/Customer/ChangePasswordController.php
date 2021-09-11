<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Rules\MatchCustomerOldPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ChangePasswordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->middleware("customer");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        return view('customer.profile.change-password');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'password' => [new MatchCustomerOldPassword],
            'newPassword' => 'required|confirmed|min:8|max:255',
        ]);
        $user = Auth::user();
        $user->forceFill(['password'=> Hash::make($request->newPassword)])->setRememberToken(Str::random(60));
        $user->save();

        return redirect()->route("customer.profile.password")->with("message", "Your password has been updated successfully");
    }
}
