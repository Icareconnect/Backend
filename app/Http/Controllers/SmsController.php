<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Jwt\ClientToken;
use Twilio\Exceptions\TwilioException;
use GuzzleHttp\Exception\GuzzleException;
// use GuzzleHttp\Client;
use Validator;
use DateTime,DateTimeZone,DB;
use Twilio\Rest\Client;
use App\Helpers\Helper;
use App\User;
class SmsController extends Controller
{
    protected $code, $smsVerifcation;

    function __construct() {
        $this->smsVerifcation = new \App\Model\Verification();
        $this->emailVerifcation = new \App\Model\EmailVerification();
    }

    public function sendLink(Request $request){
        try {
            $validation = Validator::make($request->all(), [
                        'phone' => 'required']
            );
            if ($validation->fails()) {
                return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                    $validation->getMessageBag()->first()), 400);
            }
            $f_keys = Helper::getClientFeatureKeys('social login','Twilio OTP');
            $accountSid = isset($f_keys['account_sid'])?$f_keys['account_sid']:env('TWILIO_ACCOUNT_SID_NEW');
            $authToken = isset($f_keys['token'])?$f_keys['token']:env('TWILLIO_TOKEN_NEW');
            $number = isset($f_keys['number'])?$f_keys['number']:env('TWILLIO_NUMBER');
            try {
                $twilio = new Client($accountSid, $authToken);
                   $body = "iCareConnect
Hey, Here is an online platform that matches fully vetted professionals with Health care facilities, Long term care homes and private homes.
iOS app download Link:
Nurse : https://i.diawi.com/17mV1y
User: https://i.diawi.com/t72PJ3
Android app download Link:https://drive.google.com/drive/folders/16B2wfuNUAYWA1A6EX6qSQr3QIrpVeyL6";
                $message = $twilio->messages->create($request->phone,
                                           ["body" =>$body,
                                           "from" => $number]);
                $data = (object)[];
                if ($message->sid) {
                    $send  = new \App\Model\SendLink();
                    $send->phone = $request->phone;
                    $send->status = 'success';
                    $send->save();
                    return response(['status' => 'success', 'statuscode' => 200, 'message' => __('Link has been sent to your number')], 200);
                } else {
                    return response(['status' => 'error', 'statuscode' => 400, 'message' => __('Link not sent to your number. Please try again!')], 400);
                }
            } catch (Exception $e) {
                return response(['status' => 'error', 'statuscode' => 500, 'message' => $e->getMessage()], 500);
            }
        } catch (Exception $e) {
            return response(['status' =>'error', 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @SWG\Post(
     *     path="/send-sms",
     *     description="Send OTP Api",
     * tags={"SMS"},
     *  @SWG\Parameter(
     *         name="phone",
     *         in="query",
     *         type="string",
     *         description="Mobile Number (**********)",
     *         required=true,
     *     ), 
     *  @SWG\Parameter(
     *         name="country_code",
     *         in="query",
     *         type="string",
     *         description="country_code +91",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="user_type",
     *         in="query",
     *         type="string",
     *         description="user_type e.g customer,service_provider",
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
    public function store(Request $request) {
        try {
            $validation = Validator::make($request->all(), [
                        'phone' => 'required',
                        'country_code'=>'required']
            );
            if ($validation->fails()) {
                return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                    $validation->getMessageBag()->first()), 400);
            }
            $user_type = $request->header('user-type');
            if($user_type){
                $user = User::where(function ($query) {
                    $query->where('phone',request('phone'))->where('country_code',request('country_code'));
                })->first();
                if($user){
                    $current_role = ucwords(str_replace('_', ' ', $user->roles[0]['name']));
                    if(!$user->hasrole($user_type)){
                        return response(array('status' => "error", 'statuscode' => 400, 'message' =>"You are register as $current_role with same account, Please try with other account."), 400);
                    }
                }
            }
            $code = rand(1000, 9999); //generate random code
            $request['code'] = $code; //add code in $request body
            $this->smsVerifcation->store($request); //call store method of model
            return $this->sendSms($request); // send and return its response
        } catch (Exception $e) {
            return response(['status' =>'error', 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @SWG\Post(
     *     path="/send-email-otp",
     *     description="Send OTP Api For Email",
     * tags={"SMS"},
     *  @SWG\Parameter(
     *         name="email",
     *         in="query",
     *         type="string",
     *         description="Valid Email Id",
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
    public function sendEmailOtp(Request $request) {
        try {
            $validation = Validator::make($request->all(), [
                        'email' => 'required|email'
                    ]
            );
            if ($validation->fails()) {
                return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                    $validation->getMessageBag()->first()), 400);
            }
            $user_type = $request->header('user-type');
            $column_name = 'email';
            $user = User::where(function ($query) use($column_name) {
                $query->where($column_name, '=', request('email'));
            })->first();
            if(!$user){
                    $code = rand(1000, 9999); //generate random code
                    $request['code'] = $code; //add code in $request body
                    $request['status'] = 'pending'; //add code in $request body
                    $this->emailVerifcation->store($request); //call store method of model
                    return $this->sendEmail($request); // send and return its response
            }
            $current_role = ucwords(str_replace('_', ' ', $user->roles[0]['name']));
            return response(array('status' => "error", 'statuscode' => 400, 'message' =>"You are register as $current_role with same account, Please try with other account."), 400);
        } catch (Exception $e) {
            return response(['status' =>'error', 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }


    public function sendSms($request) {
        $data['otp'] = $request->code;
        $data['to'] = $request->phone;
        $f_keys = Helper::getClientFeatureKeys('social login','Twilio OTP');
        $accountSid = isset($f_keys['account_sid'])?$f_keys['account_sid']:env('TWILIO_ACCOUNT_SID_NEW');
        $authToken = isset($f_keys['token'])?$f_keys['token']:env('TWILLIO_TOKEN_NEW');
        $number = isset($f_keys['number'])?$f_keys['number']:"+14158959801";
        try {
            $body = "CODE: $request->code";
            if(\Config::get('client_connected') && (\Config::get('client_data')->domain_name=='healtcaremydoctor')){
                $body = "Welcome to My Doctor your Code: $request->code";
            }else if(\Config::get('client_connected') && (\Config::get('client_data')->domain_name=='intely')){
                $body = "Welcome to iCareConnect your Code: $request->code";
            }
            $twilio = new Client($accountSid, $authToken);
            try{
                $message = $twilio->messages->create($request->country_code.$request->phone, // to
                                       ["body" =>$body,
                                       "from" => $number]);
            }catch(TwilioException $ex){
                $message = $ex->getMessage().' '.$ex->getCode();
                if($ex->getCode()==21211){
                    $message = 'Invalid phone number';
                }
                return response(['status' => 'error', 'statuscode' => 500, 'message' =>$message], 500);
            }
            $data = (object)[];
            if ($message->sid) {
                return response(['status' => 'success', 'statuscode' => 200, 'message' => __('OTP sent to your mobile number!'), 'data' => $data], 200);
            } else {
                return response(['status' => 'error', 'statuscode' => 400, 'message' => __('OTP has not been sent. Please try again!'), 'data' => $data], 400);
            }
        } catch (Exception $e) {
            return response(['status' => 'error', 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    public function sendEmail($request) {
        $data['otp'] = $request->code;
        $data['to'] = $request->email;
        try {
            $data['email'] = $request->email;
            $mail = \Mail::send('emailtemplate.emailotp', array('data' => $data, 'otp' => $data['otp']),
                    function($message) use ($data) {
                        $message->to($data['email'],'Verification')->subject('OTP Verification!');
                        $message->from('test.codebrewlab@gmail.com', 'no-reply');
                    });
            return response(['status' => 'success', 'statuscode' => 200, 'message' => __('OTP sent to your Email ID!')], 200);
        } catch (Exception $e) {
            return response(['status' => 'error', 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }


    /**
     * @SWG\Post(
     *     path="/email-verify",
     *     description="Verify Email",
     * tags={"SMS"},
     *  @SWG\Parameter(
     *         name="email",
     *         in="query",
     *         type="string",
     *         description="Valid Email Id",
     *         required=true,
     *     ), 
     *  @SWG\Parameter(
     *         name="code",
     *         in="query",
     *         type="string",
     *         description="otp code",
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

    public function verifyEmail(Request $request) {
        try {
            $validation = Validator::make($request->all(), [
                'email' => 'required',
                'otp' => 'required'
            ]
            );
            if ($validation->fails()) {
                return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                    $validation->getMessageBag()->first()), 400);
            }
            $input = $request->all();
            $datenow = new DateTime("now", new DateTimeZone('UTC'));
            $datenowone = $datenow->format('Y-m-d H:i:s');

            $verify = \App\Model\EmailVerification::where([
                'email' => $input['email'],
                'code'=>$input['otp'],
                'status'=>'pending'
            ])->latest()->first();
            if(!$verify){
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>'Invalid OTP'), 400);
            }
            if($verify){
                $verify->status = 'verified';
                $verify->save();
                $user = User::where(function ($query) {
                            $query->where(['email'=>request('email')]);
                        })->first();
                if($user){
                    $user->email_verified_at = $datenowone;
                    $user->save();
                }
                return response(['status' => 'success', 'statuscode' => 200, 'message' => __('Email has been verified')], 200);
            }
        } catch (Exception $e) {
            return response(['status' => 'error', 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }


    public function verifyContact(Request $request) {
        try {
            $validation = Validator::make($request->all(), [
                        'phone' => 'required',
                        'code' => 'required',
                        'country_code'=>'required'
                    ]
            );
            if ($validation->fails()) {
                return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                    $validation->getMessageBag()->first()), 400);
            }
            $datenow = new DateTime("now", new DateTimeZone('UTC'));
            $datenowone = $datenow->format('Y-m-d H:i:s');
            $smsVerifcation = $this->smsVerifcation::where(['phone' => $request->phone, 
                'code' => $request->code,
                'country_code'=>$request->country_code
            ])->where('expired_at', '>=', $datenowone)
                    ->latest() //show the latest if there are multiple
                    ->first();
            if ($request->code == $smsVerifcation->code) {
                $request["status"] = 'verified';
                $smsVerifcation->updateModel($request,$smsVerifcation->id);
                return response(['status' => 'success', 'statuscode' => 200, 'message' => __('Phone Number is verified'), 'data' => $data], 200);
            } else {
                return response(['status' => 'error', 'statuscode' => 500, 'message' => __('Phone Number not  verified'), 'data' => $data], 500);
            }
        } catch (Exception $e) {
            return response(['status' => 'error', 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }
}
