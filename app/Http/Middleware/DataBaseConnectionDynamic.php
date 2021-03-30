<?php

namespace App\Http\Middleware;

use Closure;


use Cookie;
use Config;
use DB;
use App\User;
use Auth;
use Session;
class DataBaseConnectionDynamic
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
      $client = DB::connection('godpanel')->table('clients')->where('domain_name','intely')->first();
        $client->payment_type = 'stripe';
        $client->gateway_key = '';
        $client->gateway_secret = '';
       $client_features = DB::connection('godpanel')->table('godpanel_client_features')->where('client_id',$client->id)->pluck('feature_id')->toArray();
        $database_name = 'db_'.$client->db_id;
        $default = [
            'driver' => env('DB_CONNECTION','mysql'),
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT'),
            'database' => $database_name,
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null
        ];
        $builds = (object)[];
        $builds->ios_url = \App\Helpers\Helper::getClientFeatureKeys('Build Urls','IOS Url');
        $builds->android_url = \App\Helpers\Helper::getClientFeatureKeys('Build Urls','Android Url');
        Config::set("builds",$builds);

        Config::set("database.connections.$database_name", $default);
        Config::set("client_features", $client_features);
        Config::set("client_id", $client->id);
        Config::set("client_connected",true);
        Config::set("client_data",$client);
        DB::setDefaultConnection($database_name);
        DB::purge($database_name);
        return $next($request);
    }
}
