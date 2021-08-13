<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Employee
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::guard('employee')->check())
            return abort(404);
        if (empty($roles))
            return $next($request);
        $role = Auth::guard('employee')->user()->role();
        foreach ($roles as $key => $r) {
            if (Str::lower($role) == $r)
                return $next($request);
        }
        return abort(404);
    }
}
