<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
class WebAuth
{
    
    


    public function handle($request, Closure $next)
    {
    	\Log::channel('custom')->info('web',[Auth::guard('web')->check()]);
    	\Log::channel('custom')->info('web',[Auth::user()]);
        if (!Auth::guard('web')->check() && !Auth::user())
        {
            return redirect('/');
        }
        return $next($request);
    }
}
