<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            if (Auth::user()->hasrole('admin') || Auth::user()->hasrole('super_admin')) { 
                return redirect('/admin/dashboard');
            }
            if (Auth::user()->hasrole('godadmin')) { 
                return redirect('/godpanel/dashboard');
            }
            if (Auth::user()->hasrole('customer') || Auth::user()->hasrole('service_provider')) {
                
                return redirect('/');
            }
            return redirect(RouteServiceProvider::HOME);
        }

        return $next($request);
    }
}
