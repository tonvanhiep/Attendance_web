<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExistedLoginMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            if (Auth::user()->fl_admin == 1 ) {
                return redirect()->route('admin.dashboard');
            }
            if (Auth::user()->fl_admin == 0 || Auth::user()->fl_admin == 1) {
                return redirect()->route('user.index');
            }
        }
        return $next($request);
    }
}
