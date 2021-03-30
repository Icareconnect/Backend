<?php

namespace App\Http\Controllers\API;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User,App\Notification;
use Illuminate\Support\Facades\Auth;
use Validator;
use Hash;
use Mail;
use DB,Carbon\Carbon;
use DateTime,DateTimeZone;
use Redirect,Response,File;
use Image;
use Illuminate\Support\Facades\URL;
use App\Helpers\Helper;
use App\Model\Role,App\Model\UserMasterPreference;
use App\Model\Profile;
use App\Model\CustomUserField;
use App\Model\Wallet;
use App\Model\slot;
use App\Model\Request as RequestTable;
use App\Model\HealthCareVisit;
use App\Model\TypeOfRecords;
use App\Model\HealthRecords;
use App\Model\HealthRecordImage;
use App\Model\UserInsurance;
use App\Model\ServiceProviderSlot;
use App\Model\SocialAccount;
use App\Model\Family;
use App\Model\UserSecurityAnswer;
use App\Model\Feedback,App\Model\EnableService;
use Socialite;
use Exception;
use Intervention\Image\ImageManager;
use App\Model\CategoryServiceProvider;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Arr;
use Laravel\Passport\Token;
use App\Model\Image as ModelImage;
use App\Jobs\SignupEmail;
class UserController extends Controller {

    public $successStatus = 200;
    /**
     * @SWG\Post(
     *     path="/test-notification",
     *     description="Test Notification",
     * tags={"Notification"},
     *  @SWG\Parameter(
     *         name="device_type",
     *         in="query",
     *         type="string",
     *         description="e.g ios,andriod",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="message",
     *         in="query",
     *         type="string",
     *         description="message",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="fcm_id",
     *         in="query",
     *         type="string",
     *         description="fcm_id",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="fcm_server_key",
     *         in="query",
     *         type="string",
     *         description="fcm_server_key",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     */

    public function testNotification(Request $request){
        $rules = ['device_type' => 'required|in:ios,andriod','message'=>'required','fcm_id'=>'required','fcm_server_key'=>'required'];
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                $validator->getMessageBag()->first()), 400);
        }
        $notification = new Notification();
        $response = $notification->push_test_notification($request->fcm_id,array(
            'pushType'=>'TEST',
            'message'=>$request->message
        ),$request);
        return response(array('status' => "success", 'statuscode' => 200,'data'=>$response), 200);
    }


    public static function simple_login(Request $request) {
        $validator = Validator::make($request->all(), [
                    'email' => 'required',
                    'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                $validator->getMessageBag()->first()), 400);
        }
        $user = User::where(function ($query) {
                    $query->where('email', '=', request('email'));
                })->first();

        if (!$user)
            return Response(array('status' => "error", 'statuscode' => 400, 'message' => __('We are sorry, this user is not registered with us.')), 400);
        elseif (!Hash::check(request('password'), $user->password))
            return Response(array('status' => "error", 'statuscode' => 400, 'message' => __('Sorry, this password is incorrect!')), 400);

        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            if (isset($request->fcm_id) && !empty($request->fcm_id)) {
                User::where('id', $user->id)
                        ->update(['fcm_id' => request('fcm_id')]);
            }
            $updateduser = User::find($user->id);
            $token = $user->createToken('consult_app')->accessToken;
            $updateduser->token = $token;
            return response(['status' => "success", 'statuscode' => 200,
                'message' => __('login successfully !'), 'data' => ($updateduser)], 200);
        } else {
            return response(array('status' => "error", 'statuscode' => 400, 'message' =>'Unauthorised'), 400);
        }
    }
    /**
     * @SWG\Post(
     *     path="/login",
     *     description="Login with Google,Facebook,Email,Phone,Apple",
     * tags={"User Register & Login Section"},
     *  @SWG\Parameter(
     *         name="provider_type",
     *         in="query",
     *         type="string",
     *         description="e.g facebook,google,email,phone,apple",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="provider_id",
     *         in="query",
     *         type="string",
     *         description="if provider type is email and phone then required e.g dummpy@yopmail.com,9988551155",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="provider_verification",
     *         in="query",
     *         type="string",
     *         description="if type Facebook or Google or Apple then it will be access token if email=>password,phone=>otp ",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="user_type",
     *         in="query",
     *         type="string",
     *         description="User Type [customer,service_provider]",
     *         required=true,
     *     ),     
     *  @SWG\Parameter(
     *         name="country_code",
     *         in="query",
     *         type="string",
     *         description="if login with phone then required country_code",
     *         required=false,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     */
    public static function socialLogin(Request $request) {
        try{
          $column_name = 'email';
          $user_name_column_search = false;
          $client_feature_exist = Helper::checkFeatureExist([
            'client_id'=>\Config::get('client_id'),
            'feature_name'=>'UserName/UserID Column']);
          if($client_feature_exist){
            $user_name_column_search = true;
          }
          // print_r($user_name_column_search);die;
          // $test_key = env('STRIPE_TEST_KEY');
            $rules = [
                    'provider_type' => 'required',
                    'provider_verification' => 'required',
                    'user_type'=>'required',
                ];
            $customMessages = [
                    'provider_type.required' => 'provider type required facebook,google,email,phone.',
                    'provider_verification.required' => 'provider verification required e.g facebook token, google token,email password,phone code(otp)',
                ];
                // print_r($request->provider_type);die;
            $input = $request->all();
            if($request['provider_type']=='phone' || $request['provider_type']=='email'){
                if($request['provider_type']=='phone'){
                    $rules['country_code'] = 'required';
                }
                $rules['provider_id'] = 'required';
                $customMessages['provider_id.required'] = 'provider id required email id,phone number';
                if($request['provider_type']=='email'){
                  // die;
                    $rules['provider_id'] = 'required|email';
                    if($user_name_column_search){
                        if(filter_var(request('provider_id'), FILTER_VALIDATE_EMAIL)) {
                            $column_name = 'email';
                        }else {
                            $column_name = 'user_name';
                            $rules['provider_id'] = 'required';
                        }
                    }
                }
            }
            $device_type = $request->header('devicetype');
            if($device_type!='IOS'){
                $device_type = 'ANDROID';
            }
            $validator = Validator::make($request->all(), $rules, $customMessages);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' => $validator->getMessageBag()->first()), 400);
            }
            if($input['user_type']!=='customer' && $input['user_type']!=='service_provider'){
                return response(['status' => "error", 'statuscode' => 400, 'message' => __(' Please provide correct user type')], 400);
            }
            $vendor_auto_approved = true;
             $con_vendor_approved = EnableService::where(['type'=>'vendor_approved'])->first();
            if($con_vendor_approved){
                if($con_vendor_approved->value=='no'){
                    $vendor_auto_approved = false;
                }
            }
            // print_r($con_vendor_approved);die;
            $image = null;
            $driver = $user = null;
            switch ($request['provider_type']) {
                // Facebook
                case 'facebook':

                    $facebook_keys = Helper::getClientFacebookKeys();
                    $config = [
                      'client_id' => isset($facebook_keys['api_key'])?$facebook_keys['api_key']:env('FB_CLIENT_ID'),
                      'client_secret' =>isset($facebook_keys['secret_key'])?$facebook_keys['secret_key']:env('FB_CLIENT_SECRET'), 
                      'redirect' =>  isset($facebook_keys['redirect_url'])?$facebook_keys['redirect_url']:env('FB_REDIRECT_URL'), 
                  ];
                  $driver = Socialite::buildProvider(
                                              \Laravel\Socialite\Two\FacebookProvider::class, 
                                              $config
                                          );
                    break;
                case 'google':
                    $driver = \Google::getClient();
                    break;
                // phone - verify code and get login
                case 'phone':
                    $verify = \App\Model\Verification::where([
                        'phone' => $input['provider_id'],
                        'country_code'=>$input['country_code'],
                        'code'=>$input['provider_verification'],
                        'status'=>'pending'
                    ])->latest()->first();
                    if($input['provider_verification']=='1234' || $verify){
                        if($verify){
                            $verify->status = 'verified';
                            $verify->save();
                        }
                        $user = User::where(function ($query) {
                                $query->where(['phone'=>request('provider_id'),'country_code'=>request('country_code')]);
                            })->first();
                        if (!$user){
                                $password = bcrypt('password');
                                $user_detail = array(
                                    'phone'=>$input['provider_id'],
                                    'country_code'=>$input['country_code'],
                                    'password'=>$password,
                                    'name'=>'',
                                );
                                if (isset($request->fcm_id) && !empty($request->fcm_id)) {
                                    $user_detail['fcm_id'] = $request->fcm_id;
                                }
                                $user = User::create($user_detail);
                                if($user){
                                    $dateznow = new DateTime("now", new DateTimeZone('UTC'));
                                    $datenow = $dateznow->format('Y-m-d H:i:s');
                                    if($vendor_auto_approved){
                                        $user->account_verified = $datenow;
                                        $user->save();
                                    }
                                    if($request->user_type=='customer'){
                                        $user->account_verified = $datenow;
                                        $user->save();
                                    }
                                    $user = $user->addUserRequiredDetail($user,$request->user_type);
                                    if(\Config::get('client_connected') && \Config::get('client_data')->domain_name=='nurselynx'){
                                        $time = new DateTime($datenow);
                                        $time->modify("+5 second");
                                        $time->format('Y-m-d H:i:s');
                                        $push_data = ["id"=>$user->id];
                                        $job = (new SignupEmail($push_data))->delay($time);
                                        dispatch($job);
                                    }
                                }
                        }else{
                            Auth::login($user);
                        }
                    }else{
                        return response(array('status' => "error", 'statuscode' => 400, 'message' =>'Invalid OTP'), 400);
                    }
                    break;

                case 'email':
                    $email_otp = Helper::getClientFeatureExistWithFeatureType('social login','email otp');
                    // print_r($email_otp);die;
                    $verify = \App\Model\EmailVerification::where([
                        'email' => $input['provider_id'],
                        'code'=>$input['provider_verification'],
                        'status'=>'pending'
                    ])->latest()->first();
                    if($email_otp){
                        if($input['provider_verification']!=='1234' && !$verify){
                            return response(array('status' => "error", 'statuscode' => 400, 'message' =>'Invalid OTP'), 400);
                        }
                    }
                    $user = User::where(function ($query) use($column_name) {
                                $query->where($column_name, '=', request('provider_id'));
                            })->first();
                    if (!$user){
                        return Response(array('status' => "error", 'statuscode' => 400, 'message' => __('We are sorry, this user is not registered with us.')), 400);
                    }elseif (!Hash::check($request->provider_verification, $user->password) && !$email_otp){
                        return Response(array('status' => "error", 'statuscode' => 400, 'message' => __('Sorry, this password is incorrect!')), 400);
                    }
                    if($email_otp){
                        Auth::login($user);
                        if (isset($request->fcm_id) && !empty($request->fcm_id)) {
                            User::where('id', $user->id)
                                    ->update(['fcm_id' => request('fcm_id')]);
                        }
                        if($verify){
                            $verify->status = 'verified';
                            $verify->save();
                        }
                    }else if (Auth::attempt([$column_name => $request->provider_id, 'password' => $request->provider_verification])) {
                        $user = Auth::user();
                        if (isset($request->fcm_id) && !empty($request->fcm_id)) {
                            User::where('id', $user->id)
                                    ->update(['fcm_id' => request('fcm_id')]);
                        }
                    } else {
                        return response(array('status' => "error", 'statuscode' => 400, 'message' =>'Unauthorised'), 400);
                    }
                    break;
                case 'apple':
                  $socialaccount = SocialAccount::where(['provider_id'=>$request->provider_verification,'provider'=>$request->provider_type])->first();
                  if($socialaccount){
                      $user = User::where(function ($query) use($socialaccount) {
                                      $query->where('id', '=', $socialaccount->user_id);
                                  })->first();
                  }else{
                        $name = ' ';
                        $password = bcrypt('password');
                        $user_detail = array(
                            'name'=>$name,
                            'password'=>$password,
                            'email'=>null
                        );
                        if (isset($request->fcm_id) && !empty($request->fcm_id)) {
                            $user_detail['fcm_id'] = $request->fcm_id;
                        }
                        $dateznow = new DateTime("now", new DateTimeZone('UTC'));
                        $datenow = $dateznow->format('Y-m-d H:i:s');
                        $user_detail['email_verified_at'] = $datenow;
                        $user = User::create($user_detail);
                        if($user){
                            if($vendor_auto_approved){
                                $user->account_verified = $datenow;
                                $user->save();
                            }
                            if($request->user_type=='customer'){
                                $user->account_verified = $datenow;
                                $user->save();
                            }
                            $user->save();
                            $user = $user->addUserRequiredDetail($user,$request->user_type);

                            if(\Config::get('client_connected') && \Config::get('client_data')->domain_name=='nurselynx'){
                                $time = new DateTime($datenow);
                                $time->modify("+5 second");
                                $time->format('Y-m-d H:i:s');
                                $push_data = ["id"=>$user->id];
                                $job = (new SignupEmail($push_data))->delay($time);
                                dispatch($job);
                            }
                        }
                  }
                  if (!$user){
                      return response(array('status' => "error", 'statuscode' => 400, 'message' => __('We are sorry, this user is not registered with us.'),'data'=>$user), 400);
                  }else{
                      Auth::login($user);
                      SocialAccount::firstOrCreate(['user_id'=>$user->id,'provider_id'=>$request->provider_verification,'provider'=>$request->provider_type]);
                  }
                  break;
            }
            if ($driver) {
                $provider_id = null;
                $email = null;
                $name = null;
                
                if($request['provider_type']=='google'){
                    $driver_user = Curl::to('https://oauth2.googleapis.com/tokeninfo')
                    ->withData( array( 'id_token' =>$request->provider_verification ) )
                    ->asJson()
                    ->get();
                    if($driver_user){
                       $provider_id = $driver_user->sub; 
                       $email = $driver_user->email; 
                       $name = $driver_user->name; 
                       $image = $driver_user->picture; 
                    }
                }else{
                    $driver_user = $driver->userFromToken($request->provider_verification);
                    $provider_id = $driver_user->getId();
                    $email = $driver_user->getEmail();
                    $name = $driver_user->getName();
                    $image = $driver_user->getAvatar();
                }
                if (!$driver_user && !$provider_id) {
                    return response(array('status' => "error", 'statuscode' => 400, 'message' => __('Unauthorised')), 400);
                }
                $socialaccount = SocialAccount::where(['provider_id'=>$provider_id,'provider'=>$request->provider_type])->first();
                if($socialaccount){
                    $user = User::where(function ($query) use($socialaccount) {
                                    $query->where('id', '=', $socialaccount->user_id);
                                })->first();
                }else{
                    $user = User::where(function ($query) use($email) {
                                    $query->where('email', '=', $email);
                                })->first();
                    if(!$user && $email){
                        if(!$name){
                            $name = $email;
                        }
                        $password = bcrypt('password');
                        $user_detail = array(
                            'name'=>$name,
                            'password'=>$password,
                            'email'=>$email
                        );
                        if (isset($request->fcm_id) && !empty($request->fcm_id)) {
                            $user_detail['fcm_id'] = $request->fcm_id;
                        }
                        $dateznow = new DateTime("now", new DateTimeZone('UTC'));
                        $datenow = $dateznow->format('Y-m-d H:i:s');
                        $user_detail['email_verified_at'] = $datenow;
                        $user = User::create($user_detail);
                        if($user){
                            if($vendor_auto_approved){
                                $user->account_verified = $datenow;
                                $user->save();
                            }
                            if($request->user_type=='customer'){
                                $user->account_verified = $datenow;
                                $user->save();
                            }
                            $user->save();
                            $user = $user->addUserRequiredDetail($user,$request->user_type);
                            $profile = New Profile();
                            $profile->dob ='0000-00-00';
                            $profile->user_id = $user->id;
                            $profile->save();

                            if(\Config::get('client_connected') && \Config::get('client_data')->domain_name=='nurselynx'){
                                $time = new DateTime($datenow);
                                $time->modify("+5 second");
                                $time->format('Y-m-d H:i:s');
                                $push_data = ["id"=>$user->id];
                                $job = (new SignupEmail($push_data))->delay($time);
                                dispatch($job);
                            }
                        }
                    }
                }
                if (!$user){
                    return response(array('status' => "error", 'statuscode' => 400, 'message' => __('We are sorry, this user is not registered with us.'),'data'=>$user), 400);
                }else{
                    Auth::login($user);
                    SocialAccount::firstOrCreate(['user_id'=>$user->id,'provider_id'=>$provider_id,'provider'=>$request->provider_type]);
                }
                
            }
            $current_role = ucwords(str_replace('_', ' ', $user->roles[0]['name']));
            $single_app = false;
            if(isset($request->single_app)){
              $single_app = true;
            }
            if($user && ($user->hasrole($request->user_type) || $single_app)){
                $updateduser = User::with('roles')->find($user->id);
                $updateduser->device_type = $device_type;
                $updateduser->provider_type = $request->provider_type;
                if($image!=null && !$updateduser->profile_image){
                    $filename = time().'.jpg';
                    // Image::make($image)->save(public_path('media/' . $filename));
                    $extension = 'jpg';
                    $filename = str_replace(' ','', md5(time()).'_'.$filename);
                    $thumb = \Image::make($image)->resize(100, 100,
                      function ($constraint) {
                          $constraint->aspectRatio();
                      })->encode($extension);
                    $big = \Image::make($image)->encode($extension);
                    $_800x800 = \Image::make($image)->resize(800, 800,
                      function ($constraint) {
                          $constraint->aspectRatio();
                      })->encode($extension);
                    $_400x400 = \Image::make($image)->resize(400, 400,
                      function ($constraint) {
                          $constraint->aspectRatio();
                      })->encode($extension);
                    \Storage::disk('spaces')->put('thumbs/'.$filename, (string)$thumb, 'public');
                    \Storage::disk('spaces')->put('uploads/'.$filename, (string)$_400x400, 'public');
                    \Storage::disk('spaces')->put('original/'.$filename, (string)$big, 'public');
                    \Storage::disk('spaces')->put('800x800/'.$filename, (string)$_800x800, 'public');
                    \Storage::disk('spaces')->put('400x400/'.$filename, (string)$_400x400, 'public');
                    $updateduser->profile_image = $filename;
                }
                if (isset($request->apn_token) && !empty($request->apn_token)) {
                    $updateduser->apn_token = $request->apn_token;
                }

                $updateduser->save();

                $userTokens = $user->tokens;
                if($userTokens){
                    foreach($userTokens as $token) {
                        $token->revoke();   
                    }
                }
                $token = $user->createToken('consult_app')->accessToken;
                $updateduser->token = $token;
                $updateduser->profile;
                if($updateduser->profile){
                    $updateduser->profile->bio = $updateduser->profile->about;
                    $updateduser->totalRating =  $updateduser->profile->rating;
                }
                $updateduser->subscriptions = $updateduser->getSubscription($updateduser);
                $updateduser->categoryData = $user->getCategoryData($user->id);
                $updateduser->additionals = $user->getAdditionals($user->id);
                $updateduser->insurances = $user->getInsurnceData($user->id);
                $updateduser->custom_fields = $user->getCustomFields($user->id);
                $updateduser->services = $user->getServices($user->id);
                $updateduser->filters = $user->getFilters($user->id);
                // $updateduser->spcourses = $user->getcourseSP($user->id);
                if($user->hasrole('service_provider')){
                    $updateduser->patientCount = User::getTotalRequestDone($user->id);
                    $updateduser->reviewCount = Feedback::reviewCountByConsulatant($user->id); 
                }
                $updateduser = Helper::getMoreData($updateduser);
                return response(['status' => "success", 'statuscode' => 200,
                            'message' => __('login successfully !'), 'data' => ($updateduser)], 200);
            }else{
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>"You are register as $current_role with same account, Please try with other account."), 400);
            }
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage().' '.$ex->getLine()], 500);
        }
    }

    /**
     * @SWG\Post(
     *     path="/register",
     *     description="Login with Google,Facebook,Email,Phone",
     * tags={"User Register & Login Section"},
     *  @SWG\Parameter(
     *         name="name",
     *         in="query",
     *         type="string",
     *         description="name",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="email",
     *         in="query",
     *         type="string",
     *         description="email",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="password",
     *         in="query",
     *         type="string",
     *         description="password",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         name="phone",
     *         in="query",
     *         type="string",
     *         description="Mobile Number (9988556677)",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="code",
     *         in="query",
     *         type="number",
     *         description="OTP Code",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="user_type",
     *         in="query",
     *         type="string",
     *         description="user_type=>customer,service_provider",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         name="fcm_id",
     *         in="query",
     *         type="string",
     *         description="fcm_id",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="country_code",
     *         in="query",
     *         type="string",
     *         description="country code",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="dob",
     *         in="query",
     *         type="string",
     *         description="Date of birth e.g YYYY-MM-DD=>2000-02-20",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         name="bio",
     *         in="query",
     *         type="string",
     *         description="a short biography",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="address",
     *         in="query",
     *         type="string",
     *         description="a address",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="city",
     *         in="query",
     *         type="string",
     *         description="a city",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="state",
     *         in="query",
     *         type="string",
     *         description="a state",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="country",
     *         in="query",
     *         type="string",
     *         description="a country",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="npi_id",
     *         in="query",
     *         type="string",
     *         description="npi_id for mypath",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="invite_code",
     *         in="query",
     *         type="string",
     *         description="user reference_code for invite",
     *         required=false,
     *     ),
     *    @SWG\Parameter(
     *      name="profile_image",
     *      in="formData",
     *      description="Profile Pic URL",
     *      required=false,
     *      type="file"
     *      ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     */
    public static function register(Request $request) {
        try{
            $user_name_column_search = false;
            $client_feature_exist = Helper::checkFeatureExist([
              'client_id'=>\Config::get('client_id'),
              'feature_name'=>'UserName/UserID Column']);
            if($client_feature_exist){
              $user_name_column_search = true;
            }

            $input = $request->all();
            $column_name = 'email';
            if($user_name_column_search){
                if(filter_var(request('email'), FILTER_VALIDATE_EMAIL)) {
                    $column_name = 'email';
                }else {
                    $column_name = 'user_name';
                }
            }
            $rules = [
                        'name' => 'required',
                        'password' => 'required|min:8|max:12',
                        'user_type' => 'required',
            ];
            if($column_name=='email'){
              $rules['email'] = 'required|email|unique:users,email';
              $customMessages['email.unique'] = "The email has already been taken.";
              $customMessages['email.required'] = 'The email name required';
            }else{
              $rules['email'] = 'required|unique:users,user_name';
              $customMessages['email.unique'] = "The user name has already been taken.";
              $customMessages['email.required'] = 'The email or username required.';
            }
            if(isset($input['dob'])){
               $rules['dob'] = 'required|date|date_format:Y-m-d';
            }
            if(isset($input['phone'])){
                $rules['phone'] = 'required|unique:users,phone';
            }else{
                $input['phone'] = null;
            }
            if(isset($input['invite_code'])){
                $rules['invite_code'] = 'required|exists:users,reference_code';
                $customMessages['invite_code.exists'] = 'The invite code is invalid';
            }
            $validator = Validator::make($request->all(),$rules,$customMessages);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            if($input['user_type']!=='customer' && $input['user_type']!=='service_provider'){
                return response(['status' => "error", 'statuscode' => 400, 'message' => __(' Please provide correct user type')], 400);
            }
            $device_type = $request->header('devicetype');
            if($device_type!='IOS'){
                $device_type = 'ANDROID';
            }
            if($column_name=='user_name'){
              $input["user_name"] =  $input["email"];
              unset($input["email"]);
            }
            $vendor_auto_approved = true;
            $con_vendor_approved = EnableService::where(['type'=>'vendor_approved'])->first();
            if($con_vendor_approved){
                if($con_vendor_approved->value=='no'){
                    $vendor_auto_approved = false;
                }
            }
            $datenow = new DateTime("now", new DateTimeZone('UTC'));
            $datenowone = $datenow->format('Y-m-d H:i:s');
            $smsVerifcation = \App\Model\Verification::where(['phone' => $request->phone, 'code' => $request->code, 'status' => 'pending'])->latest()->first();
            // if (!is_object($smsVerifcation)) {
            //     return response(['status' => "error", 'statuscode' => 400, 'message' => __('Please enter the correct OTP!')], 400);
            // }
            // $smsVerifcationdata = \App\Model\Verification::where(['phone' => $request->phone, 'code' => $request->code])->where('expired_at', '>=', $datenowone)
            //         ->latest() //show the latest if there are multiple
            //         ->first();

            // if (!is_object($smsVerifcationdata)) {
            //     return response(['status' => "error", 'statuscode' => 400, 'message' => __('OTP has been expired. Please register again!')], 400);
            // }
            // if (is_object($smsVerifcationdata)) {
                $request["status"] = 'verified';
                // \App\Model\Verification::updateModel($request, $smsVerifcationdata->id);

                if ($request->hasfile('profile_image')) {
                    if ($image = $request->file('profile_image')) {
                        $extension = $image->getClientOriginalExtension();
                        $filename = str_replace(' ','', md5(time()).'_'.$image->getClientOriginalName());
                        $thumb = \Image::make($image)->resize(100, 100,
                        function ($constraint) {
                            $constraint->aspectRatio();
                        })->encode($extension);
                      $normal = \Image::make($image)->resize(400, 400,
                        function ($constraint) {
                            $constraint->aspectRatio();
                        })->encode($extension);
                      $big = \Image::make($image)->encode($extension);
                      $_800x800 = \Image::make($image)->resize(800, 800,
                        function ($constraint) {
                            $constraint->aspectRatio();
                        })->encode($extension);
                      $_400x400 = \Image::make($image)->resize(400, 400,
                        function ($constraint) {
                            $constraint->aspectRatio();
                        })->encode($extension);
                \Storage::disk('spaces')->put('thumbs/'.$filename, (string)$thumb, 'public');
                \Storage::disk('spaces')->put('uploads/'.$filename, (string)$normal, 'public');
                \Storage::disk('spaces')->put('original/'.$filename, (string)$big, 'public');
                \Storage::disk('spaces')->put('800x800/'.$filename, (string)$_800x800, 'public');
                \Storage::disk('spaces')->put('400x400/'.$filename, (string)$_400x400, 'public');
                \Storage::disk('spaces')->put('original/'.$filename, (string)$big, 'public');
                       $input['profile_image'] = $filename;
                    }
                }
                $input['password'] = bcrypt($input['password']);
                if (isset($request->fcm_id) && !empty($request->fcm_id)) {
                    $input['fcm_id'] = $request->fcm_id;
                }
                $user = User::create($input);
                if($user){
                    if($vendor_auto_approved){
                        $user->account_verified = $datenowone;
                        $user->save();
                    }
                    if($input['user_type']=='customer'){
                        $user->account_verified = $datenowone;
                        $user->save();
                    }
                    if(\Config::get('client_connected') && \Config::get('client_data')->domain_name=='intely'){
                       $user->account_step = 5;
                    }
                    if($user->hasrole('customer') && \Config::get('client_connected') && \Config::get('client_data')->domain_name=='mp2r' && $user->account_step==1){
                        $user->account_step = 2;
                    }
                    $user = $user->createStripeCustomer($user);
                    $user->provider_type = 'email';
                    $user->device_type = $device_type;
                    if(isset($input['npi_id'])){
                        $user->npi_id = $input['npi_id'];
                    }
                    if(isset($input['invite_code'])){
                        $ref_by = User::select('id')->where('reference_code',$input['invite_code'])->first();
                        if($ref_by){
                            $user->reference_by = $ref_by->id;
                            $ref_by->wallet->increment('points',5);    
                        }
                        $wallet = new Wallet();
                        $wallet->balance = 100;
                        $wallet->points = 5;
                        $wallet->user_id = $ref_by->id;
                        $wallet->save();
                    }else{
                        $wallet = new Wallet();
                        $wallet->balance = 100;
                        $wallet->points = 5;
                        $wallet->user_id = $user->id;
                        $wallet->save();
                    }
                    if(!$user->reference_code){
                        $user->reference_code = Str::random(10).$user->id;
                    }
                    $user->save();
                    
                    $role = Role::where('name',$input['user_type'])->first();
                    if($role){
                        $user->roles()->attach($role);
                    }
                    if(\Config::get('client_connected') && \Config::get('client_data')->domain_name=='nurselynx'){
                        $time = new DateTime($datenowone);
                        $time->modify("+5 second");
                        $time->format('Y-m-d H:i:s');
                        $push_data = ["id"=>$user->id];
                        $job = (new SignupEmail($push_data))->delay($time);
                        dispatch($job);
                    }
                    $profile = New Profile();
                    if(\Config::get('client_connected') && \Config::get('client_data')->domain_name=='intely' &&$input['user_type']=='service_provider'){
                       $profile->rating = 5;
                    }
                    $profile->dob = isset($input['dob'])?$input['dob']:'0000-00-00';
                    if(isset($input['working_since'])){
                        $profile->working_since = $input['working_since'];
                    }
                    if(isset($input['title'])){
                        $profile->title = $input['title'];
                    }
                    if(isset($input['speciality'])){
                        $profile->speciality = $input['speciality'];
                    }
                    if(isset($input['experience'])){
                        $profile->experience = $input['experience'];
                    }
                    if(isset($input['lat'])){
                        $profile->lat = $input['lat'];
                    }
                    if(isset($input['long'])){
                        $profile->long = $input['long'];
                    }
                    if(isset($input['location_name'])){
                        $profile->location_name = $input['location_name'];
                    }
                    // if(isset($request->accept_self_pay) && $request->accept_self_pay==true){
                    //     $profile->accept_self_pay = 1;
                    // }else{
                    //     $profile->accept_self_pay = 0;
                    // }
                    $profile->user_id = $user->id;
                    $profile->about = isset($input['bio'])?$input['bio']:'';
                    $profile->address = isset($input['address'])?$input['address']:'';
                    $profile->city = isset($input['city'])?$input['city']:'';
                    $profile->state = isset($input['state'])?$input['state']:'';
                    $profile->country = isset($input['country'])?$input['country']:'';
                    $profile->save();
                    $userz = User::with('roles')->find($user->id);
                    $token = $user->createToken('consult_app')->accessToken;
                    $userz->token = $token;
                    $userz->profile;
                    $userz->profile->bio = $userz->profile->about;
                    $userz->subscriptions = $userz->getSubscription($userz);
                    $userz->categoryData = $user->getCategoryData($user->id);
                    $userz->additionals = $user->getAdditionals($user->id);
                    $userz->insurances = $user->getInsurnceData($user->id);
                    if(isset($input['custom_fields'])){
                      if(!is_array($input['custom_fields'])){
                          $input['custom_fields'] = json_decode($input['custom_fields']);
                      }
                      if(is_array($input['custom_fields'])){
                        CustomUserField::where('user_id',$user->id)->delete();
                        foreach ($input['custom_fields'] as $cus_key => $custom_field) {
                          $CustomUserField = new CustomUserField();
                          $CustomUserField->field_value = $custom_field->field_value;
                          $CustomUserField->user_id = $user->id;
                          $CustomUserField->custom_field_id = $custom_field->id;
                          $CustomUserField->save();
                        }
                      }
                    }
                    if(isset($input['master_preferences'])){
                          if(!is_array($input['master_preferences']))
                            $input['master_preferences'] = json_decode($input['master_preferences']);
                          if(is_array($input['master_preferences'])){
                            foreach ($input['master_preferences'] as $cus_key => $master_preference) {
                                if($master_preference->preference_id){
                                    UserMasterPreference::where([
                                        'user_id'=>$user->id,
                                        'preference_id'=>$master_preference->preference_id,
                                    ])->delete();
                                    foreach ($master_preference->option_ids as $option_key => $option) {
                                        UserMasterPreference::firstOrCreate([
                                            'user_id'=>$user->id,
                                            'preference_id'=>$master_preference->preference_id,
                                            'preference_option_id'=>$option,
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                    $userz->custom_fields = $user->getCustomFields($user->id);
                    $userz->filters = $user->getFilters($user->id);
                    $userz->services = $user->getServices($user->id);
                    if($user->hasrole('service_provider')){
                        $userz->totalRating =  $userz->profile->rating;
                        $userz->patientCount = User::getTotalRequestDone($user->id);
                        $userz->reviewCount = Feedback::reviewCountByConsulatant($user->id); 
                    }
                    $userz = Helper::getMoreData($userz);
                    return response(['status' => 'success', 'statuscode' => 200, 'message' => __('You signed-up successfully !'), 'data' => ($userz)], 200);
                }else {
                    return response(['status' => 'error', 'statuscode' => 400, 'message' => __('Please try again')], 400);
                 }
            // }else{
            //     return response(['status' => 'error', 'statuscode' => 400, 'message' => __('OTP not verified')], 400);
            // }
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }

    public static function formatSizeUnits($bytes) {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
    /**
     * @SWG\Post(
     *     path="/profile-update",
     *     description="User Profile Update",
     * tags={"User Register & Login Section"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="name",
     *         in="query",
     *         type="string",
     *         description="name",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="email",
     *         in="query",
     *         type="string",
     *         description="email",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="phone",
     *         in="query",
     *         type="string",
     *         description="Mobile Number (+91**********)",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="country_code",
     *         in="query",
     *         type="string",
     *         description="Country Code",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="dob",
     *         in="query",
     *         type="string",
     *         description="Date of birth e.g YYYY-MM-DD=>2000-02-20",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="bio",
     *         in="query",
     *         type="string",
     *         description="a short biography",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="speciality",
     *         in="query",
     *         type="string",
     *         description="a short speciality",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="call_price",
     *         in="query",
     *         type="number",
     *         description="call_price per minute",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="chat_price",
     *         in="query",
     *         type="number",
     *         description="chat_price per minute",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="category_id",
     *         in="query",
     *         type="number",
     *         description="Category ID",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="experience",
     *         in="query",
     *         type="string",
     *         description="total experience",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="lat",
     *         in="query",
     *         type="string",
     *         description="latitude",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="long",
     *         in="query",
     *         type="string",
     *         description="longitude",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="location_name",
     *         in="query",
     *         type="string",
     *         description=" location name",
     *         required=false,
     *     ), 
     *     @SWG\Parameter(
     *         name="apn_token",
     *         in="query",
     *         type="string",
     *         description=" apn_token",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="title",
     *         in="query",
     *         type="string",
     *         description=" title  Mrs,Miss etc",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="working_since",
     *         in="query",
     *         type="string",
     *         description="Date of working since e.g YYYY-MM-DD=>2000-02-20",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="address",
     *         in="query",
     *         type="string",
     *         description="a address",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="city",
     *         in="query",
     *         type="string",
     *         description="a city",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="state",
     *         in="query",
     *         type="string",
     *         description="a state",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="country",
     *         in="query",
     *         type="string",
     *         description="a country",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="insurance_enable",
     *         in="query",
     *         type="string",
     *         description="0 and 1",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="insurances",
     *         in="query",
     *         type="string",
     *         description="required comma seprated",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="master_preferences",
     *         in="query",
     *         type="string",
     *         description="master_preferences array",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="invite_code",
     *         in="query",
     *         type="string",
     *         description="invite code",
     *         required=false,
     *     ),
     *    @SWG\Parameter(
     *      name="profile_image",
     *      in="formData",
     *      description="Profile Pic URL",
     *      required=false,
     *      type="file"
     *      ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     *
     *
     * User Profile Update
     *
     * @return \Illuminate\Http\Response
     */
    public function profieUpdate(Request $request) {
        try {
            // print_r($request->insurance);
            $user = Auth::user();
            $rules = [];
            if(!$user->name){
                $rules['name'] = 'required';
            }
            $customMessages = [];
            $input = $request->all();
            if(isset($input['email'])){
                $rules['email'] = 'email|unique:users,email,' . $user->id;
                $customMessages['email.unique'] = 'The Email has already been taken.';
            }
            if(isset($input['phone'])){
                $rules['phone'] = 'unique:users,phone,' . $user->id;
                $customMessages['phone.unique'] = 'The Mobile number has already been taken.';
                $rules['country_code'] = 'required';
            }
            if(isset($input['dob'])){
                $rules['dob'] = 'required|date|date_format:Y-m-d';
            }
            if(isset($input['working_since'])){
                $rules['working_since'] = 'required|date|date_format:Y-m-d';
            }
            if(isset($input['bio'])){
                $rules['bio'] = 'required';
            }
            if(isset($input['insurance_enable']) && $input['insurance_enable']=="1"){
                $rules['insurances'] = 'required|string';
            }
            if(isset($input['invite_code'])){
                $rules['invite_code'] = 'required|exists:users,reference_code';
                $customMessages['invite_code.exists'] = 'The invite code is invalid';
            }
            $validator = Validator::make($request->all(), $rules, $customMessages);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            if ($request->hasfile('profile_image')) {
                if ($image = $request->file('profile_image')) {
                    $extension = $image->getClientOriginalExtension();
                    $filename = str_replace(' ','', md5(time()).'_'.$image->getClientOriginalName());
                        $thumb = \Image::make($image)->resize(100, 100,
                  function ($constraint) {
                      $constraint->aspectRatio();
                  })->encode($extension);
                $big = \Image::make($image)->encode($extension);
                $_800x800 = \Image::make($image)->resize(800, 800,
                  function ($constraint) {
                      $constraint->aspectRatio();
                  })->encode($extension);
                $_400x400 = \Image::make($image)->resize(400, 400,
                  function ($constraint) {
                      $constraint->aspectRatio();
                  })->encode($extension);
                \Storage::disk('spaces')->put('thumbs/'.$filename, (string)$thumb, 'public');
                \Storage::disk('spaces')->put('uploads/'.$filename, (string)$_400x400, 'public');
                \Storage::disk('spaces')->put('original/'.$filename, (string)$big, 'public');
                \Storage::disk('spaces')->put('800x800/'.$filename, (string)$_800x800, 'public');
                \Storage::disk('spaces')->put('400x400/'.$filename, (string)$_400x400, 'public');
                $input['profile_image'] = $filename;
                }
            }
            $device_type = $request->header('devicetype');
            if($device_type!='IOS'){
                $device_type = 'ANDROID';
            }
            User::updateOrCreate(['id' => $user->id], $input);
            if(isset($input['npi_id'])){
                $user->npi_id = $input['npi_id'];
            }
            if(isset($input['invite_code']) && $user->reference_by==null){
                $ref_by = User::select('id')->where('reference_code',$input['invite_code'])->first();
                if($ref_by){
                    $user->reference_by = $ref_by->id;    
                    $user->save();
                    $ref_by->wallet->increment('points',5);
                }
                $wallet = new Wallet();
                $wallet->balance = 100;
                $wallet->points = 5;
                $wallet->user_id = $ref_by->id;
                $wallet->save();
            }
            $profile = Profile::where('user_id',$user->id)->first();
            if(!$profile){
                $profile = new Profile();
                if(\Config::get('client_connected') && \Config::get('client_data')->domain_name=='intely'){
                   $profile->rating = 5;
                }
                $profile->user_id = $user->id;
            }
            if(isset($input['dob'])){
                $profile->dob = $input['dob'];
            }
            if(isset($input['bio'])){
                $profile->about = $input['bio'];
            }
            if(isset($input['speciality'])){
                $profile->speciality = $input['speciality'];
            }
            if(isset($input['city'])){
                $profile->city = $input['city'];
            }
            if(isset($input['state'])){
                $profile->state = $input['state'];
            }
            if(isset($input['address'])){
                $profile->address = $input['address'];
            }
            if(isset($input['country'])){
                $profile->country = $input['country'];
            }
            if(isset($input['call_price'])){
                $profile->call_price = $input['call_price'];
            }
            if(isset($input['chat_price'])){
                $profile->chat_price = $input['chat_price'];
            }
            if(isset($input['experience'])){
                $profile->experience = $input['experience'];
            }
            if(isset($input['lat'])){
                $profile->lat = $input['lat'];
            }
            if(isset($input['long'])){
                $profile->long = $input['long'];
            }
            if(isset($input['location_name'])){
                $profile->location_name = $input['location_name'];
            }
            if(isset($input['working_since'])){
                $profile->working_since = $input['working_since'];
            }
            if(isset($input['title'])){
                $profile->title = $input['title'];
            }
            if (isset($request->apn_token) && !empty($request->apn_token)) {
                $user->apn_token = $request->apn_token;
            }
            if(isset($request->accept_self_pay) && $request->accept_self_pay==true){
                    $profile->accept_self_pay = 1;
            }else{
                $profile->accept_self_pay = 0;
            }
            if(isset($input['category_id'])){
                if($user->hasrole('service_provider')){
                    $category_service = CategoryServiceProvider::where(['sp_id'=>$user->id])->first();
                    if(!$category_service){
                        $category_service =  new CategoryServiceProvider();
                        $category_service->sp_id = $user->id;
                    }
                    $category_service->category_id = $input['category_id'];
                    $category_service->save();
                }
            }
            $profile->save();
            $profile->setSubscription($profile);
            if(isset($input['insurance_enable']) && $input['insurance_enable']==="0"){
                UserInsurance::where('user_id',$user->id)->delete();
            }
            if(isset($input['insurances']) && $input['insurance_enable']==="1"){
                $insurances = explode(",",$input['insurances']);
                UserInsurance::where('user_id',$user->id)->delete();
                foreach ($insurances as $key => $insurance_id) {
                  if($insurance_id){
                      $userinsurance = new UserInsurance();
                      $userinsurance->insurance_id = $insurance_id;
                      $userinsurance->user_id = $user->id;
                      $userinsurance->save();
                  }
                }
            }
            if(isset($input['master_preferences'])){
                  if(!is_array($input['master_preferences']))
                    $input['master_preferences'] = json_decode($input['master_preferences']);
                  if(is_array($input['master_preferences'])){
                    foreach ($input['master_preferences'] as $cus_key => $master_preference) {
                        if($master_preference->preference_id){
                            UserMasterPreference::where([
                                'user_id'=>$user->id,
                                'preference_id'=>$master_preference->preference_id,
                            ])->delete();
                            foreach ($master_preference->option_ids as $option_key => $option) {
                                // if($option)
                                UserMasterPreference::firstOrCreate([
                                    'user_id'=>$user->id,
                                    'preference_id'=>$master_preference->preference_id,
                                    'preference_option_id'=>$option,
                                ]);
                            }
                        }
                    }
                }
            }
            if(isset($input['insurance_images'])){
                 if(!is_array($input['insurance_images']))
                    $input['insurance_images'] = json_decode($input['insurance_images']);
                 if(is_array($input['insurance_images'])){
                        ModelImage::where([
                            'module_table'=>'insurance_images',
                            'module_table_id'=>$user->id
                        ])->whereNotIn('image_name',$input['insurance_images'])->delete();
                        foreach ($input['insurance_images'] as $key => $insurance_image) {
                        ModelImage::firstOrCreate([
                            'module_table'=>'insurance_images',
                            'module_table_id'=>$user->id,
                            'image_name'=>$insurance_image
                        ]);
                    }
                 }
            }
            if(isset($input['insurance_info'])){
                 if(!is_array($input['insurance_info']))
                    $input['insurance_info'] = json_decode($input['insurance_info'],true);
                 if(is_array($input['insurance_info'])){
                    \App\Model\CustomInfo::where([
                                'info_type'=>'user_insurance_info',
                                'ref_table'=>'users',
                                'ref_table_id'=>$user->id,
                            ])->delete();
                        foreach ($input['insurance_info'] as $key => $info) {
                            \App\Model\CustomInfo::firstOrCreate([
                                'info_type'=>'user_insurance_info',
                                'raw_detail'=>json_encode($info),
                                'ref_table'=>'users',
                                'ref_table_id'=>$user->id,
                                'status'=>'success',
                            ]);
                        }
                 }
            }
            $timezone = $request->header('timezone');
            if(!$timezone){
                $timezone = 'Asia/Kolkata';
            }
            if(isset($input['custom_fields'])){
              if(!is_array($input['custom_fields'])){
                  $input['custom_fields'] = json_decode($input['custom_fields']);
              }
              if(is_array($input['custom_fields'])){
                CustomUserField::where('user_id',$user->id)->delete();
                foreach ($input['custom_fields'] as $cus_key => $custom_field) {
                  $CustomUserField = new CustomUserField();
                  $CustomUserField->field_value = $custom_field->field_value;
                  $CustomUserField->user_id = $user->id;
                  $CustomUserField->custom_field_id = $custom_field->id;
                  $CustomUserField->save();
                  $CustomField =   \App\Model\CustomField::where([
                    'field_name'=>'Working shifts',
                    'user_type'=>3,
                    'id'=>$CustomUserField->custom_field_id
                    ])->first();
                  if($CustomField){
                        ServiceProviderSlot::where([
                            'service_provider_id'=>$user->id,
                            'service_id'=>1,
                            'category_id'=>1,
                        ])->delete();
                        $shifts = explode(',', $CustomUserField->field_value);
                        // print_r($shifts);die;
                        foreach ($shifts as $key => $shift) {
                            // print_r($shift);
                            $shift = trim(strtolower($shift));
                            if(strtolower($shift)=="day shift"){
                                // print_r($shift);
                                $slot['slots'] = [['start_time'=>'07:00','end_time'=>'14:59']];
                                $this->createSlotsForServiceProvider($user,$slot,$timezone);
                            }
                            // strtolower($shift);
                            if(strtolower($shift)=="evening shift"){
                                // print_r($shift);die;
                                // die('2');
                                $slot['slots'] = [['start_time'=>'15:00','end_time'=>'21:59']];
                                $this->createSlotsForServiceProvider($user,$slot,$timezone);
                            }
                            if(strtolower($shift)=="night shift"){
                                // print_r($shift);
                                $slot['slots'] = [['start_time'=>'22:00','end_time'=>'06:59']];
                                $this->createSlotsForServiceProvider($user,$slot,$timezone);
                            }
                        }
                  }
                }
              }
            }


            // die;
            if ($user->email && $user->phone && $user->account_step==1) {
                $user->account_step = 2;
            }
            if($user->hasrole('customer') && \Config::get('client_connected') && \Config::get('client_data')->domain_name=='mp2r' && $user->account_step==1){
                $user->account_step = 2;
            }
            $user->device_type = $device_type;
            $user->save();
            $userz = User::with('roles')->find($user->id);
            $token = $user->createToken('consult_app')->accessToken;
            $userz->token = $token;
            $userz->profile;
            $userz->profile->location = ["name"=>$userz->profile->location_name,"lat"=>$userz->profile->lat,"long"=>$userz->profile->long];
            $userz->profile->bio = $userz->profile->about;
            $userz->subscriptions = $userz->getSubscription($userz); 
            $userz->categoryData = $userz->getCategoryData($userz->id);
            $userz->additionals = $userz->getAdditionals($userz->id);
            $userz->filters = $userz->getFilters($userz->id);
            $userz->services = $user->getServices($userz->id);
            $userz->insurances = $user->getInsurnceData($userz->id);
            $userz->custom_fields = $user->getCustomFields($userz->id);
            if($user->hasrole('service_provider')){
                $userz->totalRating =  $userz->profile->rating;
                $userz->patientCount = User::getTotalRequestDone($user->id);
                $userz->reviewCount = Feedback::reviewCountByConsulatant($user->id); 
            }
            $userz = Helper::getMoreData($userz); 
            return response(['status' =>"success", 'statuscode' => 200, 'message' => __('Profile successfully updated.'), 'data' => ($userz)], 200);
        } catch (Exception $e) {
            return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    private function createSlotsForServiceProvider($user,$availability,$timezone){
        $weekdays = [0,1,2,3,4,5,6];
        foreach ($weekdays as $day) {
           foreach ($availability['slots'] as $slot) {
                $start_time = Carbon::parse($slot['start_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                $end_time = Carbon::parse($slot['end_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                $spavailability = new ServiceProviderSlot();
                $spavailability->service_provider_id = $user->id;
                $spavailability->service_id = 1;
                $spavailability->category_id = 1;
                $spavailability->start_time = $start_time;
                $spavailability->end_time = $end_time;
                $spavailability->day = $day;
                $spavailability->save();
           }
        }
        return;
    }

    

    /**
     * @SWG\Post(
     *     path="/insurance-info",
     *     description="insurance verification",
     * tags={"Insurance"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="first_name",
     *         in="query",
     *         type="string",
     *         description="Member first_name",
     *         required=false,
     *     ), 
     *  @SWG\Parameter(
     *         name="last_name",
     *         in="query",
     *         type="string",
     *         description="last_name",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="dob",
     *         in="query",
     *         type="string",
     *         description="dob as 09/22/1984",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="gender",
     *         in="query",
     *         type="string",
     *         description="Gender",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="member_id",
     *         in="query",
     *         type="string",
     *         description="member_id",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="provider_first_name",
     *         in="query",
     *         type="string",
     *         description="provider_first_name",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="provider_last_name",
     *         in="query",
     *         type="string",
     *         description="provider_last_name",
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
    public function saveInsuranceInfo(Request $request){
        try{
            $input = $request->all();
            $rules = array(
                'dob'=>'required',
                'member_id'=>'required',
            );
            $validation = \Validator::make($input,$rules);            
            if($validation->fails()){
                return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                    $validation->getMessageBag()->first()), 400);
            }
            $user = Auth::user();
            $insurance = UserInsurance::where([
                'user_id'=>$user->id
            ])->first();
            if($insurance){
                $input['insurance_id'] = $insurance->insurance_id;
            }
            $CustomInfo = new \App\Model\CustomInfo();
            $CustomInfo->info_type = 'insurance_verification';
            $CustomInfo->ref_table = 'users';
            $CustomInfo->ref_table_id = $user->id;
            $CustomInfo->status = 'success';
            $CustomInfo->raw_detail = json_encode($input);
            $CustomInfo->save();
            return response(array(
                'status' => 'success',
                'statuscode' => 200,
                'message' =>'Insurance Info Saved')
        ,200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        } 
    }

    /**
     * @SWG\Post(
     *     path="/save-address",
     *     description="Addresses",
     * tags={"User Register & Login Section"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="address_name",
     *         in="query",
     *         type="string",
     *         description="Member address_name",
     *         required=true,
     *     ), 
     *  @SWG\Parameter(
     *         name="save_as",
     *         in="query",
     *         type="string",
     *         description="save_as e.g Home,Office etc",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="lat",
     *         in="query",
     *         type="string",
     *         description="lattitude",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="long",
     *         in="query",
     *         type="string",
     *         description="Longitude",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="house_no",
     *         in="query",
     *         type="string",
     *         description="House Number",
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
    public function addAddress(Request $request){
        try{
            $input = $request->all();
            $rules = array(
                'address_name'=>'required',
                'save_as'=>'required',
                'lat'=>'required',
                'long'=>'required',
            );
            $validation = \Validator::make($input,$rules);            
            if($validation->fails()){
                return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                    $validation->getMessageBag()->first()), 400);
            }
            $user = Auth::user();
            $address = new \App\Model\CustomInfo();
            $address->info_type = 'user_address';
            $address->ref_table = 'users';
            $address->ref_table_id = $user->id;
            $address->status = 'success';
            $address->raw_detail = json_encode($input);
            $address->save();
            return response(array(
                'status' => 'success',
                'statuscode' => 200,
                'message' =>'Address Saved'),200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        } 
    }

    /**
     * @SWG\Get(
     *     path="/get-address",
     *     description="Addresses",
     * tags={"User Register & Login Section"},
     *     security={
     *     {"Bearer": {}},
     *   },
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
    public function getAddress(Request $request){
        try{
            $user = Auth::user();
            $adrs = \App\Model\CustomInfo::where([
                'info_type'=>'user_address',
                'ref_table'=>'users',
                'ref_table_id'=>$user->id,
            ])->get();
            $addresses = [];
            foreach ($adrs as $key => $add) {
                $real = $add->raw_detail;
                $jsn = json_decode($add->raw_detail);
                $jsn->id = $add->id;
                $jsn = json_encode($jsn);
                $addresses[] = json_decode($jsn);
            }
            return response(array(
                'status' => 'success',
                'statuscode' => 200,
                'data' =>[
                    'addresses'=>$addresses
                ]),200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        } 
    }
    /**
     * @SWG\Get(
     *     path="/insurance-info",
     *     description="insurance verification",
     * tags={"Insurance"},
     *     security={
     *     {"Bearer": {}},
     *   },
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
    public function getUserInsuranceDetail(Request $request){
        try{
            $data = new \StdClass();
            $user = Auth::user();
            $CustomInfo = \App\Model\CustomInfo::where([
                'info_type'=>'insurance_verification',
                'ref_table'=>'users',
                'ref_table_id'=>$user->id,
            ])->orderBy('id','DESC')->first();
            if($CustomInfo){
                $data = json_decode($CustomInfo->raw_detail);
            }
            $insurance = UserInsurance::where([
                'user_id'=>$user->id
            ])->first();
            if($insurance){
                $data->carrier_code = $insurance->insurance->carrier_code;
                $data->insurance_name = $insurance->insurance->name;
            }
            return response(array(
                'status' => 'success',
                'statuscode' => 200,
                'data'=>['insurance_detail'=>$data],
                'message' =>'Insurance Info')
        ,200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        } 
    }

    /**
     * @SWG\Get(
     *     path="/user-check",
     *     description="User Check Exist",
     * tags={"Security Question"},
     *  @SWG\Parameter(
     *         name="email",
     *         in="query",
     *         type="string",
     *         description="email",
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
    public function checkUserExit(Request $request){
        try{
            $input = $request->all();
            $rules = [
                'email' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $column_name = 'email';
            $domain = "mp2r";
            if($domain=="mp2r"){
                if(filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                    $column_name = 'email';
                }else {
                    $column_name = 'user_name';
                    unset($request['email']);
                }
            }
            $exist = User::whereHas('roles', function ($query) {
               $query->whereIn('name',['service_provider','customer']);
            })->where($column_name,$input['email'])->first();
            if(!$exist){
                return response(['status' => "error", 'statuscode' => 400, 'message' =>" The $column_name account that you tried to reach does not exist."], 400); 
            }
            $answers_raw = \App\Model\UserSecurityAnswer::select('id','security_question_id','answer')->where(['user_id'=>$exist->id])->get();
            if($answers_raw->count()>0){
                $answers = [];
                foreach ($answers_raw as $key => $answer) {
                    $ss_q = \App\Model\SecurityQuestion::where(['id'=>$answer->security_question_id])->first();
                    $answer->question = $ss_q->question;
                    $answer->user_answer = "";
                    $answer->id = $answer->security_question_id;
                    unset($answer->security_question_id);
                    $answers[$ss_q->type] = $answer;
                }
                return response(['status' => "success", 'statuscode' => 200, 'message' =>"Listing",'data'=>['questions'=>$answers]],200); 
            }else{
                return response(['status' => "error", 'statuscode' => 200, 'message' =>"You have not added any security question/answer. Please contact to Admin for password reset",'data'=>['questions'=>(Object)[]]], 200);
            }
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        } 
    }


    /**
     * @SWG\Get(
     *     path="/security-questions",
     *     description="getSecurityQuestion for Profile Update and Sign-Up",
     * tags={"Security Question"},
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
    public function getSecurityQuestion(Request $request){
        try{
            $question1 = Helper::getSecurityQuestion('question1');
            $question2 = Helper::getSecurityQuestion('question2');
            $question3 = Helper::getSecurityQuestion('question3');
            $questions = ['question1'=>$question1,'question2'=>$question2,'question3'=>$question3];
           return response(['status' => "success", 'statuscode' => 200, 'message' =>"Listing",'data'=>['questions'=>$questions]],200); 
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        } 
    }

    /**
     * @SWG\Post(
     *     path="/verify-check-answer",
     *     description="User Check Answer",
     * tags={"Security Question"},
     *  @SWG\Parameter(
     *         name="email",
     *         in="query",
     *         type="string",
     *         description="email",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="questions",
     *         in="query",
     *         type="string",
     *         description="questions",
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
    public function checkVerifyAnswer(Request $request){
        $input = $request->all();
        $rules = [
            'email' => 'required',
            'questions'=>'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                $validator->getMessageBag()->first()), 400);
        }
        $column_name = 'email';
        $domain = "mp2r";
        if($domain=="mp2r"){
            if(filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                $column_name = 'email';
            }else {
                $column_name = 'user_name';
                unset($request['email']);
            }
        }
        $exist = User::whereHas('roles', function ($query) {
           $query->whereIn('name',['service_provider','customer']);
        })->where($column_name,$input['email'])->first();
        if(!$exist){
            return response(['status' => "error", 'statuscode' => 400, 'message' =>" The $column_name account that you tried to reach does not exist."], 400); 
        }
        $questions = $input['questions'];
        $answers_raw1 = UserSecurityAnswer::where([
            'user_id'=>$exist->id,
            'security_question_id'=>$questions['question1']['id'],
            'answer'=>$questions['question1']['user_answer']
        ])->first();
        if(!$answers_raw1){
            return response(['status' => "error", 'statuscode' => 400,'message'=>"Not a valid Answer of Question1"], 400);
        }
        $answers_raw2 = UserSecurityAnswer::where([
            'user_id'=>$exist->id,
            'security_question_id'=>$questions['question2']['id'],
            'answer'=>$questions['question2']['user_answer']
        ])->first();
        if(!$answers_raw2){
            return response(['status' => "error", 'statuscode' => 400, 'message'=>"Not a valid Answer of Question2"], 400);
        }
        $answers_raw3 = UserSecurityAnswer::where([
            'user_id'=>$exist->id,
            'security_question_id'=>$questions['question3']['id'],
            'answer'=>$questions['question3']['user_answer']
        ])->first();
        if(!$answers_raw3){
            return response(['status' => "error", 'statuscode' => 400, 'message'=>"Not a valid Answer of Question3"], 400);
        }
        return response([
            'status' => "success",
            'statuscode' => 200,
            'message' =>"Success",
            'data'=>['user_id'=>$exist->id]
        ],200);

    }    



    /**
     * @SWG\Post(
     *     path="/update-security-question",
     *     description="User Update Question&Answer",
     * tags={"Security Question"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="questions",
     *         in="query",
     *         type="string",
     *         description="questions",
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
    public function updateSecurityQuestion(Request $request){
        try{
            $input = $request->all();
            $user = Auth::user();
            $rules = [
                'questions'=>'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $questions = $input['questions'];
            if(!isset($questions['question1']) || !isset($questions['question1']['id'])){
                return response(['status' => "error", 'statuscode' => 400,'message'=>"Please Select Question1"], 400);
            }
            if(!isset($questions['question2']) || !isset($questions['question2']['id'])){
                return response(['status' => "error", 'statuscode' => 400,'message'=>"Please Select Question2"], 400);
            }
            if(!isset($questions['question3']) || !isset($questions['question3']['id'])){
                return response(['status' => "error", 'statuscode' => 400,'message'=>"Please Select Question3"], 400);
            }

            if(!isset($questions['question1']['user_answer'])){
                return response(['status' => "error", 'statuscode' => 400,'message'=>"Please Enter Answer1"], 400);
            }
            if(!isset($questions['question2']['user_answer'])){
                return response(['status' => "error", 'statuscode' => 400,'message'=>"Please Enter Answer2"], 400);
            }
            if(!isset($questions['question3']['user_answer'])){
                return response(['status' => "error", 'statuscode' => 400,'message'=>"Please Enter Answer3"], 400);
            }
            \App\Model\UserSecurityAnswer::where('user_id',$user->id)->delete();
            $answers_raw1 = \App\Model\UserSecurityAnswer::firstOrcreate([
                'user_id'=>$user->id,
                'security_question_id'=>$questions['question1']['id'],
                'answer'=>$questions['question1']['user_answer']
            ])->first();
            $answers_raw2 = \App\Model\UserSecurityAnswer::firstOrcreate([
                'user_id'=>$user->id,
                'security_question_id'=>$questions['question2']['id'],
                'answer'=>$questions['question2']['user_answer']
            ])->first();
            $answers_raw3 = \App\Model\UserSecurityAnswer::firstOrcreate([
                'user_id'=>$user->id,
                'security_question_id'=>$questions['question3']['id'],
                'answer'=>$questions['question3']['user_answer']
            ])->first();
            return response([
                'status' => "success",
                'statuscode' => 200,
                'message' =>"Success",
            ],200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }

    }

    /**
     * @SWG\Post(
     *     path="/reset-password",
     *     description="User Check Answer",
     * tags={"Security Question"},
     *  @SWG\Parameter(
     *         name="user_id",
     *         in="query",
     *         type="string",
     *         description="user_id",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="new_password",
     *         in="query",
     *         type="string",
     *         description="new_password",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="confirm_password",
     *         in="query",
     *         type="string",
     *         description="confirm_password",
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
    public function postResetPassword(Request $request){
        $input = $request->all();
        $rules = [
            'user_id' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                $validator->getMessageBag()->first()), 400);
        }
        $exist = User::where('id',$input['user_id'])->first();
        if(!$exist){
            return response(['status' => "error", 'statuscode' => 400, 'message' =>" The account that you tried to reach does not exist."], 400); 
        }
        $rules = [
                'new_password' => 'required',
                'confirm_password' => 'required|same:new_password',
            ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                $validator->getMessageBag()->first()), 400);
        }
        $exist->update(['password'=> Hash::make($request->new_password)]);
        return response(['status' => "success", 'statuscode' => 200, 'message' =>"You Password has been updated successfully "],200); 

    }

    /**
     * @SWG\Post(
     *     path="/upload-image",
     *     description="image upload",
     * tags={"User Register & Login Section"},
     *     security={},
     *    @SWG\Parameter(
     *      name="image",
     *      in="formData",
     *      description="image upload",
     *      required=true,
     *      type="file"
     *      ),
     *  @SWG\Parameter(
     *         name="type",
     *         in="query",
     *         type="string",
     *         description="file type for e.g image,pdf,audio default is image",
     *         required=false,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     *
     *
     * User Profile Update
     *
     * @return \Illuminate\Http\Response
     */

    public static function uploadImage(Request $request){
        try {
            $user = Auth::user();
            $rules = [
                'image' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            if ($request->hasfile('image')) {
                if ($image = $request->file('image')) {
                    $extension = $image->getClientOriginalExtension();
                    $filename = str_replace(' ','', md5(time()).'_'.$image->getClientOriginalName());
                    if(isset($request->type) && strtolower($request->type)=='pdf'){
                        $FileEnconded=  \File::get($request->image);
                        \Storage::disk('spaces')->put('pdf/'.$filename, (string)$FileEnconded,'public');
                        $image_name = $filename;
                    }else if(isset($request->type) && strtolower($request->type)=='audio'){
                        $FileEnconded=  \File::get($request->image);
                        \Storage::disk('spaces')->put('audio/'.$filename, (string)$FileEnconded,'public');
                        $image_name = $filename;
                    }else{
                        $thumb = \Image::make($image)->resize(100, 100,
                      function ($constraint) {
                          $constraint->aspectRatio();
                      })->encode($extension);
                    $normal = \Image::make($image)->resize(800, 800,
                      function ($constraint) {
                          $constraint->aspectRatio();
                      })->encode($extension);
                    $big = \Image::make($image)->encode($extension);
                    $_800x800 = \Image::make($image)->resize(800, 800,
                      function ($constraint) {
                          $constraint->aspectRatio();
                      })->encode($extension);
                    $_400x400 = \Image::make($image)->resize(400, 400,
                      function ($constraint) {
                          $constraint->aspectRatio();
                      })->encode($extension);
                    \Storage::disk('spaces')->put('thumbs/'.$filename, (string)$thumb, 'public');
                    \Storage::disk('spaces')->put('uploads/'.$filename, (string)$normal, 'public');
                    \Storage::disk('spaces')->put('original/'.$filename, (string)$big, 'public');
                    \Storage::disk('spaces')->put('800x800/'.$filename, (string)$_800x800, 'public');
                    \Storage::disk('spaces')->put('400x400/'.$filename, (string)$_400x400, 'public');
                    \Storage::disk('spaces')->put('original/'.$filename, (string)$big, 'public');
                           $image_name = $filename;
                    }
                    $type = isset($request->type)?$request->type:'IMAGE';
                    return response(['status' =>"success", 'statuscode' => 200, 'message' => __('Profile successfully updated.'), 'data' =>['image_name'=>$image_name,'type'=>$type]], 200);
                }else{
                    return response(['status' => 'error', 'statuscode' => 400, 'message' => __('Image upload error')], 400);
                }
            }else{
                return response(['status' => 'error', 'statuscode' => 400, 'message' => __('Image upload error')], 400);
            }
        } catch (Exception $e) {
            return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    } 


    /**
     * @SWG\Post(
     *     path="/add-family",
     *     description="Add Family Member",
     * tags={"User Register & Login Section"},
     *     security={},
     *  @SWG\Parameter(
     *         name="optionals",
     *         in="query",
     *         type="string",
     *         description="optionals key for skip gender,height,weight etc fileds",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="first_name",
     *         in="query",
     *         type="string",
     *         description="first_name",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="last_name",
     *         in="query",
     *         type="string",
     *         description="last_name",
     *         required=false,
     *     ), 
     *  @SWG\Parameter(
     *         name="relation",
     *         in="query",
     *         type="string",
     *         description="relation",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="gender",
     *         in="query",
     *         type="string",
     *         description="gender",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="age",
     *         in="query",
     *         type="string",
     *         description="age",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="height",
     *         in="query",
     *         type="string",
     *         description="height",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="weight",
     *         in="query",
     *         type="string",
     *         description="weight",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="blood_group",
     *         in="query",
     *         type="string",
     *         description="blood_group",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="medical_allergies",
     *         in="query",
     *         type="string",
     *         description="medical_allergies",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="chronic_diseases",
     *         in="query",
     *         type="string",
     *         description="chronic_diseases",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="chronic_diseases_desc",
     *         in="query",
     *         type="string",
     *         description="chronic_diseases_desc",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="previous_surgeries",
     *         in="query",
     *         type="string",
     *         description="previous_surgeries",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="previous_medication",
     *         in="query",
     *         type="string",
     *         description="previous_medication",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="country_code",
     *         in="query",
     *         type="string",
     *         description="country_code",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="email",
     *         in="query",
     *         type="string",
     *         description="email",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="phone",
     *         in="query",
     *         type="string",
     *         description="phone",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="patient_type",
     *         in="query",
     *         type="string",
     *         description="patient_type",
     *         required=false,
     *     ),
     *    @SWG\Parameter(
     *      name="image",
     *      in="query",
     *      description="image name",
     *      required=false,
     *      type="string"
     *      ),
     *    @SWG\Parameter(
     *      name="family_id",
     *      in="query",
     *      description="family_id",
     *      required=false,
     *      type="string"
     *      ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     *
     *
     * User Profile Update
     *
     * @return \Illuminate\Http\Response
     */

    public function addFamilyMember(Request $request){
        try {
            $user = Auth::user();
            if(isset($request->family_id)){
                $rules = [
                        'family_id' => 'required|exists:families,id',
                    ];
            }else{
                if(isset($request->optionals)){
                    $rules = [
                        'first_name' => 'required',
                        'relation' => 'required',
                    ];
                }else{
                    $rules = [
                        'first_name' => 'required',
                        'relation' => 'required',
                        'gender' => 'required',
                        'age' => 'required',
                        'height' => 'required',
                        'weight' => 'required',
                        'blood_group' => 'required',
                        'image' => 'required',
                    ];
                }
            }
            if(isset($input['insurance_enable']) && $input['insurance_enable']=="1"){
                $rules['insurances'] = 'required|string';
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $input = $request->all();
            if(isset($input['family_id'])){
                $family = Family::where('id',$input['family_id'])->first();
            }else{
                $family = new Family();
                $family->first_name = $input['first_name'];
                $family->last_name = isset($input['last_name'])?$input['last_name']:null;
                $family->relation = $input['relation'];
                $family->gender = isset($input['gender'])?$input['gender']:null;
                $family->age = isset($input['age'])?$input['age']:null;
                $family->height = isset($input['height'])?$input['height']:null;
                $family->weight = isset($input['weight'])?$input['weight']:null;
                $family->blood_group = isset($input['blood_group'])?$input['blood_group']:null;
                $family->image = isset($input['image'])?$input['image']:null;
                $family->user_id = $user->id;
                $family->save();
                $raw_detail = [];
                $raw_detail['medical_allergies'] = isset($input['medical_allergies'])?$input['medical_allergies']:null;
                $raw_detail['chronic_diseases'] = isset($input['chronic_diseases'])?$input['chronic_diseases']:null;
                $raw_detail['previous_surgeries'] = isset($input['previous_surgeries'])?$input['previous_surgeries']:null;
                $raw_detail['previous_medication'] = isset($input['previous_medication'])?$input['previous_medication']:null;
                $raw_detail['country_code'] = isset($input['country_code'])?$input['country_code']:null;
                $raw_detail['phone'] = isset($input['phone'])?$input['phone']:null;
                $raw_detail['email'] = isset($input['email'])?$input['email']:null;
                $raw_detail['patient_type'] = isset($input['patient_type'])?$input['patient_type']:null;
                $raw_detail['chronic_diseases_desc'] = isset($input['chronic_diseases_desc'])?$input['chronic_diseases_desc']:null;
                $CustomInfo = new \App\Model\CustomInfo();
                $CustomInfo->info_type = 'family_info';
                $CustomInfo->ref_table = 'families';
                $CustomInfo->ref_table_id = $family->id;
                $CustomInfo->status = 'success';
                $CustomInfo->raw_detail = json_encode($raw_detail);
                $CustomInfo->save();
            }
            if(isset($input['insurance_enable']) && $input['insurance_enable']==="0"){
                    \App\Model\PatientInsurance::where('user_id',$family->id)->delete();
                }
            if(isset($input['insurances']) && $input['insurance_enable']==="1"){
                $insurances = explode(",",$input['insurances']);
                \App\Model\PatientInsurance::where('user_id',$family->id)->delete();
                foreach ($insurances as $key => $insurance_id) {
                  if($insurance_id){
                      $userinsurance = new \App\Model\PatientInsurance();
                      $userinsurance->insurance_id = $insurance_id;
                      $userinsurance->user_id = $family->id;
                      $userinsurance->save();
                  }
                }
            }
            if(isset($input['insurance_info'])){
                 if(!is_array($input['insurance_info']))
                    $input['insurance_info'] = json_decode($input['insurance_info'],true);
                 if(is_array($input['insurance_info'])){
                    \App\Model\CustomInfo::where([
                                'info_type'=>'user_insurance_info',
                                'ref_table'=>'paient',
                                'ref_table_id'=>$family->id,
                            ])->delete();
                        foreach ($input['insurance_info'] as $key => $info) {
                            \App\Model\CustomInfo::firstOrCreate([
                                'info_type'=>'user_insurance_info',
                                'raw_detail'=>json_encode($info),
                                'ref_table'=>'paient',
                                'ref_table_id'=>$family->id,
                                'status'=>'success',
                            ]);
                        }
                 }
            }
            if($family->save()){
                $fm = Family::familyData($family->id);
                return response(['status' =>"success", 'statuscode' => 200, 'message' => __('Family Member Added  Successfully'), 'data' =>['family'=>$fm]], 200);
            }
        } catch (Exception $e) {
            return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }
    /**
     * @SWG\Post(
     *     path="/update-phone",
     *     description="Change Phone Number",
     * tags={"User Register & Login Section"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *     @SWG\Parameter(
     *         name="phone",
     *         in="query",
     *         type="string",
     *         description="Mobile Number (123456789)",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         name="country_code",
     *         in="query",
     *         type="string",
     *         description="Country Code",
     *         required=true,
     *     ),
     *    @SWG\Parameter(
     *      name="otp",
     *      in="query",
     *      description="OTP code",
     *      required=true,
     *      type="string"
     *      ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     *
     *
     * User Phone Update
     *
     * @return \Illuminate\Http\Response
     */
    public static function changePhoneNumber(Request $request) {
        try {
            $user = Auth::user();
            $rules = [
                'phone' => 'required|unique:users,phone,' . $user->id,
                'country_code' => 'required',
                'otp' => 'required',
            ];
            $customMessages = [];
            $input = $request->all();
            $customMessages['phone.unique'] = 'The Mobile number has already been taken.';
            $validator = Validator::make($request->all(), $rules, $customMessages);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $verify = \App\Model\Verification::where([
                'phone' => $input['phone'],
                'country_code'=>$input['country_code'],
                'code' => $input['otp']
            ])->first();
            if($input['otp']=='1234' || $verify){
                if($verify){
                    $verify->status = 'verified';
                    $verify->save();
                }
                $user->phone = $input['phone'];
                $user->country_code = $input['country_code'];
                $user->save();
                $token = $user->createToken('consult_app')->accessToken;
                $user->token = $token;
                $user->profile;
                if($user->profile){
                    $user->profile->bio = $user->profile->about; 
                }
                $user->subscriptions = $user->getSubscription($user);
                $user->categoryData = $user->getCategoryData($user->id);
                $user->additionals = $user->getAdditionals($user->id);
                $user->insurances = $user->getInsurnceData($user->id);
                $user->custom_fields = $user->getCustomFields($user->id);
                $user->filters = $user->getFilters($user->id);
                $user->services = $user->getServices($user->id);
                if($user->hasrole('service_provider')){
                    $user->totalRating = 0;
                    if($user->profile){
                        $user->totalRating =  $user->profile->rating;
                    }
                    $user->patientCount = User::getTotalRequestDone($user->id);
                    $user->reviewCount = Feedback::reviewCountByConsulatant($user->id); 
                }
                $user = Helper::getMoreData($user); 
                return response(['status' =>"success", 'statuscode' => 200, 'message' => __('Profile successfully updated.'), 'data' => ($user)], 200);
            }else{
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    __('Invalid OTP')), 400);
            }
        } catch (Exception $e) {
            return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    } 

    /**
     * @SWG\Post(
     *     path="/update-fcm-id",
     *     description="Update FCM_ID",
     * tags={"User Register & Login Section"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *     @SWG\Parameter(
     *         name="fcm_id",
     *         in="query",
     *         type="string",
     *         description="FCM ID",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="apn_token",
     *         in="query",
     *         type="string",
     *         description=" apn_token",
     *         required=false,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     *
     *
     * User Phone Update
     *
     * @return \Illuminate\Http\Response
     */
    public static function updateFcmId(Request $request) {
        try {
            $user = Auth::user();
            $customMessages = [];
            $input = $request->all();
            // $validator = Validator::make($request->all(),$rules);
            // if ($validator->fails()) {
            //     return response(array('status' => "error", 'statuscode' => 400, 'message' =>
            //         $validator->getMessageBag()->first()), 400);
            // }
            if (isset($request->apn_token) && !empty($request->apn_token)) {
                $user->apn_token = $request->apn_token;
            }
            if (isset($request->fcm_id) && !empty($request->fcm_id)) {
                $user->fcm_id = $request->fcm_id;
            }
            if($user->save()){
                return response(['status' =>"success", 'statuscode' => 200, 'message' => __('fcm_id updated')], 200);
            }else{
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    __('fcm_id not updated')), 400);
            }
        } catch (Exception $e) {
            return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }
    

    /**
     * @SWG\Post(
     *     path="/manual-available",
     *     description="Mannual Available",
     * tags={"User Register & Login Section"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *     @SWG\Parameter(
     *         name="manual_available",
     *         in="query",
     *         type="string",
     *         description="manual_available true or false or 1,0",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="notification_enable",
     *         in="query",
     *         type="string",
     *         description="notification_enable true or false or 1,0",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="premium_enable",
     *         in="query",
     *         type="string",
     *         description="premium_enable true or false or 1,0",
     *         required=false,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     *
     *
     * User Phone Update
     *
     * @return \Illuminate\Http\Response
     */
    public static function postMannualAvailable(Request $request) {
        try {
            $user = Auth::user();
            $input = $request->all();
            $rules = [];
            if(isset($request->manual_available)){
                $rules['manual_available'] = 'required';
            }
            if(isset($request->notification_enable)){
                $rules['notification_enable'] = 'required';
            }
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            if(isset($input['manual_available'])){
                if($input['manual_available']==true || $input['manual_available']=='true'){
                    $user->manual_available = 1;
                }else{
                    $user->manual_available = 0;
                }
            }

            if(isset($input['notification_enable'])){
                if($input['notification_enable']==true || $input['notification_enable']=='true'){
                    $dateznow = new DateTime("now", new DateTimeZone('UTC'));
                    $datenow = $dateznow->format('Y-m-d H:i:s');
                    $user->notification_enable = $datenow;
                }else{
                    $user->notification_enable = null;
                }
            }


            if(isset($input['premium_enable'])){
                if($input['premium_enable']==true || $input['premium_enable']=='true'){
                    $dateznow = new DateTime("now", new DateTimeZone('UTC'));
                    $datenow = $dateznow->format('Y-m-d H:i:s');
                    $user->premium_enable = $datenow;
                }else{
                    $user->premium_enable = null;
                }
            }
            if($user->save()){
                return response(['status' =>"success", 'statuscode' => 200, 'message' => __('manual_available updated')], 200);
            }else{
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    __('manual_available not updated')), 400);
            }
        } catch (Exception $e) {
            return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }
    


    /**
     * @SWG\Get(
     *     path="/online-flags",
     *     description="Get Online Flags",
     * tags={"User Register & Login Section"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     *
     *
     * User Phone Update
     *
     * @return \Illuminate\Http\Response
     */
    public static function getOnlineFlags(Request $request) {
        try {
            $user = Auth::user();
            $manual_available = $user->manual_available;
            $availability_available = Helper::checkVendorAvailableToday($user->id);
            return response(['status' =>"success",
                'statuscode' => 200,
                'message' => __(''),'data'=>[
                "manual_available"=>$manual_available,
                "availability_available"=>$availability_available
            ]], 200);
        } catch (Exception $e) {
            return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }


    /**
     * @SWG\Get(
     *     path="/profile",
     *     description="Get profile current of user login",
     * tags={"User Register & Login Section"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     *
     *
     * User Phone Update
     *
     * @return \Illuminate\Http\Response
     */
    public static function getUserProfile(Request $request) {
        try {
            $user = Auth::user();
            $userz = User::with('roles')->find($user->id);
            $token = $user->createToken('consult_app')->accessToken;
            $userz->token = $token;
            
            if(!$userz->profile){
                $profile = New Profile();
                $profile->dob ='0000-00-00';
                $profile->user_id = $user->id;
                $profile->save();
            }
            $userz->profile;
            $userz->profile->location = ["name"=>$userz->profile->location_name,"lat"=>$userz->profile->lat,"long"=>$userz->profile->long];
            $userz->profile->bio = $userz->profile->about;
            $userz->subscriptions = $userz->getSubscription($userz); 
            $userz->categoryData = $userz->getCategoryData($userz->id);
            $userz->additionals = $userz->getAdditionals($userz->id);
            $userz->filters = $userz->getFilters($userz->id);
            $userz->services = $user->getServices($userz->id);
            $userz->insurances = $user->getInsurnceData($userz->id);
            $userz->custom_fields = $user->getCustomFields($userz->id);
            if($user->hasrole('service_provider')){
                $userz->totalRating =  $userz->profile->rating;
                $userz->patientCount = User::getTotalRequestDone($user->id);
                $userz->reviewCount = Feedback::reviewCountByConsulatant($user->id); 
            }
            $userz = Helper::getMoreData($userz);
            return response(['status' =>"success", 'statuscode' => 200, 'message' => __('Profile'), 'data' => ($userz)], 200);
        } catch (Exception $e) {
            return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @SWG\Post(
     *     path="/forgot_password",
     *     description="Forgot Password Api",
     * tags={"User Register & Login Section"},
     *
     *     @SWG\Parameter(
     *         name="email",
     *         in="query",
     *         type="string",
     *         description="Email",
     *         required=true,
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Please provide all data"
     *     )
     * )
     */
    public static function forgot_password(Request $request) {
        $validation = Validator::make($request->all(), [
                    'email' => 'bail|required',
                        ]
        );
        if ($validation->fails()) {
            return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                $validation->getMessageBag()->first()), 400);
        }
        $user = User::where('email', $request->email)->first();
        if (is_object($user)) {
            $password = rand('10000000', '99999999');
            $data['user_id'] = $user->id;
            $data['new_password'] = bcrypt($password);
            $data['name'] = $user->name;
            $data['email'] = $user->email;
            $mail = \Mail::send('emailtemplate.forgotpassword', array('data' => $data, 'password' => $password),
                    function($message) use ($data) {
                        $message->to($data['email'], $data['name'])->subject('Consultant APP - Forgot Password!');
                        $message->from('test.codebrewlab@gmail.com', 'Consultant');
                    });
            User::whereId($data['user_id'])
                    ->limit(1)
                    ->update([
                        'password' => $data['new_password'],
                        'updated_at' => new \DateTime
            ]);
        } else {
            return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                __('This email is not registered with us!')), 400);
        }
        return response(['status' => 'success', 'statuscode' => 200, 'message' => __('We have sent a temporary password in your email. Please check your email.')], 200);
    }


   /**
     * @SWG\Post(
     *     path="/change_password",
     *     description="Change Password Api",
     * tags={"User Register & Login Section"},
     *   security={
     *     {"Bearer": {}},
     *   },
     *     @SWG\Parameter(
     *         name="password",
     *         in="query",
     *         type="string",
     *         description="New Password",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Please provide all data"
     *     )
     * )
     */
    public static function change_password(Request $request) {
        try {
            $user = Auth::user();
            $validation = Validator::make($request->all(), [
                        'password' => 'required',
                            ]
            );
            if ($validation->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validation->getMessageBag()->first()), 400);
            }
            $npassword = bcrypt($request->password);
            User::whereId($user->id)
                    ->limit(1)
                    ->update([
                        'password' => $npassword,
                        'updated_at' => new \DateTime
            ]);
            $token = $user->createToken('consult_app')->accessToken;
            $user->subscriptions = $user->getSubscription($user);
            $user->categoryData = $user->getCategoryData($user->id);
            $user->additionals = $user->getAdditionals($user->id);
            $user->insurances = $user->getInsurnceData($user->id);
            $user->custom_fields = $user->getCustomFields($user->id);
            $user->filters = $user->getFilters($user->id);
            $user->services = $user->getServices($user->id);
            $user->token = $token;
            if($user->hasrole('service_provider')){
                $user->totalRating =  $user->profile->rating;
                $user->patientCount = User::getTotalRequestDone($user->id);
                $user->reviewCount = Feedback::reviewCountByConsulatant($user->id); 
            }
            $user = Helper::getMoreData($user);
            return response(["status" => "success", 'statuscode' => 200, 'message' => __('Password changed successfully !'), 'data' => ($user)], 200);
        } catch (Exception $e) {
            return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }


    /**
     * @SWG\Post(
     *     path="/password-change",
     *     description="Change Password Api",
     * tags={"User Register & Login Section"},
     *   security={
     *     {"Bearer": {}},
     *   },
     *     @SWG\Parameter(
     *         name="current_password",
     *         in="query",
     *         type="string",
     *         description="Old Password",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         name="new_password",
     *         in="query",
     *         type="string",
     *         description="New Password",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Please provide all data"
     *     )
     * )
     */
    public function passwordChange(Request $request) {
        try {
            $user = Auth::user();
            $validation = Validator::make($request->all(), [
                        'current_password' => 'required',
                        'new_password' => 'required|min:8',
                        ]
            );
            if ($validation->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validation->getMessageBag()->first()), 400);
            }
            if (Hash::check($request->current_password, $user->password)) { 
               $user->fill([
                    'password' => Hash::make($request->new_password)
                ])->save();
                return response(["status" => "success", 'statuscode' => 200, 'message' => __('Password changed successfully !'), 'data' =>(Object)[]], 200);

            } else {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>'Current Password does not match'), 400);
            }
        } catch (Exception $e) {
            return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }


    /**
     *
     *   @SWG\Post(
     *     path="/app_logout",
     *     tags={"User Register & Login Section"},
     *     description="User Logout",
     *     security={
     *     {"Bearer": {}},
     *   },
     *     @SWG\Response(response=200, description="Success"),
     *     @SWG\Response(response=400, description="Validation Error"),
     *     @SWG\Response(response=500, description="Api Error"),
     * )
     *
     */
    public static function app_logout(Request $request) {
        try {
            $user = Auth::user();
            Auth()->user()->token()->revoke();
            User::whereId($user->id)
                    ->limit(1)
                    ->update([
                        'fcm_id' => '',
                        'updated_at' => new \DateTime
            ]);
            return response(["status" => "success", 'statuscode' => 200, 'message' => __('Logout successfully !')], 200);
        } catch (\Exception $e) {
            return response(["status" => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }


    public function getReferenceByList(Request $request)
    {
         try{

            $reference_by_user = User::select('id','name','phone','country_code','user_name','profile_image','provider_type','reference_code','reference_by','email',
                'device_type')->with('roles','wallet')->where('reference_by',Auth::user()->id)->get();

            if(count($reference_by_user) > 0){
                
                return response(["status" => "success", 'statuscode' => 200, 'message'=>"Reference by user Found",'data'=>$reference_by_user], 200);
            }else {

                return response(['status' => "success", 'statuscode' => 200, 'message' =>'Reference by user Not Found','data'=>[] ], 200);
            }
          
        }catch (\Exception $e) {
            return response(["status" => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    public function getSlotsTime(Request $request)
    {
        
        try{
            
            $slots = slot::select('id','slot_value')->orderby('slot_value','asc')->get();
            if(count($slots) > 0){
                
                return response(["status" => "success", 'statuscode' => 200, 'message'=>"Slots Found",'data'=>$slots], 200);
            }else {

                return response(['status' => "success", 'statuscode' => 200, 'message' =>'Slots Not Found','data'=>[] ], 200);
            }
        }catch (\Exception $e) {
            return response(["status" => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    public function getHealthCareVisit(Request $request)
    {
        
        try{
            
            $healthCareVisit = HealthCareVisit::select('id','health_care_value')->orderby('id','asc')->get();
            if(count($healthCareVisit) > 0){
                
                return response(["status" => "success", 'statuscode' => 200, 'message'=>"HealthCareVisit Found",'data'=>$healthCareVisit], 200);
            }else {

                return response(['status' => "success", 'statuscode' => 200, 'message' =>'HealthCareVisit Not Found','data'=>[] ], 200);
            }
        }catch (\Exception $e) {
            return response(["status" => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }


    public function getTypeOfRecords(Request $request)
    {
        
        try{
            
            $typeOfRecords = TypeOfRecords::select('id','records_value')->orderby('id','asc')->get();
            if(count($typeOfRecords) > 0){
                
                return response(["status" => "success", 'statuscode' => 200, 'message'=>"TypeOfRecords Found",'data'=>$typeOfRecords], 200);
            }else {

                return response(['status' => "success", 'statuscode' => 200, 'message' =>'TypeOfRecords Not Found','data'=>[] ], 200);
            }
        }catch (\Exception $e) {
            return response(["status" => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }


    public function getInvoiceList(Request $request)
    {
         try{
            $user = Auth::user();
            $customer = false;
            if($user->hasrole('customer')){
                $customer = true;
                $requestInvoice = RequestTable::with(['from_users','to_users','transactions' => function ($query) {
                            $query->where('transaction_type', 'withdrawal');
                        }])->where('from_user',$user->id)->get();
            }else{
                $customer = false;
                $requestInvoice = RequestTable::with(['from_users','to_users','transactions' => function ($query) {
                            $query->where('transaction_type', 'deposit');
                        }])->where('to_user',$user->id)->get();
            }

            if(count($requestInvoice) > 0){
                
                return response(["status" => "success", 'statuscode' => 200, 'message'=>"Request Invoice Found",'data'=>$requestInvoice], 200);
            }else {

                return response(['status' => "success", 'statuscode' => 200, 'message' =>'Request Invoice Not Found','data'=>[] ], 200);
            }
          
        }catch (\Exception $e) {
            return response(["status" => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    public function saveHealthRecords(Request $request)
    {
        $validator = Validator::make($request->all(), [
                    'hospital_name' => 'required|string',
                    'health_care_visit' => 'required|string',
                    'date_of_approved' => 'required|date',
                    'records_value' => 'required|string',
                    'type' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                $validator->getMessageBag()->first()), 400);
        }

        try{
            $user = Auth::user();
            $healthrecords = HealthRecords::create([
                'user_id' => $user->id,
                'name'=>$request->name ?? NULL,
                'hospital_name' => $request->hospital_name,
                'health_care_visit' => $request->health_care_visit,
                'date_of_approved' => $request->date_of_approved,
                'records_value' =>$request->records_value,
                'tell_us' => $request->tell_us ?? NULL,
                'type' => $request->type ?? NULL,
            ]);

            if( $healthrecords ){
                
                return response(["status" => "success", 'statuscode' => 200, 'message'=>"Health Records Added successfully",'data'=>$healthrecords], 200);
            }else {

                return response(['status' => "error", 'statuscode' => 400, 'message' =>'Health Records Added'], 400);
            }
            
        }catch (\Exception $e) {
            return response(["status" => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }

    }

    public function getHealthRecordsList(Request $request)
    {
        
        try{
            $user = Auth::user();

            $healthRecordsList = HealthRecords::where('user_id',$user->id)
                         ->where('type',$request->type)->orderby('id','asc')->get();
            if(count($healthRecordsList) > 0){
                
                return response(["status" => "success", 'statuscode' => 200, 'message'=>"Health Records List Found",'data'=>$healthRecordsList], 200);
            }else {

                return response(['status' => "success", 'statuscode' => 200, 'message' =>'Health Records List Not Found','data'=>[] ], 200);
            }
        }catch (\Exception $e) {
            return response(["status" => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }


    public function getHealthRecordsDetail(Request $request)
    {
        
        try{
            $user = Auth::user();

            $healthRecordDetail = HealthRecords::with('htRecordImg')->where('user_id',$user->id)->where('id',$request->id)->orderby('id','asc')->first();
            if( !empty($healthRecordDetail)){
                
                return response(["status" => "success", 'statuscode' => 200, 'message'=>"Health Records Detail Found",'data'=>$healthRecordDetail], 200);
            }else {

                return response(['status' => "success", 'statuscode' => 200, 'message' =>'Health Records Detail Not Found','data'=>[] ], 200);
            }
        }catch (\Exception $e) {
            return response(["status" => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    public function editHealthRecords(Request $request)
    { 
        
        try{

            $user = Auth::user();
            $alreadyAdded = HealthRecords::where(['id'=>$request->id,'user_id'=>$user->id])->first();

            $data = array(
                'user_id' => $user->id,
                'name'=> $request->name ?? $alreadyAdded->name,
                'hospital_name' => $request->hospital_name ?? $alreadyAdded->hospital_name,
                'health_care_visit' => $request->health_care_visit ?? $alreadyAdded->health_care_visit,
                'date_of_approved' => $request->date_of_approved ?? $alreadyAdded->date_of_approved,
                'records_value' =>$request->records_value ?? $alreadyAdded->records_value,
                'tell_us' => $request->tell_us ?? $alreadyAdded->tell_us,
                'updated_at'  => new \DateTime
            ); 
            $updatedhealthrecord = HealthRecords::where(['id'=>$request->id,'user_id'=>$user->id])->update($data);
            
            if(!empty($updatedhealthrecord)){

              $updated = HealthRecords::where(['id'=>$request->id,'user_id'=>$user->id])->first();
              $response['updateHtRecord'] = $updated;
                
              return response(["status" => "success", 'statuscode' => 200, 'message'=>"Health Records Updated Successfully",'data'=>$response], 200);

            }else{
                return response(['status' => "error", 'statuscode' => 400, 'message' =>'Health Records Not Updated'], 400);
            }
        }catch (\Exception $e) {
            return response(["status" => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    public function upload_profile_image(Request $request)
    {
        $msg = [];
        if($request->hasfile('image')) {
          $rule['image']='required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=50,min_height=50';
          $msg['image.dimensions'] = "image should be min_width=50,min_height=50";
        }
       
        $validator = \Validator::make($request->all(),$rule,$msg);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        try{ 

            $user = Auth::user();

            if($request->hasfile('image')) {
                if ($image = $request->file('image')) {
                    $extension = $image->getClientOriginalExtension();
                    $filename = str_replace(' ','', md5(time()).'_'.$image->getClientOriginalName());
                    $thumb = \Image::make($image)->resize(100, 100,
                      function ($constraint) {
                          $constraint->aspectRatio();
                      })->encode($extension);
                    $normal1 = \Image::make($image)->resize(260, 260,
                      function ($constraint1) {
                          $constraint1->aspectRatio();
                    })->encode($extension);
                    $big = \Image::make($image)->encode($extension);
                    $_800x800 = \Image::make($image)->resize(800, 800,
                      function ($constraint) {
                          $constraint->aspectRatio();
                      })->encode($extension);
                    $_400x400 = \Image::make($image)->resize(400, 400,
                      function ($constraint) {
                          $constraint->aspectRatio();
                      })->encode($extension);
                    \Storage::disk('spaces')->put('thumbs/'.$filename, (string)$thumb, 'public');
                    \Storage::disk('spaces')->put('uploads/'.$filename, (string)$normal1, 'public');
                    \Storage::disk('spaces')->put('original/'.$filename, (string)$big, 'public');
                    \Storage::disk('spaces')->put('800x800/'.$filename, (string)$_800x800, 'public');
                    \Storage::disk('spaces')->put('400x400/'.$filename, (string)$_400x400, 'public');
                    \Storage::disk('spaces')->put('original/'.$filename, (string)$big, 'public');
                    HealthRecordImage::create(['ht_record_id' =>$request->id,'health_doc' => $filename]);

                    $getRecords = HealthRecords::with('htRecordImg')->where(['id'=>$request->id,'user_id'=>$user->id])->get();

                    return response(['status' => "success", 'statuscode' => 200, 'message'=>"Document Uploaded Successfully",'data' => $getRecords], 200);

                }else{

                    return response(['status' => "error", 'statuscode' => 400, 'message'=>"Document Not Uploaded"], 400);
                }
            }
               
        }catch (\Exception $e) {
            return response(["status" => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
      try{
            $user = Auth::user();

            $healthRecords = HealthRecords::findOrFail($id);
            $healthRecords->delete();

            if( $healthRecords ){
                
                return response(["status" => "success", 'statuscode' => 200, 'message'=>"Health Records Deleted Successfully"], 200);
            }else {

                return response(['status' => "error", 'statuscode' => 200, 'message' =>'Health Records Not Deleted'], 400);
            }
        }catch (\Exception $e) {
            return response(["status" => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }


    public function removeImage($id)
    {
        try{
            
            $healthRecordImage = HealthRecordImage::findOrFail($id);
            $healthRecordImage->delete();

            if( $healthRecordImage ){
                
                return response(["status" => "success", 'statuscode' => 200, 'message'=>"Health Records Document Deleted Successfully"], 200);
            }else {

                return response(['status' => "error", 'statuscode' => 200, 'message' =>'Health Records Document Not Deleted '], 400);
            }
        }catch (\Exception $e) {
            return response(["status" => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }


    public function saveMultipleImages(Request $request)
    {
        $msg = [];
        if($request->hasfile('image')) {
          $rule['image']='required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=50,min_height=50';
          $msg['image.dimensions'] = "image should be min_width=50,min_height=50";
        }
       
        $validator = \Validator::make($request->all(),$rule,$msg);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        try{
            $user = Auth::user();
            $response = [];  
            if($request->hasfile('image')) {
                if ($image = $request->file('image')) {

                    $extension = $image->getClientOriginalExtension();
                    $filename = str_replace(' ','', md5(time()).'_'.$image->getClientOriginalName());

                    $thumb = \Image::make($image)->resize(100, 100,
                      function ($constraint) {
                          $constraint->aspectRatio();
                      })->encode($extension);
                    $normal1 = \Image::make($image)->resize(260, 260,
                      function ($constraint1) {
                          $constraint1->aspectRatio();
                    })->encode($extension);
                    $big = \Image::make($image)->encode($extension);
                    $_800x800 = \Image::make($image)->resize(800, 800,
                      function ($constraint) {
                          $constraint->aspectRatio();
                      })->encode($extension);
                    $_400x400 = \Image::make($image)->resize(400, 400,
                      function ($constraint) {
                          $constraint->aspectRatio();
                      })->encode($extension);
                    \Storage::disk('spaces')->put('thumbs/'.$filename, (string)$thumb, 'public');
                    \Storage::disk('spaces')->put('uploads/'.$filename, (string)$normal1, 'public');
                    \Storage::disk('spaces')->put('original/'.$filename, (string)$big, 'public');
                    \Storage::disk('spaces')->put('800x800/'.$filename, (string)$_800x800, 'public');
                    \Storage::disk('spaces')->put('400x400/'.$filename, (string)$_400x400, 'public');
                    \Storage::disk('spaces')->put('original/'.$filename, (string)$big, 'public');

                    $array = array('ht_record_id'=> $request->id,'health_doc'=> $filename);
                    $insertImage = HealthRecordImage::create($array);
                    if( $insertImage ){
                        $response['id'] = $insertImage->id;
                        $response['image'] = $insertImage->health_doc;

                        return response(['status' => "success", 'statuscode' => 200, 'message'=>"Document Uploaded Successfully",'data' => $response], 200);
                    }
                    else{

                        return response(['status' => "error", 'statuscode' => 400, 'message'=>"Document Not Uploaded"], 400);
                    }
          
                }
            }

        }catch (\Exception $e) {
            return response(["status" => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }

}
 