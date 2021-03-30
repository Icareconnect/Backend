<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use Illuminate\Encryption\Encrypter;
use AWS;
use Aws\S3\S3Client;
use GuzzleHttp\Client;
use Cartalyst\Stripe\Stripe;
use Image,File,Storage;
use Config,Exception;
use App\Model\Transaction,App\Model\Payment;
use App\Model\GodPanel\FeatureType;
use App\Model\UserInsurance;
use App\Model\EnableService;
use App\Model\Card;
use App\Model\GroupVendor;
use App\Model\Plan;
use Carbon\Carbon;
use DB,App\User,App\Model\Package,App\Model\UserPackage,App\Model\SubscribePlan;
use App\Model\Image as ModelImage;
use Illuminate\Http\Request;
use DateTime,DateTimeZone;
class Helper{

    public static function getBanners(){
        $dateznow = new DateTime("now", new DateTimeZone('UTC'));
        $datenow = $dateznow->format('Y-m-d');
        $banners = \App\Model\Banner::where(function($q) use($datenow) {
              $q->where('end_date', '>=', $datenow)
                ->orWhere('start_date', '>=', $datenow);
        })->where('enable',1)->orderBy('position','ASC')->get();
        foreach ($banners as $key => $banner) {
            if($banner->banner_type=='category'){
                $banner->category;
                $subcategory = \App\Model\Category::where('parent_id',$banner->category_id)->where('enable','=','1')->count();
                if($subcategory > 0){
                   $banner->category->is_subcategory = true;
                }else{
                    $banner->category->is_subcategory = false;
                }
                $banner->category->is_filters = false;
                if($banner->category->filters->count() > 0){
                    $banner->category->is_filters = true;
                }
            }elseif ($banner->banner_type=='class') {
                $banner->class;
            }elseif ($banner->banner_type=='service_provider') {
                $banner->service_provider;
            }
        }
        $blogs = \App\Feed::select('id','title','image','description','like','user_id','created_at','views','favorite')->where('type','blog')->orderBy('id', 'desc')->take(20)->get();
        return ['banners'=>$banners,'blogs'=>$blogs];
    }

    public static function getTimeSlot($starttime,$endtime,$duration){
        //$starttime = '9:00';  // your start time
        //$endtime = '21:00';  // End time
        //$duration = '30';  // split by 30 mins
        $array_of_time = array ();
        $start_time    = strtotime ($starttime); //change to strtotime
        $end_time      = strtotime ($endtime); //change to strtotime
        $add_mins  = $duration * 60;
        while ($start_time <= $end_time) // loop between time
        {
           $interval = ["key"=> date("H:i", $start_time),"value"=>date("H:i A", $start_time)];
           $array_of_time[] = $interval;
           $start_time += $add_mins; // to check endtie=me
        }
        return $array_of_time;
    }

    public static function getSecurityQuestion($type){
        $SecurityQuestion = \App\Model\SecurityQuestion::where('type',$type)->get();
        return $SecurityQuestion;
    }

    public static function getSelectedQuestion($type,$user_id){
        $SecurityQuestion = \App\Model\UserSecurityAnswer::whereHas('question', function ($query) use($type) {
            return $query->where('type',$type);
        })
        ->where('user_id',$user_id)->first();
        return $SecurityQuestion;
    }

    public static function getMasterSlots($timezone='UTC'){
        $masterslots = \App\Model\MasterSlot::orderBy('id','ASC')->get();
        foreach ($masterslots as $key => $masterslot) {
            $start_time_date = Carbon::parse($masterslot->start_time,'UTC')->setTimezone($timezone);
            $end_time_date = Carbon::parse($masterslot->end_time,'UTC')->setTimezone($timezone);
            $masterslot->start_time = $start_time_date->format('H:i');
            $masterslot->end_time = $end_time_date->format('H:i');;
        }
        return $masterslots;
    }

    public static function connectByClient($client_id){
         $client = DB::connection('godpanel')->table('clients')->where('id',$client_id)->first();
         if($client){
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
            Config::set("database.connections.$database_name", $default);
            Config::set("client_id", $client->id);
            Config::set("client_connected",true);
            Config::set("client_data",$client);
            DB::setDefaultConnection($database_name);
            DB::purge($database_name);
            return $database_name;
        }
    }

    public static function connectByClientKey($client_key){
         $client = DB::connection('godpanel')->table('clients')->where('client_key',$client_key)->first();
         if($client){
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
            Config::set("database.connections.$database_name", $default);
            Config::set("client_id", $client->id);
            Config::set("client_connected",true);
            Config::set("client_data",$client);
            DB::setDefaultConnection($database_name);
            DB::purge($database_name);
            return $database_name;
        }
    }

    public static function connectByDomain($domain_name){
         $client = DB::connection('godpanel')->table('clients')->where('domain_name',$domain_name)->first();
         if($client){
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
            Config::set("database.connections.$database_name", $default);
            Config::set("client_id", $client->id);
            Config::set("client_connected",true);
            Config::set("client_data",$client);
            DB::setDefaultConnection($database_name);
            DB::purge($database_name);
            return $database_name;
        }
    }
	/**
     * Send APN Push Notification
     *
     */
    public static function sendAPNPushNotification($user,$params){
        // Put your device token here (without spaces):
        //$deviceToken = '83F20AEF16A27994535F55C02F82DF29E8A5E87B9E43A68A48A8922FC3010F01';
        // Put your private key's passphrase here:
        $passphrase = '123';
        // Put your alert message here:
	// Put the full path to your .pem file
        $pem_file_name = "VOIP_PEM_USER.pem";
        if(Config::get('client_connected') && Config::get("client_data")->domain_name=="marketplace"){
            $pem_file_name = "MarketPlace.pem";
        }else if(Config::get('client_connected') && Config::get("client_data")->domain_name=="education"){
            $pem_file_name = "Education.pem";
        }else if(Config::get('client_connected') && Config::get("client_data")->domain_name=="healtcaremydoctor"){
            $pem_file_name = "Mydoctor.pem";
        }else if(Config::get('client_connected') && Config::get("client_data")->domain_name=="heal"){
            $pem_file_name = "heal.pem";
        }else if(Config::get('client_connected') && Config::get("client_data")->domain_name=="homedoctor"){
            $pem_file_name = "homedoctor.pem";
        }else if(Config::get('client_connected') && Config::get("client_data")->domain_name=="nurselynx"){
            $pem_file_name = "nurselynx.pem";
        }
    	if($user->hasrole('customer')){
            $pemFile = public_path().'/'.$pem_file_name;
    	}else{
            $pemFile = '';
    	}
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $pemFile);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

        // Open a connection to the APNS server
        $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

        if (!$fp)
            return false;
        // Create the payload body
        $body['aps'] = array(
            'alert' => $params['message'],
            'sound' => 'default',
            'data' => $params
            );
        // Encode the payload as JSON
        $payload = json_encode($body);
        \Log::channel('custom')->info('sendAPNPushNotification', ['payload'=>$payload]);
        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $params['tokens']) . pack('n', strlen($payload)) . $payload;
        // Send it to the server
        \Log::channel('custom')->info('sendAPNPushNotification', ['strlen($msg)'=>strlen($msg)]);
        $result = fwrite($fp, $msg, strlen($msg));
        fclose($fp);
        if (!$result){
            $response = false;
        }
        else{
            $response = true;
        }
        return $response;
    }

    public static function sendAPNPushNotificationTest($params){
        $deviceToken = $params->token;
        $passphrase = $params->password;
        $sender_type = $params->sender_type;
        $message = 'Your message';
        $pem_file_name = "VOIP_PEM_USER.pem";
        if(Config::get('client_connected') && Config::get("client_data")->domain_name=="marketplace"){
            $pem_file_name = "MarketPlace.pem";
        }else if(Config::get('client_connected') && Config::get("client_data")->domain_name=="education"){
            $pem_file_name = "Education.pem";
        }else if(Config::get('client_connected') && Config::get("client_data")->domain_name=="healtcaremydoctor"){
            $pem_file_name = "Mydoctor.pem";
        }else if(Config::get('client_connected') && Config::get("client_data")->domain_name=="heal"){
            $pem_file_name = "heal.pem";
        }else if(Config::get('client_connected') && Config::get("client_data")->domain_name=="homedoctor"){
            $pem_file_name = "homedoctor.pem";
        }
        if($sender_type==2){
            $pemFile = public_path().'/'.$pem_file_name;
        }else{
            $pemFile = '';
        }
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $pemFile);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

        // Open a connection to the APNS server
        $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

        if (!$fp)
            exit("Failed to connect: $err $errstr" . PHP_EOL);

        $body['aps'] = array(
            'alert' => array(
                'body' => $message,
                'action-loc-key' => 'Bango App',
            ),
            'badge' => 2,
            'sound' => 'oven.caf',
            );

        $payload = json_encode($body);
        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;


        $result = fwrite($fp, $msg, strlen($msg));
        fclose($fp);

        if (!$result)
            return 'Message not delivered' . PHP_EOL;
        else
            return 'Message successfully delivered' . PHP_EOL;
    }

    public static function getClientStripeData($params){
        $client_feature_keys = DB::connection('godpanel')->table('godpanel_client_features')
        ->where('client_id',$params['client_id'])
        ->join('godpanel_features', function($join)
        {
            $join->on('godpanel_client_features.feature_id', '=', 'godpanel_features.id')
                 ->where('godpanel_features.name','stripe');
        })
        ->join('godpanel_feature_keys', function($join)
        {
            $join->on('godpanel_client_features.feature_id', '=', 'godpanel_feature_keys.feature_id');
        })
        ->get();
        $data = [];
        foreach ($client_feature_keys as $key => $client_feature_key) {
            $data[$client_feature_key->key_name] = $client_feature_key->key_value;
        }
        return $data;
    }   

    public static function getClientFacebookKeys(){
        $godpanel_feature_type_id  = FeatureType::where('name','=','social login')->pluck('id')->first();
        $client_feature_keys = DB::connection('godpanel')->table('godpanel_client_features')
        ->where('client_id',Config::get('client_id'))
        ->join('godpanel_features', function($join) use($godpanel_feature_type_id)
        {
            $join->on('godpanel_client_features.feature_id', '=', 'godpanel_features.id')
                 ->where('godpanel_features.name', 'like', 'Facebook')
                 ->where('godpanel_features.feature_type_id', '=', $godpanel_feature_type_id);
        })
        ->join('godpanel_feature_keys', function($join)
        {
            $join->on('godpanel_client_features.feature_id', '=', 'godpanel_feature_keys.feature_id');
        })
        ->get();
        $data = [];
        foreach ($client_feature_keys as $key => $client_feature_key) {
            $keys = json_decode($client_feature_key->feature_values,true);
            if(isset($keys[$client_feature_key->id])){
                $data[$client_feature_key->key_name] = $keys[$client_feature_key->id];
            }else{
                $data[$client_feature_key->key_name] = '';
            }
        }
        return $data;
    }

    public static function getClientFeatureKeys($feature_type,$feature_name){
        $godpanel_feature_type_id  = FeatureType::where('name','=',$feature_type)->pluck('id')->first();
        $client_feature_keys = DB::connection('godpanel')->table('godpanel_client_features')
        ->where('client_id',Config::get('client_id'))
        ->join('godpanel_features', function($join) use($godpanel_feature_type_id,$feature_name)
        {
            $join->on('godpanel_client_features.feature_id', '=', 'godpanel_features.id')
                 ->where('godpanel_features.name', 'like', $feature_name)
                 ->where('godpanel_features.feature_type_id', '=', $godpanel_feature_type_id);
        })
        ->join('godpanel_feature_keys', function($join)
        {
            $join->on('godpanel_client_features.feature_id', '=', 'godpanel_feature_keys.feature_id');
        })
        ->get();
        $data = [];
        foreach ($client_feature_keys as $key => $client_feature_key) {
            $keys = json_decode($client_feature_key->feature_values,true);
            if(isset($keys[$client_feature_key->id])){
                $data[$client_feature_key->key_name] = $keys[$client_feature_key->id];
            }else{
                $data[$client_feature_key->key_name] = '';
            }
        }
        return $data;
    }


    public static function getClientFeatureExistWithFeatureType($feature_type,$feature_name){
        $godpanel_feature_type_id  = FeatureType::where('name','=',$feature_type)->pluck('id')->first();
        $client_feature_keys = DB::connection('godpanel')->table('godpanel_client_features')
        ->where('client_id',Config::get('client_id'))
        ->join('godpanel_features', function($join) use($godpanel_feature_type_id,$feature_name)
        {
            $join->on('godpanel_client_features.feature_id', '=', 'godpanel_features.id')
                 ->where('godpanel_features.name', 'like', $feature_name)
                 ->where('godpanel_features.feature_type_id', '=', $godpanel_feature_type_id);
        })
        ->get();
        if(count($client_feature_keys)){
            return true;
        }else{
            return false;
        }
    } 

    public static function checkFeatureExist($params){
        $client_feature_exist = DB::connection('godpanel')->table('godpanel_client_features')
        ->where('client_id',$params['client_id'])
        ->join('godpanel_features', function($join) use($params)
        {
            $join->on('godpanel_client_features.feature_id', '=', 'godpanel_features.id')
                 ->where('godpanel_features.name', '=', $params['feature_name']);
        })->first();
        return $client_feature_exist;
    }

    public static function isSusbScribe($params){
        $userpackage  = UserPackage::where([
            'user_id'=>$params['user_id'],
            'package_id'=>$params['package_id'],
        ])->where('available_requests','>',0)->first();
        if($userpackage){
            return true;
        }else{
            return false;
        }
    }
  

  public static function getUserSubPackage($params){
        $userpackage  = UserPackage::where([
            'user_id'=>$params['user_id'],
            'package_id'=>$params['package_id'],
        ])->where('available_requests','>',0)->first();
        return $userpackage;
    }

    public static function subscribePackage($params){
        $user = User::find($params['user_id']);
        $package = Package::where('id',$params['package_id'])->first();
        if($user->wallet->balance < $package->price){
            return array(
                'status' => "success",
                'statuscode' => 200,
                'message' => __('insufficient balance'),
                'data'=>(Object)['amountNotSufficient'=>true]);
        }
        $userpackage  = UserPackage::firstOrCreate(['user_id'=>$user->id,'package_id'=>$package->id]);
        if($userpackage){
            $userpackage->increment('available_requests',$package->total_requests);
        }
        $user->wallet->decrement('balance',$package->price);
        $transaction = Transaction::create(array(
                'amount'=>$package->price,
                'transaction_type'=>'add_package',
                'status'=>'success',
                'wallet_id'=>$user->wallet->id,
                'closing_balance'=>$user->wallet->balance,
        ));
        if($transaction){
            $transaction->module_table = 'user_packages';
            $transaction->module_id = $userpackage->id;
            $transaction->save();
            $payment = Payment::create(array('from'=>1,'to'=>$user->id,'transaction_id'=>$transaction->id));
        }
        return array(
                'status' => "success",
                'statuscode' => 200,
                'data'=>(Object)["price"=>$package->price,'userpackage'=>$userpackage],
                'message' =>__('Subscribe Successfully'));
    }

    public static function chargeFromSP(){
        if(Config::get('client_connected') && Config::get('client_data')->client_key==env('MYPATH_KEY')){
            return true;
        }
        return false;
    }

    public static function is_mp2r(){
        if(Config::get('client_connected') && Config::get('client_data')->client_key==env('MYPATH_KEY')){
            return true;
        }
        return false;
    }

    public static function getCurrentSubscription($user){
        $exist = self::checkFeatureExist([
            'client_id'=>Config::get('client_id'),
            'feature_name'=>'monthly plan'
        ]);
        $user->current_plan = null;
        $user->subscribe_plan = [];
        if($exist){
            $datenow = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
            $user->current_plan  = SubscribePlan::where(['user_id' => $user->id])
                ->where('expired_on', '>', $datenow)
                ->whereHas('plan', function ($query) {
                    $query->whereIn('plan_id', ["com.mp2r.basic", "com.mp2r.premium", "com.mp2r.executive"]);
                })->with('plan')
                ->first();
             if($user->current_plan){
                $user->current_plan->plan_name = $user->current_plan->plan->name;
                $user->current_plan->plan_attribute = $user->current_plan->plan->plan_id;
             }
             $subscribe_plans = [];
             $subscribeplans = SubscribePlan::where(['user_id'=>$user->id])
             ->where('expired_on','>',$datenow)->get();
            if($subscribeplans->count()>0){
                foreach ($subscribeplans as $key => $subscribeplan) {
                    $subscribe_plan = $subscribeplan->plan;
                    $subscribe_plan->expired_on = $subscribeplan->expired_on;
                    $subscribe_plans[] = $subscribe_plan;
                }
            }
            $user->subscribe_plan = $subscribe_plans;
        }
        $user->premium = Plan::where('plan_id','com.mp2r.premium')->first();
        $user->executive = Plan::where('plan_id','com.mp2r.executive')->first();
        $user->basic = Plan::where('plan_id','com.mp2r.basic')->first();
        $user->insurance = Plan::where('plan_id','com.mp2r.additional.insurance')->first();
        $user->group = Plan::where('plan_id','com.mp2r.additional.group')->first();
        return $user;
    }

    public static function getMoreData($user){
        $us_detail = User::select('account_verified as account_verified_at','account_rejected as account_rejected_at')->where('id',$user->id)->first();
        $exist = self::checkFeatureExist([
            'client_id'=>Config::get('client_id'),
            'feature_name'=>'monthly plan'
        ]);
        if($exist){
             $plan_names=[];
             $subscribe_plans = [];
             $datenow = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
             $subscribeplans = SubscribePlan::where(['user_id'=>$user->id])
             ->where('expired_on','>',$datenow)->get();
            if($subscribeplans->count()>0){
                foreach ($subscribeplans as $key => $subscribeplan) {
                    $subscribe_plan = $subscribeplan->plan;
                    $subscribe_plan->expired_on = $subscribeplan->expired_on;
                    $subscribe_plans[] = $subscribe_plan;
                    $plan_names[] = $subscribeplan->plan->name;
                }
            }
            $user->subscribe_plan = $subscribe_plans;
            $user->plan_names = $plan_names;
        }
        $user->group = null;
        $group = GroupVendor::where(['user_id'=>$user->id])->first();
        if($group){
            $user->group = $group->group->name;
        }
        $user->availability_available =  self::checkVendorAvailableToday($user->id);
        $user->account_verified_at =  $us_detail->account_verified_at;
        $user->account_rejected_at =  $us_detail->account_rejected_at;
        $user->isApproved = $user->account_verified;
        $user->master_preferences = \App\Model\MasterPreference::getMasterPreferences($user->id);
        $user->family_members = \App\Model\Family::getFamiliesByUser($user->id);
        $user->insurance_images = ModelImage::where([
            'module_table'=>'insurance_images','module_table_id'=>$user->id
                        ])->pluck('image_name')->toArray();
        $insurance_data = [];
        $insurance_infos = \App\Model\CustomInfo::where([
                'info_type'=>'user_insurance_info',
                'ref_table'=>'users',
                'ref_table_id'=>$user->id,
            ])->get();
        foreach ($insurance_infos as $key => $insurance_info) {
            $insurance_data [] = json_decode($insurance_info->raw_detail);
        }
        $user->insurance_info = $insurance_data;
        $user->insurance_info = $insurance_data;
        $user->consultationCount = $user->requestCompleted($user->id);
        $user->reference_user = User::select('name','email','id','profile_image','phone','country_code')->with('profile')->where('id',$user->reference_by)->first();
        $user->categoriesData = $user->getCategorysData($user->id);
        $user->additionalsdocument = $user->getAdditionalsDocument($user->id);
        $user->spPrice = $user->getSpPrice($user->id);
        $user->spCourses= $user->getcourseSP($user->id);
        
        // if(Config::get('client_connected') && Config::get('client_data')->domain_name=='heal'){
        //     $user->can_ask_question = \App\Model\Support::checkCanCreateQuestion($user->id);
        // }
        return $user;
    }

    public static function checkVendorAvailableToday($user_id){
        $datenow = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        $date= \Carbon\Carbon::now()->format('Y-m-d');
        $current_time = \Carbon\Carbon::now()->format('H:i:s');
        $current_category = 0;
        $service_id = 1;
        $user = User::where('id',$user_id)->first();
        $category = $user->getCategoryData($user_id);
        if ($category) {
            $current_category = $category->id;
        }
        $weekMap = ['SU'=>0,'MO'=>1,'TU'=>2,'WE'=>3,'TH'=>4,'FR'=>5,'SA'=>6];
        $sp_slots = \App\Model\ServiceProviderSlotsDate::where([
            'service_provider_id'=>$user_id,
            'service_id'=>$service_id,
            'date'=>$date,
            'category_id'=>$current_category,
            'working_today'=>'y'
        ])
        ->where(function ($query) use($current_time) {
                $query->whereTime('end_time','>=',$current_time);
                $query->whereTime('start_time','<=',$current_time);
        })->get();
        if($sp_slots->count()>0){
            return true;
        } 
        $day = strtoupper(substr(Carbon::parse($datenow)->format('l'), 0, 2));
        $day_number = $weekMap[$day];
        $sp_slots = \App\Model\ServiceProviderSlot::where([
            'service_provider_id'=>$user_id,
            'service_id'=>$service_id,
            'day'=>$day_number,
            'category_id'=>$current_category,
        ])
        ->where(function ($query) use($current_time) {
                $query->whereTime('end_time','>=',$current_time);
                $query->whereTime('start_time','<=',$current_time);
        })
        ->get();
        if($sp_slots->count()>0){
            return true;
        }else{
            return false;
        }
    }

    public static function checkVendorsAvailableToday($user_ids){
        $datenow = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        $date= \Carbon\Carbon::now()->format('Y-m-d');
        $current_time = \Carbon\Carbon::now()->format('H:i:s');
        $current_category = [];
        $service_id = 1;
        $users = User::whereIn('id',$user_ids)->get();
        foreach ($users as $key => $user) {
            $category = $user->getCategoryData($user->id);
            if ($category) {
                $current_category[] = $category->id;
            }
        }
        $weekMap = ['SU'=>0,'MO'=>1,'TU'=>2,'WE'=>3,'TH'=>4,'FR'=>5,'SA'=>6];
        $sp_list_first = \App\Model\ServiceProviderSlotsDate::whereIn('service_provider_id',$user_ids)->whereIn('category_id',$current_category)->where([
            'service_id'=>$service_id,
            'date'=>$date,
            'working_today'=>'y'
        ])
        ->where(function ($query) use($current_time) {
                $query->whereTime('end_time','>=',$current_time);
                $query->whereTime('start_time','<=',$current_time);
        })->pluck('service_provider_id')->toArray();
        $day = strtoupper(substr(Carbon::parse($datenow)->format('l'), 0, 2));
        $day_number = $weekMap[$day];
        $sp_list_second = \App\Model\ServiceProviderSlot::whereIn('service_provider_id',$user_ids)->whereIn('category_id',$current_category)->where([
            'service_id'=>$service_id,
            'day'=>$day_number,
        ])
        ->where(function ($query) use($current_time) {
                $query->whereTime('end_time','>=',$current_time);
                $query->whereTime('start_time','<=',$current_time);
        })
        ->pluck('service_provider_id')->toArray();
        $sp_list_third = User::whereIn('id',$user_ids)->where('manual_available',1)->pluck('id')->toArray();

        $sp_lists = [];
        $sp_lists = array_merge($sp_list_first,$sp_list_second);
        $sp_lists = array_merge($sp_lists,$sp_list_third);



        return array_unique($sp_lists);

        return $sp_lists;


        return array_unique($sp_lists);


        return $sp_lists;

    }    

    public static function getHigherDoctors(){
        $doctors = [];
        $exist = self::checkFeatureExist([
            'client_id'=>Config::get('client_id'),
            'feature_name'=>'monthly plan'
        ]);
        if($exist){
             $datenow = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
             $res = SubscribePlan::query();
             $doctors = $res->where('expired_on','>',$datenow)
             ->whereHas('plan', function ($query) {
                return $query->whereIn('plan_id',[
                    'com.mp2r.premium','com.mp2r.executive'
                ]);
             });
             $doctors = $doctors->groupBy('user_id')->pluck('user_id')->toArray();

        }
        return $doctors;
    } 

    public static function getPaidDoctors($consultant_ids){
        $doctors = [];
        $exist = self::checkFeatureExist([
            'client_id'=>Config::get('client_id'),
            'feature_name'=>'monthly plan'
        ]);
        $ids_ordered = ['com.mp2r.executive','com.mp2r.premium'];
        $plan = Plan::whereIn('plan_id',$ids_ordered)->orderBy('plan_id','ASC')->pluck('id')->toArray();
        if($exist){
             $datenow = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
             $res = SubscribePlan::query();
             $doctors = $res->where('expired_on','>',$datenow)
             ->whereHas('plan', function ($query) use($ids_ordered) {
                return $query->whereIn('plan_id',$ids_ordered);
             })
             ->with('plan')->whereIn('user_id',$consultant_ids)->orderBy('plan_id','DESC');
             $doctors = $doctors->pluck('user_id')->toArray();

        }
        return $doctors;
    }

    public static function getOrderHigherDoctorPlan($plan_name,$consultant_ids){
        $doctors = [];
        $exist = self::checkFeatureExist([
            'client_id'=>Config::get('client_id'),
            'feature_name'=>'monthly plan'
        ]);
        if($exist){
            $ids_ordered = ['com.mp2r.executive','com.mp2r.premium'];
            $plan = Plan::whereIn('plan_id',$ids_ordered)->orderBy('plan_id','ASC')->pluck('id')->toArray();
            $doctors = SubscribePlan::orderBy("user_id","ASC")->groupBy('user_id')->pluck('user_id')->toArray();

        }
        return $doctors;
    }

    public static function getDocotorInsuranceByUser($user_id,$consultant_ids){
        $sp_ids = [];
        $userinsurance = UserInsurance::where(['user_id'=>$user_id])->first();
        if($userinsurance){
            $userinsurances = UserInsurance::query();
            $sp_ids = $userinsurances->where([
                'insurance_id'=>$userinsurance->insurance_id
            ])->whereIn('user_id',$consultant_ids)->groupBy('user_id')->pluck('user_id')->toArray();
            return array('check'=>true,'sp_ids'=>$sp_ids);
        }else{
            return array('check'=>false,'sp_ids'=>$sp_ids);
        }
    }

    public static function getDoctorInsurances($doc_id){
        $insurances = UserInsurance::where([
            'user_id'=>$doc_id
        ])->pluck('insurance_id')->toArray();
        return $insurances;
    }

    public static function twopoints_on_earth($lat_longs) 
      { 
           $long1 = deg2rad($lat_longs['user_long']); 
           $long2 = deg2rad($lat_longs['doctor_long']); 
           $lat1 = deg2rad($lat_longs['user_lat']); 
           $lat2 = deg2rad($lat_longs['doctor_lat']);
           $dlong = $long2 - $long1; 
           $dlati = $lat2 - $lat1; 
           $val = pow(sin($dlati/2),2)+cos($lat1)*cos($lat2)*pow(sin($dlong/2),2); 
           $res = 2 * asin(sqrt($val)); 
           $radius = 3958.756; 
           return number_format(($res*$radius* 1.60934),2);
      }

    public static function generateCardToken($request,$user){
        try{
            $stripe = new Stripe(env('STRIPE_TEST_KEY'));
            $token = $stripe->tokens()->create([
                'card' => [
                    'number'    => $request->card_number,
                    'exp_month' => $request->exp_month,
                    'cvc'       => $request->cvc,
                    'exp_year'  => $request->exp_year,
                ],
            ]);
            $fingerprint = $token['card']['fingerprint'];
            $card = $stripe->cards()->create($user->stripe_id, $token['id']);
            return ['status'=>"success",'statuscode' =>200,'message' => __('Card Added'),'card' =>$card];
        }catch(Exception $ex){
            return ['status' => "error",'statuscode' =>500,'message' => $ex->getMessage()];
        }
    }

    public static function createPayment($input,$user){
        try{
            $currency_code = 'INR';
            $requires_source_action = false;
            $currency = EnableService::where('type','currency')->first();
            if(isset($currency->value)){
                $currency_code = $currency->value;
            }
            $transaction_type = 'deposit';
            $message = 'Transaction failed';
            $key = env('STRIPE_TEST_KEY');
            $stripe_id = $user->stripe_id;
            $keys = self::getClientFeatureKeys('Payment Gateway','Stripe');
             if(isset($keys['STRIPE_MODE']) && $keys['STRIPE_MODE']=='test'){
                $key = $keys['STRIPE_TEST_KEY'];
             }elseif (isset($keys['STRIPE_MODE']) && $keys['STRIPE_MODE']=='live') {
                $stripe_id = $user->stripe_live_id;
                $key = $keys['STRIPE_LIVE_KEY'];
            }
            $stripe = new Stripe($key);
            $card = Card::select('id','card_id')->where(['id'=>$input['card_id'],'user_id'=>$user->id])->first();
            if(!$card){
                return array(
                    'status' =>"error",
                    'statuscode' => 400,
                    'message' =>__('Card Not Found')
                );
            }
            $paymentIntent = $stripe->paymentIntents()->create([
                'amount' => $input['balance'],
                'currency' => $currency_code,
                'customer'=>$stripe_id,
                'payment_method'=>$card->card_id,
                'confirm'=>true,
                'description' => 'Software development services',
                'shipping' => [
                    'name' => $user->name,
                    'address' => [
                      'line1' => '510 Townsend St',
                      'postal_code' => '98140',
                      'city' => 'San Francisco',
                      'state' => 'CA',
                      'country' => 'US',
                    ],
                  ],
            ]);
            $transaction = Transaction::create(array(
                'amount'=>$input['balance'],
                'transaction_type'=>$transaction_type,
                'status'=>'pending',
                'wallet_id'=>$user->wallet->id,
                'closing_balance'=>$user->wallet->balance,
            ));
            $transaction->raw_details = json_encode($input);
            $transaction->save();
            if($paymentIntent['id']){
                $transaction->transaction_id  = $paymentIntent['id'];
                $transaction->payment_gateway  = 'stripe';
                $transaction->module_table  = 'request_creation';
                $transaction->save();
            }
            if($paymentIntent['status']=='succeeded'){
                $url = null;
                $payment = Payment::create(array(
                    'from'=>1,
                    'to'=>$user->id,
                    'transaction_id'=>$transaction->id
                ));
                return array(
                        'status' => "success",
                        'statuscode' => 200,
                        'data'=>['transaction_id'=>$transaction->transaction_id,
                        'requires_source_action'=>$requires_source_action,
                        'url'=>$url],
                        'message' =>__('Please wait to confirming your appointment...')
                    );
            }else if($paymentIntent['status']=='requires_source_action'){
                $type = $paymentIntent['next_source_action']['type'];
                $url = null;
                if($type=='use_stripe_sdk'){
                    $url = $paymentIntent['next_source_action'][$type]['stripe_js'];
                }
                if($url){
                    $requires_source_action = true;
                }
                $payment = Payment::create(array(
                    'from'=>1,
                    'to'=>$user->id,
                    'transaction_id'=>$transaction->id
                ));
                return array(
                    'status' => "success",
                    'statuscode' => 200,
                    'data'=>['transaction_id'=>$transaction->transaction_id,
                    'requires_source_action'=>$requires_source_action,
                    'url'=>$url],
                    'message' =>__('Please wait to confirming your appointment...')
                );
            }else{
                $message = 'Payment Failed';
                $transaction->status = 'failed';
                $transaction->save();
                $payment = Payment::create(array(
                    'from'=>1,
                    'to'=>$user->id,
                    'transaction_id'=>$transaction->id
                ));
            }
            return array(
                'status' =>"error",
                'statuscode' => 400,
                'message' =>__($message)
            );
        }catch(Exception $ex){
            return [
                'status'=>"error",
                'statuscode' =>500,
                'message' => $ex->getMessage()
            ];
        }
    }


    public static function  encryptAES($str,$key)
    {

        $str = self::pkcs5_pad($str);
        $ivlen = openssl_cipher_iv_length($cipher="aes-256-cbc");
        $iv="PGKEYENCDECIVSPC";
        $encrypted = openssl_encrypt($str, "aes-256-cbc",$key, OPENSSL_ZERO_PADDING, $iv);
        $encrypted = base64_decode($encrypted);
        $encrypted = unpack('C*', ($encrypted));
        $encrypted=  self::byteArray2Hex($encrypted);
        $encrypted = urlencode($encrypted);
        return $encrypted;
    }

    public static function byteArray2Hex($byteArray) {
      $chars = array_map("chr", $byteArray);
      $bin = join($chars);
      return bin2hex($bin);
    }

    public static function pkcs5_pad ($text,$blocksize=256) 
    {
        $pad = $blocksize - (strlen($text) % $blocksize); 
        return $text . str_repeat(chr($pad), $pad);
    }

    public static function hex2ByteArray($hexString) {
      $string = hex2bin($hexString);
      return unpack('C*', $string);
    }

    public static function pkcs5_unpad($text,$blocksize=256)
    {
        $pad = $blocksize - strlen($text) % $blocksize;
        return $text . str_repeat(chr($pad), $pad);
    } 

    public static function decryptAES ($code, $key)
    {
        $code = self::hex2ByteArray(trim($code));
        $code=self::byteArray2String($code);
        $iv = "PGKEYENCDECIVSPC";
        $code = base64_encode($code);
        $decrypted = openssl_decrypt($code, 'AES-256-CBC', $key, OPENSSL_ZERO_PADDING,
        $iv);
        return self::pkcs5_unpad($decrypted);
    }

    public static function byteArray2String($byteArray) {
      $chars = array_map("chr", $byteArray);
      return join($chars);
    }

    public static function encrypt($str, $key)
    {
        $iv = str_repeat($zeroPack, 4);
        mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
        $encryptedStr = mcrypt_encrypt(
            MCRYPT_RIJNDAEL_128,
            hex2bin(md5($key)),
            pkcs5_pad($str, mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC)),
            MCRYPT_MODE_CBC,
            $iv)
        ;
        return bin2hex($encryptedStr);
    }

    public static function createPaymentByAlRajahiBank($input,$user){
        try{
            $requires_source_action = true;
            $responseURL = "https://homedoctor.royoconsult.com/al_rajhi_bank/webhook";
            $trackId = (string)time();
            $transaction_type = 'deposit';
            $message = 'Transaction failed';
            $textToEncrypt[] = [
                'id'=>'f7qs1EKUSi9N5j2',
                'password'=>'h$f3qEKrTE!$435',
                'action' => "1",
                'currencyCode'=>"682",
                'trackId'=>$trackId,
                'amt' => (string)$input['balance'],
                // 'amt' => "100",
                'errorURL'=>$responseURL,
                'responseURL'=>$responseURL,
            ];
            $tarndata = urlencode(json_encode($textToEncrypt));
            // print_r($tarndata);die;
            $data = [
                "textToEncrypt"=>$tarndata,
                "secretKey"=>"03368541659603368541659603368541",
                "mode"=>"CBC",
                "keySize"=>"256",
                "dataFormat"=>"Hex",
                "iv"=>"PGKEYENCDECIVSPC"
            ];
            $data = json_encode($data);
            $headers = array(
            'Content-type: multipart/form-data'
            );
            $post_data = array(
              "data" => $data
            ); 
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://www.devglan.com/online-tools/aes-encryption",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => $post_data,
              CURLOPT_HTTPHEADER =>$headers,
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                return [
                    'status'=>"error",
                    'statuscode' =>500,
                    'message' => $err,
                ];
            }
            $data = json_decode($response);
            $trandata = $data->output;
            $http_build_query[] =  [
                'id'=>'f7qs1EKUSi9N5j2',
                'errorURL'=>$responseURL,
                'responseURL'=>$responseURL,
                "trandata"=>$trandata,
            ];
            $http_build_query = json_encode($http_build_query);
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://securepayments.alrajhibank.com.sa/pg/payment/hosted.htm",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS =>$http_build_query,
              CURLOPT_HTTPHEADER => array(
                "content-type: application/json",
              ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                return [
                    'status'=>"error",
                    'statuscode' =>500,
                    'message' => $err,
                ];
            }
            $url = null;$payment_id = null;
            $result = json_decode($response);
            if(isset($result[0]) && $result[0]->status==1){
                $string_position = strpos($result[0]->result,"http");
                $url = substr($result[0]->result,$string_position);
                $payment_id = substr($result[0]->result,0,$string_position - 1);
                $url = $url.'?PaymentID='.$payment_id;
            }else{
                return [
                    'status'=>"error",
                    'statuscode' =>500,
                    'message' => "Something went wrong please try again",
                ];
            }
            $transaction = Transaction::create(array(
                'amount'=>$input['balance'],
                'transaction_type'=>$transaction_type,
                'status'=>'pending',
                'wallet_id'=>$user->wallet->id,
                'closing_balance'=>$user->wallet->balance,
            ));
            $transaction->raw_details = json_encode($input);
            $transaction->transaction_id  = $payment_id;
            $transaction->order_id  = $trackId;
            $transaction->payment_gateway  = 'al_rajhi_bank';
            if(isset($input['payment_type'])){
                $transaction->module_table  = $input['payment_type'];
                $transaction->request_id  = $input['request_id'];
            }else{
                $transaction->module_table  = 'request_creation';
            }
            $transaction->save();
            if($requires_source_action){
                $payment = Payment::create(array(
                    'from'=>1,
                    'to'=>$user->id,
                    'transaction_id'=>$transaction->id
                ));
                return array(
                    'status' => "success",
                    'statuscode' => 200,
                    'data'=>['transaction_id'=>$transaction->transaction_id,
                    'requires_source_action'=>$requires_source_action,
                    'url'=>$url],
                    'message' =>__('Payment Success...')
                );
            }
            return array(
                'status' =>"error",
                'statuscode' => 400,
                'message' =>__($message)
            );
        }catch(Exception $ex){
            return [
                'status'=>"error",
                'statuscode' =>500,
                'message' => $ex->getMessage()." Line".$ex->getLine(),
            ];
        }
    }

    public static function insertRequestDetail($request_id,$input){
        $requestdetail= \App\Model\RequestDetail::firstOrCreate(['request_id'=>$request_id]);
        if($requestdetail){
            $requestdetail->first_name =  isset($input->first_name)?$input->first_name:null;
            $requestdetail->last_name =  isset($input->last_name)?$input->last_name:null;
            $requestdetail->service_for =  isset($input->service_for)?$input->service_for:null;
            $requestdetail->home_care_req =  isset($input->home_care_req)?$input->home_care_req:null;
            $requestdetail->service_address =  isset($input->service_address)?$input->service_address:null;
            $requestdetail->lat =  isset($input->lat)?$input->lat:null;
            $requestdetail->long =  isset($input->long)?$input->long:null;
            $requestdetail->reason_for_service =  isset($input->reason_for_service)?$input->reason_for_service:null;
            $requestdetail->country_code =  isset($input->country_code)?$input->country_code:null;
            $requestdetail->phone_number =  isset($input->phone_number)?$input->phone_number:null;
        }
        if(isset($input->duties)){
            $duties_raw = [
                "duties"=>explode(",",$input->duties)
            ];
            $custom_info = new \App\Model\CustomInfo();
            $custom_info->raw_detail = json_encode($duties_raw);
            $custom_info->info_type = 'duties';
            $custom_info->ref_table = 'requests';
            $custom_info->ref_table_id = $request_id;
            $custom_info->status = 'success';
            $custom_info->save();
        }
        $requestdetail->save();
    }

    public static function checkSlotFullOrNot($slot_duration,$timezone,$add_slot_second,$input,$request_data=null){
        $dateznow = new DateTime("now", new DateTimeZone('UTC'));
        $date = self::roundToNearestMinuteInterval($dateznow,$slot_duration->value);
        $datenow = $date->format('Y-m-d H:i:s');
        $user_time_zone_slot = Carbon::parse($datenow)->setTimezone($timezone)->format('h:i a');
        $user_time_zone_date = Carbon::parse($datenow)->setTimezone($timezone)->format('Y-m-d');
        $end_time_slot_utcdate = Carbon::parse($datenow)->addSeconds($add_slot_second)->setTimezone('UTC')->format('Y-m-d H:i:s');
        $exist = \App\Model\Request::where('to_user',$input->consultant_id)
        ->whereBetween('booking_date', [$datenow, $end_time_slot_utcdate])
        ->whereHas('requesthistory', function ($query) {
            $query->where('status','!=','canceled');
        })
        ->where(function($query2) use ($request_data){
            if(isset($request_data->id))
                $query2->where('id','!=',$request_data->id);
        })
        ->get();
        if($exist->count()>0){
            return false;
        }else{
            return array(
                'user_time_zone_slot'=>$user_time_zone_slot,
                'count'=>$exist->count(),
                'user_time_zone_date'=>$user_time_zone_date,
                'datenow'=>$datenow
            );
        }
    }

    public static function extraPayment($user_trans){
        $status = 'succeeded';
        $user_trans->status = 'success';
        $user_trans->save();
        $request_data = RequestData::where(['id'=>$user_trans->request_id])->first();
        $deposit_to = array(
            'balance'=>$user_trans->balance,
            'user'=>$request_data->sr_info,
            'from_id'=>$request_data->cus_info->id,
            'request_id'=>$request_data->id,
            'status'=>'vendor-pending',
            'transaction_id'=>$user_trans->transaction_id,
        );
        Transaction::createDeposit($deposit_to);
        return [
                'status' => "success",
                'statuscode' => 200,'message' => __('Extra Payment Done'),
                'data'=>['amountNotSufficient'=>false,'total_charges'=>$user_trans->balance]
            ];
    }

    public static function createRequest($raw_details,$transaction_id){
        $user_trans = Transaction::where('id',$transaction_id)->first();
        $request = json_decode($raw_details);
        $timezone = $request->timezone;
        $user = $request->user;
        $spservicetype_id = null;
        $consult = User::find($request->consultant_id);
        $category_id = $consult->getCategoryData($consult->id);
        if($category_id){
            $categoryservicetype_id = \App\Model\CategoryServiceType::where([
                'category_id'=>$category_id->id,
                'service_id'=>$request->service_id,
            ])->first();
            if($categoryservicetype_id){
                $spservicetype_id = \App\Model\SpServiceType::where([
                    'category_service_id'=>$categoryservicetype_id->id,
                    'sp_id'=>$consult->id
                ])->first();
            }
        }
        $slot_duration = \App\Model\EnableService::where('type','slot_duration')->first();
        $add_slot_second = $slot_duration->value * 60;
        $unit_price = \App\Model\EnableService::where('type','unit_price')->first();
        $per_minute = $request->per_minute;
        $total_hours = $request->total_hours;
        $total_charges = $request->total_charges;
        $grand_total= $g_total = $request->grand_total;
        if($request->schedule_type=='schedule'){
            if(isset($request->request_type)){
                $dates = explode(',',$request->dates);
                $datenow = Carbon::parse($dates[0].' '.$request->start_time,$timezone)->setTimezone('UTC')->format('Y-m-d H:i:s');
            }else{
                $datenow = Carbon::parse($request->date.' '.$request->time,$timezone)->setTimezone('UTC')->format('Y-m-d H:i:s');
            }
        }else{
            $data = [];
            while ($data==false) {
                $data = self::checkSlotFullOrNot($slot_duration,$timezone,$add_slot_second,$request);
                $slot_duration->value = $slot_duration->value + $slot_minutes;
            }
            $datenow = $data['datenow'];
        }
        $message = 'Something went wrong';
        // print_r($datenow);die;
        // print_r($spservicetype_id);die;
        $sr_request = new \App\Model\Request();
        $sr_request->from_user = $user->id;
        $sr_request->booking_date = $datenow;
        $sr_request->to_user = $request->consultant_id;
        $sr_request->service_id = $request->service_id;
        $sr_request->sp_service_type_id = ($spservicetype_id)?$spservicetype_id->id:null;
        if(isset($request->request_type)){
            $sr_request->request_type = $request->request_type;
            $sr_request->total_hours = $total_hours;
            $sr_request->payment = 'success';
        }
        if(isset($request->filter_id)){
            $sr_request->request_category_type = 'filter_option';
            $sr_request->request_category_type_id = $request->filter_id;
        }

        if($sr_request->save()){

            /* Requests Dates Saving... */
            if(isset($request->request_type)){
               self::insertRequestDetail($sr_request->id,$request);
                $dates = explode(',',$request->dates);
                foreach ($dates as $key => $date) {
                    $start_time_multi = Carbon::parse($date.' '.$request->start_time,$timezone)->setTimezone('UTC')->format('Y-m-d H:i:s');
                    $end_time_multi = Carbon::parse($date.' '.$request->end_time,$timezone)->setTimezone('UTC')->format('Y-m-d H:i:s');
                    $requestdate  = new \App\Model\RequestDate();
                    $requestdate->request_id = $sr_request->id;
                    $requestdate->start_date_time = $start_time_multi;
                    $requestdate->end_date_time = $end_time_multi;
                    $requestdate->save();
                }
            }
            $requesthistory = new \App\Model\RequestHistory();
            $requesthistory->duration = 0;
            $requesthistory->total_charges = $grand_total;
            $requesthistory->service_tax = $request->service_tax;
            $requesthistory->tax_percantage = $request->tax_percantage;
            $requesthistory->discount = $request->discount;
            $requesthistory->without_discount = $request->total_charges;
            $requesthistory->schedule_type = $request->schedule_type;
            $requesthistory->status = 'pending';
            $requesthistory->request_id = $sr_request->id;
            if(isset($request->coupon_validation->status) && $request->coupon_validation->status=='success'){
                $requesthistory->coupon_id = $request->coupon_validation->coupon_id;
                $couponused = new \App\Model\CouponUsed();
                $couponused->user_id =  $user->id;
                $couponused->coupon_id =  $request->coupon_validation->coupon_id;
                $couponused->save();
            }
            if($requesthistory->save()){
                $status = 'succeeded';
                $user_trans->status = 'success';
                $user_trans->request_id = $sr_request->id;
                $user_trans->save();

                $deposit_to = array(
                    'balance'=>$grand_total,
                    'user'=>$sr_request->sr_info,
                    'from_id'=>$sr_request->cus_info->id,
                    'request_id'=>$sr_request->id,
                    'status'=>'vendor-pending',
                    'transaction_id'=>$user_trans->transaction_id,
                );
                Transaction::createDeposit($deposit_to);
            }
            if($request->consultant_id){
                $notification = new \App\Notification();
                $notification->sender_id = $user->id;
                $notification->receiver_id = $request->consultant_id;
                $notification->module_id = $sr_request->id;
                $notification->module ='request';
                $notification->notification_type ='NEW_REQUEST';
                $message = __('notification.new_req_text', ['user_name' => $user->name,'service_type'=>'']);
                $notification->message = $message;
                $notification->save();
                $notification->push_notification(array($request->consultant_id),array(
                    'pushType'=>'NEW_REQUEST',
                    'transaction_id'=>$user_trans->transaction_id,
                    'request_id'=>$sr_request->id,
                    'is_second_oponion'=>false,
                    'message'=>$message
                ));
            }
            if($user->id){
                $notification = new \App\Notification();
                $notification->sender_id = $request->consultant_id;
                $notification->receiver_id = $user->id;
                $notification->module_id = $sr_request->id;
                $notification->module ='request';
                $notification->notification_type ='BOOKING_RESERVED';
                $notification->message =__('notification.booking_amount_de_text', ['amount' => $grand_total]);
                $notification->save();
                $notification->push_notification(array($user->id),array(
                    'pushType'=>'BOOKING_RESERVED',
                    'transaction_id'=>$user_trans->transaction_id,
                    'request_id'=>$sr_request->id,
                    'is_second_oponion'=>false,
                    'message'=>__('notification.booking_amount_de_text', ['amount' => $grand_total]),
                ));
            }
            return [
                'status' => "success",
                'statuscode' => 200,'message' => __('New Request Created '),
                'data'=>['amountNotSufficient'=>false,'total_charges'=>$total_charges]
            ];
        }
            
    }

    
}

  function dataformatchange($date){

    } 


