<?php

namespace App\Http\Middleware;

use Closure;


use Cookie;
use Config;
use DB;
use App\User;
use Auth;
use Session;
class DataBaseConnectionDynamicWeb
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
        Config::set("default",true);
        $database_name = 'intely';
        $client = DB::connection('godpanel')->table('clients')->where('domain_name','=',$database_name)->first();
        if($client){
            if($client->db_id!=="default"){
                Config::set("default",false);
                }
            $client->payment_type = 'stripe';
            $client->gateway_key = '';
            $client->gateway_secret = '';
            $client_features = [];
            $builds = (object)[];
            Config::set("client_id", $client->id);
            Config::set("client_connected",true);
            Config::set("client_data",$client);
            $builds->ios_url = \App\Helpers\Helper::getClientFeatureKeys('Build Urls','IOS Url');
            $builds->android_url = \App\Helpers\Helper::getClientFeatureKeys('Build Urls','Android Url');
            $client_feature_type = \App\Model\GodPanel\ClientFeature::where('client_id',$client->id)->pluck('feature_id')->toArray();
            if($client_feature_type){
                $client_features = \App\Model\GodPanel\Feature::whereIn('id',$client_feature_type)->groupBy('feature_type_id')->get();
            }
            Config::set("client_features",$client_features);
            Config::set("builds",$builds);
        }
        return $next($request);
    }
}
