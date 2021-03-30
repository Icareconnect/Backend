<?php 

namespace App\Http\Middleware;
use Closure;  
use Illuminate\Contracts\Routing\TerminableMiddleware;  
use Illuminate\Support\Facades\Log;

class LogAfterRequest
{

    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
    	// Log::useDailyFiles(storage_path().'/logs/apidaily.log');
    	Log::channel('custom')->info('app.requests', ['request_url'=>url()->current(),'request' => $request->all(), 'response' => $response]);
    }

}