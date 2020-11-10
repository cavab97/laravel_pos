<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!empty(Auth::user()) && Auth::user()->is_admin == 1) {
            if (Auth::user()->status == 1) {
                return $next($request);
            } else {
                Auth::logout();
                Session::flush();
                return redirect()->route('admin.login')->with('error', 'Your account is not active. Please contact support team.');
            }
        }
        return redirect()->route('admin.login');
    }
}
