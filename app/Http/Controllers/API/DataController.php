<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\AppVersion;
use App\Model\AppDetail;
use App\Model\EnableService;
use App\Model\CustomInfo;
use App\Model\Currency;
use App\Model\MasterPreference;
use App\Model\UserMasterPreference;
use App\Model\MasterPreferencesOption;
use App\Model\Country,App\Model\FeedFavorite;
use App\Model\Page;
use App\User;
use App\Model\Covid19;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Model\GodPanel\Config as GodConfig;
use DB,Carbon\Carbon;
use DateTime,DateTimeZone;
class DataController extends Controller
{
    /**
     * @SWG\Post(
     *     path="/appversion",
     *     description="appVersion",
     * tags={"App Version"},
     *  @SWG\Parameter(
     *         name="app_type",
     *         in="query",
     *         type="number",
     *         description="app_type 1: User App, 2: Doctor App",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="current_version",
     *         in="query",
     *         type="number",
     *         description="current_version start 1 Android and 100 start for IOS ",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="device_type",
     *         in="query",
     *         type="number",
     *         description="device_type 1: iOS, 2: Android",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="update_type 0 for no-update, 1 for minor update,2 for major update",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     */
    public static function appVersion(Request $request){  
        try{
        	 //inputs
            //validation rules
            $input = $request->all();
            $rules = array(
                        'app_type'=>'required|integer|between:1,2',
                        'current_version'=>'required|integer',
                        'device_type'=>'required|integer|between:1,2'
                    );
            //validate input
            $validation = \Validator::make($input,$rules);            
            if($validation->fails()){
                return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                    $validation->getMessageBag()->first()), 400);
            }
            $data = [];
            $data['update_type'] = 0;
            $current_live = AppVersion::select(
                                    'version',
                                    'update_type',
                                    'version_name'
                                )
                                ->where('app_type',$input['app_type'])
                                ->where('device_type',$input['device_type'])
                                ->latest()
                                ->first();
            if($current_live){
            	$data['version_name'] = $current_live->version_name;
            	if($current_live->version > $input['current_version']){
	            	$data['update_type'] = $current_live->update_type;
	            }elseif($current_live->version == $input['current_version']){
	                $data['update_type'] =  0;
	            }
            	$data['current_version'] = $current_live->version;
            }
            $data['currency_code'] = 'INR';
            $data['jitsi_id'] = 202001;
            if($input['app_type']=='2'){
                $data['applogo'] = 'de88f954a66ed01278dc04f28e7d0e96_royoexpert.png';
            }else{
                $data['applogo'] = 'bb1407d131d60a18f151c0efafca1197_royouser.png';
            }
            $services = EnableService::select('type','value')->get();
            foreach ($services as $key => $service) {
            	if($service->value=='twillio')
            		$service->value = 'twilio';
            	if($service->type=='vendor_approved')
            		$service->type = 'vendor_auto_approved';
            	$data[$service->type] = $service->value;
            }
            return response(array('status' => "success", 'statuscode' => 200, 'message' =>__('appVersion'),'data'=>$data), 200); 
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        } 
    }


    /**
     * @SWG\Get(
     *     path="/countrydata",
     *     description=" Country data",
     * tags={"App Country Data"},
     *  @SWG\Parameter(
     *         name="type",
     *         in="query",
     *         type="string",
     *         description="type country,state,city",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="country_id",
     *         in="query",
     *         type="string",
     *         description="Country Id",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="state_id",
     *         in="query",
     *         type="string",
     *         description="State Id",
     *         required=false,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     */
    public function getCountryStateCity(Request $request){  
        try{
             //inputs
            //validation rules
            $input = $request->all();
            $rules = array(
                        'type'=>'required|string|in:country,state,city'
                    );
            if(isset($input['type']) && $input['type'] =='state'){
               $rules['country_id'] = 'required'; 
            }elseif (isset($input['type']) && $input['type']=='city') {
               $rules['state_id'] = 'required'; 
            }
            //validate input
            $validation = \Validator::make($input,$rules);            
            if($validation->fails()){
                return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                    $validation->getMessageBag()->first()), 400);
            }
            $data = [];
            $data['type'] = $input['type'];
            if($input['type']=='country'){
                $data['country'] = Country::select('id','sortname','name')->orderBy('name','ASC')->get();
            }elseif ($input['type']=='state') {
                $data['state'] = \DB::table('states')
                ->select('id','name')
                ->where('country_id',$input['country_id'])
                ->whereNotIn('name',["Byram","Cokato","District of Columbia","Lowa","Medfield","New Jersy","Ontario","Ramey","Sublimity","Trimble"])
                ->orderBy('name','ASC')
                ->get();
            }elseif ($input['type']=='city') {
                $data['city'] = \DB::table('cities')
                ->select('id','name')
                ->where('state_id',$input['state_id'])
                ->orderBy('name','ASC')
                ->get();
            }
            return response(array('status' => "success", 'statuscode' => 200, 'message' =>__('Data'),'data'=>$data), 200); 
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        } 
    }

    public function getClientDetail(Request $request){
        // $client = new \Predis\Client();
        // $client->flushAll();
        // $client->delete($keys);
        $app_id = 'fc3eda974104a85c07d59108190ad6056';
        $data = ['client_features'=>[]];
        $input = $request->all();
        $cache_enable = false;
        $cache = GodConfig::where(['key_name'=>'Cache Enable'])->first();
        if($cache && $cache->key_value=='1'){
            $cache_enable = true;
        }
        if (Redis::exists('client_feature_'.$app_id) && $cache_enable) {
            $data = json_decode(Redis::get('client_feature_'.$app_id),true);
            $data['CacheEnable'] = $cache_enable; 
            $data['FromCache'] = true; 
        } else {
            $data = $this->getClientDetailFromDB($app_id);
            $data['FromCache'] = false; 
            $data['CacheEnable'] = $cache_enable; 
        }
        $data['applogo'] = null;
        if(isset($input['app_type'])){
            if($input['app_type']=='2'){
                $data['applogo'] = 'de88f954a66ed01278dc04f28e7d0e96_royoexpert.png';
            }else{
                $data['applogo'] = 'bb1407d131d60a18f151c0efafca1197_royouser.png';
            }
        }
        $appdetail = AppDetail::first();
        $data['background_color'] = '#000000';
        if($appdetail){
            $data['applogo'] = $appdetail['user_side_logo'];
            $data['background_color'] = $appdetail['background_color'];
        }
        if(isset($data['domain']) && $data['domain']=='healtcaremydoctor'){
            $data['applogo'] = '54c133d30aa7e913960a3369ac3a5695_healtcaremydoctor.png';
        }
        if(Auth::guard('api')->check()){
            $data['isApproved'] = Auth::guard('api')->user()->account_verified;
            $data['account_active'] = Auth::guard('api')->user()->account_active;
            $data['account_rejected'] = Auth::guard('api')->user()->account_rejected;
            $data['notification_enable'] = Auth::guard('api')->user()->notification_enable;
            $data['premium_enable'] = Auth::guard('api')->user()->premium_enable;
        }
        return response(array('status' => "success", 'statuscode' => 200, 'message' =>__('ClientDetail'),'data'=>$data), 200); 
    }

    public function getClientDetailForPanel(Request $request){
        $app_id = 'default';
        $data = ['client_features'=>[]];
        $input = $request->all();
        if($request->headers->has('app-id')){
            $app_id = $request->header('app-id');
        }elseif(isset($input['app-id'])){
            $app_id = $input['app-id'];
        }
        if (Redis::exists('client_feature_'.$app_id)) {
            $data = json_decode(Redis::get('client_feature_admin_'.$app_id),true);
            $data['FromCache'] = true; 
        } else {
            $data = $this->getClientDetailFromDBForAdmin($app_id);
            $data['FromCache'] = false; 
        }
         return response(array('status' => "success", 'statuscode' => 200, 'message' =>__('ClientDetail'),'data'=>$data), 200); 
    }


    public function getClientDetailFromDB($app_id) {
        $data = ['client_features'=>[],'domain'=>'default'];
        $data['currency_code'] = 'INR';
        $data['jitsi_id'] = 202001;
        $data['pages'] = Page::select('slug','title')->get();
        $insurance = false;
        $country_id = null;
        $country_name = null;
        $country_code = null;
        $country_name_code = null;
        $insurances = [];
        $services = EnableService::select('type','value')->get();
        foreach ($services as $key => $service) {
            if($service->value=='twillio')
                $service->value = 'twilio';
            if($service->type=='vendor_approved'){
                $service->type = 'vendor_auto_approved';
                if($service->value=='yes'){
                    $service->value = true;
                }else{
                    $service->value = false;
                }
            }
            if($service->type=='insurance' && $service->value=='yes'){
                $insurance = true;
                $service->value = true;
            }else if($service->type=='insurance' && $service->value=='no'){
                $service->value = false;
            }
            $data[$service->type] = $service->value;
        }
        if(isset($data['currency'])){
            if($data['currency']=="USD"){
                $cnt = Country::where(['sortname'=>'US'])->first();
                $country_id = $cnt->id;
                $country_name = $cnt->name;
                $country_code = $cnt->phonecode;
                $country_name_code = $cnt->sortname;
            }else{
                $currency_data = Currency::with('country')->where('code',$data['currency'])->first();
                if($currency_data && $currency_data->country){
                    $country_id = $currency_data->country->id;
                    $country_name = $currency_data->country->name;
                    $country_code = $currency_data->country->phonecode;
                    $country_name_code = $currency_data->country->sortname;
                }
            }
        }
        if($insurance){
            $insurances = \App\Model\Insurance::where('enable','1')->get();
        }
        $data['insurances'] = $insurances;
        $customer = \App\Model\Role::where('name','customer')->first();
        $service_provider = \App\Model\Role::where('name','service_provider')->first();
        $data['custom_fields'] = ['customer'=>[],'service_provider'=>[]];
        $data['custom_fields']['customer'] = \App\Model\CustomField::select('id','field_name','field_type','required_sign_up')
        ->where('user_type',$customer->id)
        ->where('required_sign_up','1')
        ->get();
        $data['custom_fields']['service_provider'] = \App\Model\CustomField::select('id','field_name','field_type','required_sign_up')
        ->where('user_type',$service_provider->id)
        ->where('required_sign_up','1')
        ->get();
        $client = \DB::connection('godpanel')->table('clients')
        ->where('client_key',$app_id)
        ->first();
        $data['domain_url'] = env('DOMAIN_URL');
        $data['support_url'] = null;
         if($client){
            $data['domain'] = $client->domain_name;
            $data['payment_type'] = 'stripe';
            $data['gateway_key'] = '';
            $data['gateway_secret'] = '';
            $client_features = \App\Model\GodPanel\ClientFeature::select('id as client_feature_id','client_id','feature_id','feature_values')
            ->where(['client_id'=>$client->id])
            ->get();
            foreach ($client_features as $key => $client_feature) {
                if($client_feature->feature_values){
                    $client_feature->feature_values = json_decode($client_feature->feature_values,true);
                    $client_feature_key_values = [];
                    foreach ($client_feature->feature_values as $key_id => $value) {
                        $featurekey = \App\Model\GodPanel\FeatureKey::where('id',$key_id)
                        ->first();
                        if($featurekey){
                            $client_feature_key_values[] = array(
                                'key_id'=>$key_id,
                                'key_name'=>$featurekey->key_name,
                                'key_value'=>$value,
                                'for_fron_end'=>$featurekey->for_fron_end,
                            );
                        }
                    }
                    $client_feature->feature_values = null;
                }
                $client_feature->name = $client_feature->feature->name;
                unset($client_feature->feature);
            }
            // die;
            if($client_features)
                $data['client_features'] = $client_features;
         }
         $data['country_id'] = $country_id;
         $data['country_name'] = $country_name;
         $data['country_code'] = $country_code;
         $data['country_name_code'] = $country_name_code;
         Redis::set('client_feature_'.$app_id,json_encode($data,true)); 
         return $data;
    }

    public function getClientDetailFromDBForAdmin($app_id) {
        $data = ['client_features'=>[],'domain'=>'default'];
        $client = \DB::connection('godpanel')->table('clients')->where('client_key',$app_id)->first();
         if($client){
            $data['domain'] = $client->domain_name;
            $client_features = \App\Model\GodPanel\ClientFeature::select('id as client_feature_id','client_id','feature_id','feature_values')->where([
                'client_id'=>$client->id
            ])->get();
            foreach ($client_features as $key => $client_feature) {
                if($client_feature->feature_values){
                    $client_feature->feature_values = json_decode($client_feature->feature_values,true);
                    $client_feature_key_values = [];
                    foreach ($client_feature->feature_values as $key_id => $value) {
                        $featurekey = \App\Model\GodPanel\FeatureKey::where('id',$key_id)->first();
                        if($featurekey){
                            $client_feature_key_values[] = array(
                                'key_id'=>$key_id,
                                'key_name'=>$featurekey->key_name,
                                'key_value'=>$value,
                                'for_fron_end'=>$featurekey->for_fron_end,
                            );
                        }
                    }
                    $client_feature->feature_values = null;
                }
                $client_feature->name = $client_feature->feature->name;
                unset($client_feature->feature);
            }
            if($client_features)
                $data['client_features'] = $client_features;
            Redis::set('client_feature_admin_'.$app_id,json_encode($data,true));
         }
         return $data;
    }

    /**
     * @SWG\Post(
     *     path="/pages",
     *     description="pages",
     * tags={"Page"},
     *     @SWG\Response(
     *         response=200,
     *         description="slug,title",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     */

    public static function getPageContent(Request $request){  
        try{
            $pages = Page::select('slug','title')->get();
            return response(array('status' => "success", 'statuscode' => 200, 'message' =>__('Dynamic Pages'),'data'=>['pages'=>$pages]), 200); 
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        } 
    }
    /**
     * @SWG\Post(
     *     path="/home",
     *     description="Home",
     * tags={"Home"},
     *     @SWG\Response(
     *         response=200,
     *         description="top_doctors,top_blogs,top_articles",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     */
    public static function getHomePageData(Request $request){  
        try{
            $query = "CAST(rating AS DECIMAL(10,2)) DESC";
            $ids = \App\Model\Profile::orderByRaw($query)->pluck('user_id')->toArray();
            $ids_ordered = implode(',', $ids);
            $doctors = [];
            if(count($ids) >0){
                $doctors = User::whereHas('roles', function ($query) {
                   $query->where('name','service_provider');
                })->whereIn('id',$ids)->orderByRaw("FIELD(id, $ids_ordered)")
                ->take(5)->get();
            }
            $doctor_data = [];
            foreach ($doctors as $key => $doctor) {
                $doctor_data[] = User::getDoctorDetail($doctor->id);
            }
            /* Testimonials */

            $testimonials = \App\Model\Feedback::select('id','from_user','rating','comment','consultant_id')->with(['user' => function($query) {
                            return $query->select(['id', 'name', 'email','phone','profile_image']);
                        }])
            ->with(['consultant' => function($query) {
                            return $query->select(['id', 'name', 'email','phone','profile_image']);
                        }])
            ->orderBy('rating', 'desc')
            ->take(5)->groupBy('consultant_id')->get();

            $blogs = \App\Feed::select('id','title','image','description','like','user_id','created_at','views','favorite')->where('type','blog')->orderBy('id', 'desc')->take(5)->get();
            foreach ($blogs as $key => $feed) {
                $user_data = User::select(['id', 'name', 'email','phone','profile_image'])->with('profile')->where('id',$feed->user_id)->first();
                $feed->user_data = $user_data;
                if(Auth::guard('api')->check()){
                    $feedfavorite = FeedFavorite::where([
                        "user_id"=>Auth::guard('api')->user()->id,
                        "feed_id"=>$feed->id,
                        "favorite"=>1
                    ])->first();
                    $feed->is_favorite = false;
                    if($feedfavorite){
                        $feed->is_favorite = true;
                    }
                }
            }
            $articles = \App\Feed::select('id','title','image','description','like','user_id','created_at','views','favorite')->where('type','article')->orderBy('id', 'desc')->take(5)->get();
            foreach ($articles as $key => $feed) {
                $user_data = User::select(['id', 'name', 'email','phone','profile_image'])->with('profile')->where('id',$feed->user_id)->first();
                $feed->user_data = $user_data;
                if(Auth::guard('api')->check()){
                    $feedfavorite = FeedFavorite::where([
                        "user_id"=>Auth::guard('api')->user()->id,
                        "feed_id"=>$feed->id,
                        "favorite"=>1
                    ])->first();
                    $feed->is_favorite = false;
                    if($feedfavorite){
                        $feed->is_favorite = true;
                    }
                }
            }
            $tips = \App\Feed::select('id','title','image','description','like','user_id','created_at','views','favorite')->where('type','tip')->where("created_at",">",\Carbon\Carbon::now()->subDay())->where("created_at","<",\Carbon\Carbon::now())->orderBy('id', 'desc')->take(5)->get();
            foreach ($tips as $key => $feed) {
                $feed->comment_count = \App\Model\FeedComment::where('feed_id',$feed->id)->count();
                $user_data = User::select(['id', 'name', 'email','phone','profile_image'])->with('profile')->where('id',$feed->user_id)->first();
                $feed->user_data = $user_data;
                if(Auth::guard('api')->check()){
                    $feedfavorite = FeedFavorite::where([
                        "user_id"=>Auth::guard('api')->user()->id,
                        "feed_id"=>$feed->id,
                        "favorite"=>1
                    ])->first();
                    $feed->is_favorite = false;
                    if($feedfavorite){
                        $feed->is_favorite = true;
                    }
                    $like = \App\Model\FeedLike::where([
                        "user_id"=>Auth::guard('api')->user()->id,
                        "feed_id"=>$feed->id,
                        "like"=>'1'
                    ])->first();
                    $feed->is_like = false;
                    if($like){
                        $feed->is_like = true;
                    }
                }
            }
            $promotional = \App\Feed::select('id','title','image','description','like','user_id','created_at','views','favorite')->where('type','promotional')->orderBy('id', 'desc')->take(5)->get();
            foreach ($promotional as $key => $feed) {
                $user_data = User::select(['id', 'name', 'email','phone','profile_image'])->with('profile')->where('id',$feed->user_id)->first();
                $feed->user_data = $user_data;
                if(Auth::guard('api')->check()){
                    $feedfavorite = FeedFavorite::where([
                        "user_id"=>Auth::guard('api')->user()->id,
                        "feed_id"=>$feed->id,
                        "favorite"=>1
                    ])->first();
                    $feed->is_favorite = false;
                    if($feedfavorite){
                        $feed->is_favorite = true;
                    }
                }
            }
            $questions = \App\Feed::select('id','title','image','description','like','user_id','created_at','views','favorite')->where('type','question')->orderBy('id', 'desc')->take(5)->get();
            foreach ($questions as $key => $feed) {
                $user_data = User::select(['id', 'name', 'email','phone','profile_image'])->with('profile')->where('id',$feed->user_id)->first();
                $feed->user_data = $user_data;
                if(Auth::guard('api')->check()){
                    $feedfavorite = FeedFavorite::where([
                        "user_id"=>Auth::guard('api')->user()->id,
                        "feed_id"=>$feed->id,
                        "favorite"=>1
                    ])->first();
                    $feed->is_favorite = false;
                    if($feedfavorite){
                        $feed->is_favorite = true;
                    }
                }
            }
            $symptoms = \App\Model\MasterPreference::select('id','name','image')->where('type','symptoms')->orderBy('id', 'desc')->take(5)->get();

            $courses =\App\Model\Course::get();

            foreach ($courses as $key => $value) {

                $value->total =\App\Model\SpCourse::where('course_id',$value->id)->count();
                    
            }

            

                 
            return response(array(
                'status' => "success",
                'statuscode' => 200,
                'message' =>__('Home Data'),
                'data'=>[
                    'testimonials'=>$testimonials,
                    'top_doctors'=>$doctor_data,
                    'top_blogs'=>$blogs,
                    'top_articles'=>$articles,
                    'promotional'=>$promotional,
                    'tips'=>$tips,
                    'symptoms'=>$symptoms,
                    'questions'=>$questions,
                    'courses' => $courses,
                ]), 200); 
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        } 
    }


    /**
     * @SWG\Get(
     *     path="/plans",
     *     description="Plan",
     * tags={"Plan"},
     *     @SWG\Response(
     *         response=200,
     *         description="plans",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     */
    public static function getPlans(Request $request){  
        try{
            $plans = \App\Model\Plan::select('name','description','plan_id','price','permission')->where('status','enable')->get();
            return response(array('status' => "success", 'statuscode' => 200, 'message' =>__('Plans'),'data'=>['plans'=>$plans]), 200); 
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        } 
    }

    public function getNotification(Request $request){
         $notification = [
            "title" => "TEST",
            "body"=> "TEST DEMO",
            "sound"=> "default",
            "badge"=>0
        ];
          $fields = array (
              'registration_ids' =>["f-2KnVx3gkXFsGas_8uv0m:APA91bHZdscWJPemqKbYOikXfCKCbuop_aIHKWTBCjB3Pq1Dap6PvqQOjAz2Yg_7oxyBC9fSmQiUwUjSCBGTcjD-T9u1h1k9NrUzQF8QnXWzgQV6c5irF9oXZsB_NlEdAGiw5bhwZvM8"],
              'data' =>$notification,
              'notification'=>$notification,
              "priority"=>"high",
          );
         $url = "https://fcm.googleapis.com/v1/projects/consultapp/messages:send";
          //header includes Content type and api key
          /*api_key available in:
          Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key*/
          $api_key = env('SERVER_KEY_ANDRIOD');
          $headers = array(
              'Content-Type:application/json',
              'Authorization:Bearer AAAA3cw_ZOY:APA91bEhM5Y7VMATmlOkYxae_5KOwcS4FAiJ-FTZy01xtXxzbcxG1vtFp-dEOy3CSmriYR8Jz2avw3SnEwDdhoCd4zVrJQu_kRIctEv-kcP6faGpGgtI5vE2gbNBt-wwXSAgTmHSFrzX'
          );
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_POST, true);
          curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
          $result = curl_exec($ch);
          curl_close($ch);
          print_r($result);die;
          \Log::channel('custom')->info('sendNotification==========', ['result' => $result]);
    }


    /**
     * @SWG\Get(
     *     path="/pandemic",
     *     description="Pandemic List",
     * tags={"Pandemic"},
     *  @SWG\Parameter(
     *         name="type",
     *         in="query",
     *         type="string",
     *         description="type tips,symptom,prevention,home",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="plans",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     */
    public function getPandemicList(Request $request){
        $per_page = (isset($request->per_page)?$request->per_page:10);
        $type = (isset($request->type)?$request->type:'home');
        if($type=='home'){
            $banner = Covid19::select('id','image_web','image_mobile','title','description','on_click_info','home_screen')->where([
                'type'=>'banner',
                'enable'=>1
            ])->take(2)->get();
            $tips = Covid19::where([
                'type'=>'tips',
                'enable'=>1
            ])->get();
            $symptom = Covid19::where([
                'type'=>'symptom',
                'enable'=>1
            ])->take(2)->get();
            $prevention = Covid19::where([
                'type'=>'prevention',
                'enable'=>1
            ])->take(5)->get();
            return response(['status' => "success",
                'statuscode' => 200,
                'message' => __('Home Data'),
                'data' =>[
                    'banner'=>$banner,
                    'tips'=>$tips,
                    'symptom'=>$symptom,
                    'prevention'=>$prevention
                ]], 200);
        }else{
            $datas = Covid19::select('id','image_web','image_mobile','title','description','on_click_info','home_screen')->where([
                'type'=>$type,
                'enable'=>1
            ])->orderBy('id','DESC')->cursorPaginate($per_page);
            foreach ($datas as $key => $data) {
                if($data->on_click_info==''){
                    $data->on_click_info = 'detail';
                }
            }
            $after = null;
            if($datas->meta['next']){
                $after = $datas->meta['next']->target;
            }
            $before = null;
            if($datas->meta['previous']){
                $before = $datas->meta['previous']->target;
            }
            $per_page = $datas->perPage();
            return response(['status' => "success",
                'statuscode' => 200,'message' => __('List...'),
                'data' =>[
                    'pandemic'=>$datas->items(),
                    'after'=>$after,
                    'before'=>$before,
                    'per_page'=>$per_page
                ]], 200);
        }
    }

    /**
     * @SWG\Get(
     *     path="/master/preferences",
     *     description="Preferences List",
     * tags={"Master Preferences"},
     *  @SWG\Parameter(
     *         name="type",
     *         in="query",
     *         type="string",
     *         description="type Languages,Gender,All",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="preference_type",
     *         in="query",
     *         type="string",
     *         description="preference_type covid,personal_interest,work_environment,lifestyle,all",
     *         required=false,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="plans",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     */
    public function getMasterPreferences(Request $request,\App\Model\MasterPreference $masterpreference){
        $input = $request->all();
        $rules = array('type'=>'required');
        $validation = \Validator::make($input,$rules);            
        if($validation->fails()){
            return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                $validation->getMessageBag()->first()), 400);
        }
        $user_id = null;
        if(Auth::guard('api')->check()){
            $user_id = Auth::guard('api')->user()->id;
        }
        $show_on = ['both'];
        $user_type = $request->header('user-type');
        if($user_type=='customer'){
            $show_on = ['user','both'];
        }elseif($user_type=='service_provider'){
            $show_on = ['sp','both'];
        }
        $masterpreference = $masterpreference->newQuery();
        if(isset($input['preference_type']) && $input['preference_type']!='all'){
            $masterpreference->where('type',$input['preference_type']);
        }
        if(strtolower($input['type'])=='all'){
            $MasterPreferences = $masterpreference->select('id','name as preference_name','is_multi','type as preference_type','show_on','is_required')->whereIn('show_on',$show_on)->where('created_by',null)->orderBy('id','ASC')->get();
        }else{
            $MasterPreferences = $masterpreference->select('id','name as preference_name','is_multi','type as preference_type','show_on','is_required')->where('created_by',null)->orderBy('id','ASC')->where('name',$input['type'])->whereIn('show_on',$show_on)->orderBy('id','ASC')->get();
        }
        if($MasterPreferences->count()>0){
            foreach ($MasterPreferences as $key => $MasterPreference) {
                $MasterPreference->is_multi = (string)$MasterPreference->is_multi;
                foreach ($MasterPreference->options as $key => $option) {
                    if($user_id!=null){
                      $exist = UserMasterPreference::where(['user_id'=>$user_id,'preference_option_id'=>$option->id])->first();
                      if($exist)
                        $option->isSelected = true;
                    }
                    unset($option->created_at);
                    unset($option->updated_at);
                    unset($option->deleted_at);
                }
            }
        }
        return response(['status' => "success",
                'statuscode' => 200,'message' => __('List...'),
                'data' =>[
                    "preferences"=>$MasterPreferences,
                ]], 200);
    }

    /**
     * @SWG\Get(
     *     path="/master/custom/masterfields",
     *     description="getCustomMasterFields List",
     * tags={"Master Preferences"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="module_type",
     *         in="query",
     *         type="string",
     *         description="module_type medical_report",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="plans",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     */
    public function getCustomMasterFields(Request $request,\App\Model\CustomMasterField $masterpreference){
        $input = $request->all();
        $rules = array('module_type'=>'required');
        $validation = \Validator::make($input,$rules);            
        if($validation->fails()){
            return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                $validation->getMessageBag()->first()), 400);
        }
        $masterpreference = $masterpreference->newQuery();
        $masterfields = $masterpreference->with(['data'])->select('id','name','type','module_type','created_by')->where('created_by',null)->orderBy('id','ASC')->where('module_type',$input['module_type'])->get();
        return response(['status' => "success",
                'statuscode' => 200,'message' => __('List...'),
                'data' =>[
                    "masterfields"=>$masterfields,
                ]], 200);
    }



    /**
     * @SWG\Post(
     *     path="/master/custom/masterfields",
     *     description="postCustomMasterFields List",
     * tags={"Master Preferences"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="module_type",
     *         in="query",
     *         type="string",
     *         description="module_type medical_report",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="master_field_id",
     *         in="query",
     *         type="string",
     *         description="id of selected master_fields",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="selected_data",
     *         in="query",
     *         type="string",
     *         description="selected_data array [{'name':'abc.pdf','type':'pdf'},{'name':'abc.png','type':'image'}]",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="data",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     */
    public function postCustomMasterFields(Request $request){
        $user = Auth::user();
        $input = $request->all();
        $rules = array('module_type'=>'required','master_field_id'=>'required||exists:custom_master_fields,id','selected_data'=>'required');
        $validation = \Validator::make($input,$rules);            
        if($validation->fails()){
            return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                $validation->getMessageBag()->first()), 400);
        }
         if(!is_array($input['selected_data']))
            $input['selected_data'] = json_decode($input['selected_data'],true);
         if(is_array($input['selected_data'])){
                foreach ($input['selected_data'] as $key => $data) {
                if($data)
                \App\Model\CustomUserMasterField::firstOrCreate([
                    'field_value'=>$data['name'],
                    'user_id'=>$user->id,
                    'custom_field_id'=>$input['master_field_id'],
                    'field_value_type'=>$data['type'],
                ]);
            }
        }
        return response(['status' => "success",
                'statuscode' => 200,'message' => __('Added...')], 200);
    }


    /**
     * @SWG\Get(
     *     path="/master/selected/preferences",
     *     description="Preferences Selected List",
     * tags={"Master Preferences"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="preference_type",
     *         in="query",
     *         type="string",
     *         description="preference_type covid,personal_interest,work_environment,lifestyle,medical_history,all",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="plans",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     */
    public function getSelectedMasterPreferences(Request $request){
        $input = $request->all();
        $timezone = $request->header('timezone');
        if(!$timezone){
            $timezone = 'UTC';
        }
        $user = Auth::user();
        $rules = array('preference_type'=>'required');
        $validation = \Validator::make($input,$rules);            
        if($validation->fails()){
            return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                $validation->getMessageBag()->first()), 400);
        }
        $MasterPreferences = \App\Model\MasterPreference::getMasterPreferencesByType($user->id,$input['preference_type'],$timezone);
        return response(['status' => "success",
                'statuscode' => 200,'message' => __('List...'),
                'data' =>[
                    "preferences"=>$MasterPreferences,
                ]], 200);
    }


    /**
     * @SWG\Post(
     *     path="/master/preferences/custom",
     *     description="postCustomMasterPreferences",
     * tags={"Master Preferences"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="preference_type",
     *         in="query",
     *         type="string",
     *         description="preference_type personal_interest,work_environment,lifestyle,medical_history",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="name",
     *         in="query",
     *         type="string",
     *         description="name",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="summary",
     *         in="query",
     *         type="string",
     *         description="summary",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     */
    public function postCustomMasterPreferences(Request $request){
        $input = $request->all();
        $timezone = $request->header('timezone');
        if(!$timezone){
            $timezone = 'UTC';
        }
        $user = Auth::user();
        $rules = array("preference_type"=>"required|in:personal_interest,work_environment,lifestyle,medical_history",'name'=>'required','summary'=>'required');
        // print_r($rules);die;
        $validation = \Validator::make($input,$rules);            
        if($validation->fails()){
            return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                $validation->getMessageBag()->first()), 400);
        }
        $filtertype = new MasterPreference();
        $filtertype->name = $input['name'];
        $filtertype->is_multi = "0";
        $filtertype->type = $input['preference_type'];
        $filtertype->created_by = $user->id;
        if($filtertype->save()){
            $filtertypeoption = MasterPreferencesOption::firstOrcreate(array(
                'preference_id'=>$filtertype->id,
                'name'=>$input['summary'],
            ));
            UserMasterPreference::where([
                'user_id'=>$user->id,
                'preference_id'=>$filtertype->id,
            ])->delete();
            UserMasterPreference::firstOrCreate([
                'user_id'=>$user->id,
                'preference_id'=>$filtertype->id,
                'preference_option_id'=>$filtertypeoption->id,
            ]);
        }
        $MasterPreferences = \App\Model\MasterPreference::getMasterPreferencesByType($user->id,$input['preference_type'],$timezone);
        return response(['status' => "success",
                'statuscode' => 200,'message' => __('List...'),
                'data' =>[
                    "preferences"=>$MasterPreferences,
        ]], 200);
    }


    /**
     * @SWG\Get(
     *     path="/master/duty",
     *     description="Preferences List",
     * tags={"Master Preferences"},
     *  @SWG\Parameter(
     *         name="filter_ids",
     *         in="query",
     *         type="string",
     *         description="option ids of filter",
     *         required=false,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="data",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     */
    public function getMasterPreferencesDuty(Request $request,\App\Model\MasterPreOptionFilter $masterpreoptionfilter){
        $user_id = null;
        if(Auth::guard('api')->check()){
            $user_id = Auth::guard('api')->user()->id;
        }
        $input = $request->all();
        $rules = [];
        $validation = \Validator::make($input,$rules);            
        if($validation->fails()){
            return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                $validation->getMessageBag()->first()), 400);
        }
        $masterpreoptionfilter = $masterpreoptionfilter->newQuery();
        // if(isset($input['filter_ids'])){
        //     $filter_ids = explode(',',$request->filter_ids);
        //     $masterpreoptionfilter->whereIn('module_id',$filter_ids);
        // }
        $masterpreoptionfilter = $masterpreoptionfilter->where('module_table','filter_options')->groupBy('option_id')->pluck('option_id')->toArray();
        $MasterPreferences = \App\Model\MasterPreference::select('id','name as preference_name','is_multi','type as preference_type')->with(['options' => function($query) use($masterpreoptionfilter) {
            return $query->whereIn('id',$masterpreoptionfilter)->select(['id', 'name as option_name','preference_id','image','description']);
        }])->where('type','duty')->get();
        if($MasterPreferences->count()>0){
            foreach ($MasterPreferences as $key => $MasterPreference) {
                $MasterPreference->is_multi = (string)$MasterPreference->is_multi;
                foreach ($MasterPreference->options as $key => $option) {
                    if($user_id!=null){
                      $exist = UserMasterPreference::where(['user_id'=>$user_id,'preference_option_id'=>$option->id])->first();
                      if($exist)
                        $option->isSelected = true;
                    }
                    unset($option->created_at);
                    unset($option->updated_at);
                    unset($option->deleted_at);
                }
            }
        }
        return response(['status' => "success",
                'statuscode' => 200,'message' => __('List...'),
                'data' =>[
                    "preferences"=>$MasterPreferences,
                ]], 200);
    }
    /**
     * @SWG\Get(
     *     path="/symptoms",
     *     description="Symptoms List",
     * tags={"Master Preferences"},
     *  @SWG\Parameter(
     *         name="type",
     *         in="query",
     *         type="string",
     *         description="type symptom_category,symptom_options,all_symptom_options",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="symptom_id",
     *         in="query",
     *         type="string",
     *         description="in symptom_options required true",
     *         required=false,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="type is all then data->symptoms:[]",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     */
    public function getMasterSymptoms(Request $request){
        $per_page = (isset($request->per_page)?$request->per_page:10);
        $input = $request->all();
        $rules = array('type'=>'required|in:symptom_category,symptom_options,all_symptom_options');
        if(isset($input['type']) && $input['type']=='symptom_options'){
            $rules['symptom_id'] =  'required';
        }
        $validation = \Validator::make($input,$rules);            
        if($validation->fails()){
            return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                $validation->getMessageBag()->first()), 400);
        }
        if(strtolower($input['type'])=='symptom_category'){
            $MasterPreferences = \App\Model\MasterPreference::select('id','name','image')->where('type','symptoms')->orderBy('id','DESC')->get();
            if($MasterPreferences->count()>0){
                foreach ($MasterPreferences as $key => $MasterPreference) {
                    foreach ($MasterPreference->options as $key => $option) {
                        $option->symptom_id = $option->preference_id;
                        unset($option->name);
                        unset($option->preference_id);
                        unset($option->created_at);
                        unset($option->updated_at);
                        unset($option->deleted_at);
                    }
                }
            }
            return response(['status' => "success",
                    'statuscode' => 200,'message' => __('List...'),
                    'data' =>[
                        "symptoms"=>$MasterPreferences,
                    ]], 200);
        }else if(strtolower($input['type'])=='symptom_options'){
            $MasterPreferencesOptions = \App\Model\MasterPreferencesOption::select('id','name','image','description','preference_id as symptom_id')->whereHas('masterpreference', function ($query) {
                   $query->where('type','symptoms');
                })->where('preference_id',$input['symptom_id'])->orderBy('id','DESC')->get();
            return response(['status' => "success",
                'statuscode' => 200,'message' => __('List...'),
                'data' =>[
                    'symptoms'=>$MasterPreferencesOptions,
                ]], 200);
        }else if(strtolower($input['type'])=='all_symptom_options'){
            $MasterPreferencesOptions = \App\Model\MasterPreferencesOption::select('id','name','image','description','preference_id as symptom_id')->whereHas('masterpreference', function ($query) {
                   $query->where('type','symptoms');
                })->orderBy('id','DESC')->get();
            return response(['status' => "success",
                'statuscode' => 200,'message' => __('List...'),
                'data' =>[
                    'symptoms'=>$MasterPreferencesOptions,
                ]], 200);
        }
    }

     /**
     * @SWG\Post(
     *     path="/create-banner",
     *     description="Add Banner From Service Provider",
     * tags={"Banners"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="image",
     *         in="query",
     *         type="string",
     *         description="image",
     *         required=true,
     *     ), 
     *     @SWG\Response(
     *         response=200,
     *         description="",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     */
    public function addBanner(Request $request){
        try{
            $user = Auth::user();
            $input = $request->all();
            $rules = ['image'=>'required'];
            $validation = \Validator::make($input,$rules);            
            if($validation->fails()){
                return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                    $validation->getMessageBag()->first()), 400);
            }
            $start_date = \Carbon\Carbon::now()->format('Y-m-d');
            $end_date = \Carbon\Carbon::now()->addDay(15)->format('Y-m-d');
            $banner = new \App\Model\Banner();
            $banner->image_web = $input['image'];
            $banner->image_mobile = $input['image'];
            $banner->start_date = $start_date;
            $banner->end_date = $end_date;
            $banner->position = '1';
            $banner->category_id =null;
            $banner->sp_id = $user->id;
            $banner->created_by = $user->id;
            $banner->class_id =null;
            $banner->banner_type = 'service_provider';
            $banner->enable = 0;
            $banner->save();
            return response(array(
                        'status' => 'success',
                        'statuscode' => 200,
                        'message' =>'Banner Uploaded Successfully')
                    ,200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        } 

    }
    /**
     * @SWG\Post(
     *     path="/verification/insurance",
     *     description="insurance verification",
     * tags={"Insurance"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="request_id",
     *         in="query",
     *         type="string",
     *         description="request_id",
     *         required=true,
     *     ), 
     *     @SWG\Response(
     *         response=200,
     *         description="",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     */
    public function verifyEligibility(Request $request){
        try{
            $input = $request->all();
            $rules = ['request_id'=>'required|exists:requests,id'];
            $validation = \Validator::make($input,$rules);            
            if($validation->fails()){
                return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                    $validation->getMessageBag()->first()), 400);
            }
            $request_data = \App\Model\Request::where('id',$request->request_id)->first();
            $ins_info = CustomInfo::where([
                'ref_table_id'=>$request_data->from_user,
                'ref_table'=>'users',
                'info_type'=>'insurance_verification'
            ])->orderBy('id','DESC')->first();
            $insurance_query = []; 
            if($ins_info){
                $insurance_query = json_decode($ins_info->raw_detail);
                if(isset($insurance_query->insurance_id)){
                     $insurance = \App\Model\Insurance::where(['id'=>$insurance_query->insurance_id])->first();
                     if($insurance){
                        $insurance_query->carrier_code = $insurance->carrier_code;
                     }
                }
            }
            $insurance_query->npi = $request_data->sr_info->npi_id;
            $http_build_query =  http_build_query($insurance_query);
            // print_r($http_build_query);die;
            $url = "https://api.doradosystems.com/rt/validate";
            $api_key = "dCdWheAbtdfdOdEcDbDbCeFbDegbHbk";
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => $url."?api_key=$api_key&".$http_build_query,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
              ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                return response(array(
                    'status' => 'error',
                    'statuscode' => 400,
                    'message' =>$err)
                ,400);
            } else {
                $result = json_decode($response);
                if(isset($result->Loop_2000A->Loop_2100A) && isset($result->Loop_2000A->Loop_2100A->PER_InformationSourceContactInformation_2100A)){
                    // $datenow = new DateTime("now", new DateTimeZone('UTC'));
                    // $datenowone = $datenow->format('Y-m-d H:i:s');
                    // $user = Auth::user();
                    // $user->insurance_verified = $datenowone;
                    // $user->save();
                    return response(array(
                    'status' => 'success',
                    'statuscode' => 200,
                    'data'=>[
                        'insurance'=>$result->Loop_2000A->Loop_2100A->PER_InformationSourceContactInformation_2100A
                        ],
                    'message' =>'Insurance Verified')
                ,200);
                }else{
                    return response(array(
                    'status' => 'error',
                    'statuscode' => 400,
                    'message' =>'Insurance Not Verified')
                ,400);
                }
            }
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        } 
    }
}
