<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EmployeeChangePasswordController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        return view('dashboard/profile/change-password');
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
            'password' => [new MatchOldPassword],
            'newPassword' => 'required|confirmed|min:8|max:255',
        ]);
        $user = Auth::guard('employee')->user();
        $user->forceFill(['password'=> Hash::make($request->newPassword)])->setRememberToken(Str::random(60));
        $user->save();

        return redirect()->route("dashboard.profile.password")->with("message", "Your password has been updated successfully");
    }
}
