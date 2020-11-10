<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Front
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!empty(Auth::guard('fronts')->user()) && (Auth::guard('fronts')->user()->role == 2)) {
            return $next($request);
        } elseif(!empty(Auth::user()) && Auth::user()->role != 2){
            return redirect()->route('admin.login');
        }
        return redirect()->route('home');

        /*if (!empty(auth()->user()) && auth()->user()->role != 2) {
            return redirect()->route('admin.home');
        } else if (!empty(auth()->user()) && auth()->user()->role == 2) {
            return $next($request);
        } else {
            return redirect()->route('home');
        }*/

    }
}
