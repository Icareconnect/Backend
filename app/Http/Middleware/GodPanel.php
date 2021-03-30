<?php

namespace App\Http\Middleware;

class GodPanel 
{
    public function handle($request, $next)
    {
        
        //store parameter for later use
        return $next($request);
    }
}