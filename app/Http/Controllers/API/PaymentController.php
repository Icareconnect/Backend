<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use Config;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator,Hash,Mail,DB;
use DateTime,DateTimeZone;
use Redirect,Response,File,Image;
use Illuminate\Support\Facades\URL;
use App\Model\Role;
use App\Model\Wallet;
use App\Model\Profile;
use App\Model\UserPackage;
use App\Model\Payment;
use App\Model\Transaction;
use App\Model\Card;
use App\Model\SocialAccount;
use App\Model\BankAccount;
use App\Model\PayoutRequest,App\Model\Package;
use App\Model\EnableService;
use Socialite,Exception;
use Intervention\Image\ImageManager;
use Carbon\Carbon;
use Cartalyst\Stripe\Stripe;
use App\Notification;
use App\Model\ConsultClass;
use App\Model\EnrolledUser;
use Razorpay\Api\Api;
class PaymentController extends Controller{
	public $successStatus = 200;


	/**
     * @SWG\Post(
     *     path="/add-card",
     *     description="add card",
     * tags={"Payment"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="card_number",
     *         in="query",
     *         type="string",
     *         description=" card number",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="exp_month",
     *         in="query",
     *         type="number",
     *         description="exp_month e.g 1 to 12",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="exp_year",
     *         in="query",
     *         type="number",
     *         description="date e.g 2022",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="cvc",
     *         in="query",
     *         type="number",
     *         description="cvc number",
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
    public static function postAddCard(Request $request) {
    	try{
	    	$user = Auth::user();
	    	$rules = ['card_number' => 'required',
	    			 'exp_month'=>'required',
	    			 'cvc'=>'required',
	    			 'exp_year'=>'required',
	    	];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $cards = [];
            $stripe_id = $user->stripe_id;
            $key = env('STRIPE_TEST_KEY');
            $keys = Helper::getClientFeatureKeys('Payment Gateway','Stripe');
             if(isset($keys['STRIPE_MODE']) && $keys['STRIPE_MODE']=='test'){
                $key = $keys['STRIPE_TEST_KEY'];
             }elseif (isset($keys['STRIPE_MODE']) && $keys['STRIPE_MODE']=='live') {
                $stripe_id = $user->stripe_live_id;
                $key = $keys['STRIPE_LIVE_KEY'];
            }
            $stripe = new Stripe($key);
            $token = $stripe->tokens()->create([
			    'card' => [
			        'number'    => $request->card_number,
			        'exp_month' => $request->exp_month,
			        'cvc'       => $request->cvc,
			        'exp_year'  => $request->exp_year,
			    ],
			]);
			$fingerprint = $token['card']['fingerprint'];
            $user_card = Card::select('id')->where(['fingerprint'=>$fingerprint,'user_id'=>$user->id])->first();
            if(!$user_card){
				$card = $stripe->cards()->create($stripe_id, $token['id']);
            	$card_id = $user->attachCard($user,$card);
                $cards = $user->getAttachedCards($user);
				return response(['status' => "success", 'statuscode' => 200,
	                            'message' => __('Card Added'), 'data' =>['cards'=>$cards]], 200);
            }else{
            	$message = 'Card already attached';
            }
	    	return response(array('status' => "error", 'statuscode' => 400, 'message' =>__($message)), 400);
    	}catch(Exception $ex){
    		return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
    	}
    }

    /**
     * @SWG\Post(
     *     path="/update-card",
     *     description="update card",
     * tags={"Payment"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="name",
     *         in="query",
     *         type="string",
     *         description=" user name on card",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="card_id",
     *         in="query",
     *         type="string",
     *         description=" card id ",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="exp_month",
     *         in="query",
     *         type="number",
     *         description="exp_month e.g 1 to 12",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="exp_year",
     *         in="query",
     *         type="number",
     *         description="date e.g 2022",
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
    public static function updateCard(Request $request) {
        try{
            $user = Auth::user();
            $rules = ['card_id' => 'required|exists:cards,id'];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $card_detail = [];
            $input = $request->all();
            $card = Card::where(['id'=>$input['card_id'],'user_id'=>$user->id])->first();
            if(!$card){
                return response(['status' => "error", 'statuscode' => 400,'message' => __('This card is not attached with your account ')], 400);
            }
            if(isset($input['name'])){
                $card_detail['name'] = $input['name'];
            }
            if(isset($input['exp_month'])){
                $card_detail['exp_month'] = $input['exp_month'];
            }
            if(isset($input['exp_year'])){
                $card_detail['exp_year'] = $input['exp_year'];
            }
            $cards = [];
            $stripe_id = $user->stripe_id;
            $key = env('STRIPE_TEST_KEY');
            $keys = Helper::getClientFeatureKeys('Payment Gateway','Stripe');
             if(isset($keys['STRIPE_MODE']) && $keys['STRIPE_MODE']=='test'){
                $key = $keys['STRIPE_TEST_KEY'];
             }elseif (isset($keys['STRIPE_MODE']) && $keys['STRIPE_MODE']=='live') {
                $stripe_id = $user->stripe_live_id;
                $key = $keys['STRIPE_LIVE_KEY'];
            }
            $stripe = new Stripe($key);
            $card_data = $stripe->cards()->update($stripe_id,$card->card_id,$card_detail);
            if($card_data){
                $card->card_last_four = $card_data['last4'];
                $card->fingerprint = $card_data['fingerprint'];
                $card->card_brand = $card_data['brand'];
                $card->name = $card_data['name'];
                $card->save();
            }
            $cards = $user->getAttachedCards($user);
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('Card Updated'), 'data' =>['cards'=>$cards]], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }
    /**
     * @SWG\Post(
     *     path="/delete-card",
     *     description="delete card",
     * tags={"Payment"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="card_id",
     *         in="query",
     *         type="string",
     *         description=" card id ",
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
    public static function deleteCard(Request $request) {
        try{
            $user = Auth::user();
            $rules = ['card_id' => 'required|exists:cards,id'];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $card_detail = [];
            $input = $request->all();
            $card = Card::where(['id'=>$input['card_id'],'user_id'=>$user->id])->first();
            if(!$card){
                return response(['status' => "error", 'statuscode' => 400,'message' => __('This card is not attached with your account ')], 400);
            }
            $cards = [];
            $stripe_id = $user->stripe_id;
            $key = env('STRIPE_TEST_KEY');
            $keys = Helper::getClientFeatureKeys('Payment Gateway','Stripe');
             if(isset($keys['STRIPE_MODE']) && $keys['STRIPE_MODE']=='test'){
                $key = $keys['STRIPE_TEST_KEY'];
             }elseif (isset($keys['STRIPE_MODE']) && $keys['STRIPE_MODE']=='live') {
                $stripe_id = $user->stripe_live_id;
                $key = $keys['STRIPE_LIVE_KEY'];
            }
            $stripe = new Stripe($key);
            $card_data = $stripe->cards()->delete($stripe_id,$card->card_id);
            if(isset($card_data['deleted'])){
                $cards = $user->deAattachCard($user->id,$card->id);
            }
            $cards = $user->getAttachedCards($user);
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('Card Deleted'), 'data' =>['cards'=>$cards]], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }
    /**
     * @SWG\Post(
     *     path="/add-money",
     *     description="add money",
     * tags={"Payment"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="balance",
     *         in="query",
     *         type="string",
     *         description="Balance add",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="card_id",
     *         in="query",
     *         type="string",
     *         description="Card ID",
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
    public function postAddMoney(Request $request) {
    	try{
	    	$user = Auth::user();
            $requires_source_action = false;
            $payment_gateway = 'stripe';
            $client_data = null;
            if(Config::get("client_connected") && Config::get("client_data")){
                 $client_data = Config::get("client_data");
                 $payment_gateway = $client_data->payment_type;
            }
	    	$rules = ['balance' => 'required'];
            if($payment_gateway=='stripe'){
                $rules['card_id'] = 'required';
            }
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $input = $request->all();
            if($payment_gateway=='stripe'){
                $card = Card::select('id','card_id')->where(['id'=>$request->card_id,'user_id'=>$user->id])->first();
                $currency_code = 'INR';
                $currency = EnableService::where('type','currency')->first();
                if(isset($currency->value)){
                    $currency_code = $currency->value;
                }
                $transaction_type = 'deposit';
                if($user->hasrole('service_provider')){
                    $transaction_type = 'add_money';
                }
                if($card){
                    $message = 'Transaction failed';
                    $live = false;
                    $test_key = env('STRIPE_TEST_KEY');
                    $live_key = env('STRIPE_LIVE_KEY');
                    $keys = Helper::getClientFeatureKeys('Payment Gateway','Stripe');
                    $test_key = isset($keys['STRIPE_TEST_KEY'])?$keys['STRIPE_TEST_KEY']:$test_key;
                    $live_key = isset($keys['STRIPE_LIVE_KEY'])?$keys['STRIPE_LIVE_KEY']:$live_key;
                    $stripe = new Stripe($test_key);
                    $paymentIntent = $stripe->paymentIntents()->create([
        			    'amount' => $request->balance,
        			    'currency' => $currency_code,
                        'description'=>"consultant service",
        			    'customer'=>$user->stripe_id,
                        'payment_method'=>$card->card_id,
        			    'confirm'=>true,
        			]);
        			$transaction = Transaction::create(array(
        				'amount'=>$request->balance,
        				'transaction_type'=>$transaction_type,
        				'status'=>'pending',
        				'wallet_id'=>$user->wallet->id,
                        'closing_balance'=>$user->wallet->balance,
        			));
                    if($paymentIntent['id']){
                        $transaction->transaction_id  = $paymentIntent['id'];
                        $transaction->payment_gateway  = $payment_gateway;
                        $transaction->save();
                    }
        			if($paymentIntent['status']=='succeeded'){
                        $url = null;
        				$payment = Payment::create(array('from'=>1,'to'=>$user->id,'transaction_id'=>$transaction->id));
        				return response(array(
                            'status' => "success",
                            'statuscode' => 200,
                            'data'=>['transaction_id'=>$transaction->transaction_id,
                            'requires_source_action'=>$requires_source_action,
                            'url'=>$url],
                            'message' =>__('Balance  Adding...')), 200);
        			}else if($paymentIntent['status']=='requires_source_action'){
                        $type = $paymentIntent['next_source_action']['type'];
                        $url = null;
                        if($type=='use_stripe_sdk'){
                            $url = $paymentIntent['next_source_action'][$type]['stripe_js'];
                        }
                        if($url){
                            $requires_source_action = true;
                        }
                        $payment = Payment::create(array('from'=>1,'to'=>$user->id,'transaction_id'=>$transaction->id));
                        return response(array(
                            'status' => "success",
                            'statuscode' => 200,
                            'data'=>['transaction_id'=>$transaction->transaction_id,
                            'requires_source_action'=>$requires_source_action,
                            'url'=>$url],
                            'message' =>__('Balance  Adding...')), 200);
        			}else{
                        $transaction->status = 'failed';
                        $transaction->save();
                        $payment = Payment::create(array('from'=>1,'to'=>$user->id,'transaction_id'=>$transaction->id));
                    }
                }else{
                    $message = 'Card Not Found';
                }
            }elseif($payment_gateway=='paystack'){
                if(!$user->email){
                    return response(array('status' => "error", 'statuscode' => 400, 'message' =>__('Email required please update your profile')), 400);
                }
                $response =$this->createPayStackId($request,$client_data,$user);
                if($response['status']=='error'){
                    return response($response, 400);
                }
                $order = $response['response'];
                $transaction = Transaction::create(array(
                    'amount'=>$request->balance,
                    'transaction_type'=>'add_money',
                    'status'=>'pending',
                    'wallet_id'=>$user->wallet->id,
                    'closing_balance'=>$user->wallet->balance,
                ));
                if(isset($order['id'])){
                    $input['order_data'] = $order;
                    $transaction->order_id  = $order['id'];
                    $transaction->transaction_id  = $order['id'];
                }
                $transaction->raw_details = json_encode($input);
                $transaction->payment_gateway  = $payment_gateway;
                $transaction->module_table  = 'add_money';
                $transaction->save();
                $requires_source_action =  true;
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
                        'url'=>$order['authorization_url']],
                        'message' =>__('Payment initializing...')
                    );
                }
            }else{
                return $this->alRajhiPayment($request,$payment_gateway);
            }
	    	return response(array('status' => "error", 'statuscode' => 400, 'message' =>__($message)), 400);
    	}catch(Exception $ex){
    		return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
    	}
    }

    public function alRajhiPayment($request,$payment_gateway){
        try{
            $user = Auth::user();
            $requires_source_action = true;
            $responseURL = "https://homedoctor.royoconsult.com/al_rajhi_bank/webhook";
            $trackId = (string)time();
            $transaction_type = 'deposit';
            if($user->hasrole('service_provider')){
                $transaction_type = 'add_money';
            }
            // $transaction_type = 'deposit';
            $message = 'Transaction failed';
            $textToEncrypt[] = [
                'id'=>'f7qs1EKUSi9N5j2',
                'password'=>'h$f3qEKrTE!$435',
                'action' => "1",
                'currencyCode'=>"682",
                'trackId'=>$trackId,
                'amt' => (string)$request->balance,
                // 'amt' => "100",
                'errorURL'=>$responseURL,
                'responseURL'=>$responseURL,
            ];
            $tarndata = urlencode(json_encode($textToEncrypt));
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
                'amount'=>$request->balance,
                'transaction_type'=>$transaction_type,
                'status'=>'pending',
                'wallet_id'=>$user->wallet->id,
                'closing_balance'=>$user->wallet->balance,
            ));
            $transaction->raw_details = json_encode($request);
            $transaction->transaction_id  = $payment_id;
            $transaction->order_id  = $trackId;
            $transaction->payment_gateway  = 'al_rajhi_bank';
            $transaction->module_table  = 'add_money';
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




    /**
     * @SWG\Post(
     *     path="/razor-pay-webhook",
     *     description="Razor Pay Success or Failed",
     * tags={"Payment"},
     *  @SWG\Parameter(
     *         name="order_id",
     *         in="query",
     *         type="string",
     *         description="order_id",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="razorpayPaymentId",
     *         in="query",
     *         type="string",
     *         description=" razorpayPaymentId",
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
    public static function postWebhookRazorPay(Request $request) {
        try{
            $user = Auth::user();
            $requires_source_action = false;
            $rules = ['order_id' => 'required','razorpayPaymentId'=>'required'];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            if(isset($request->order_id)){
                $transaction = Transaction::where('order_id',$request->order_id)->first();
                if(!$transaction){
                    return response(array(
                        'status' =>"error",
                        'statuscode' => 400,
                        'message' =>__('Transaction not valid')),400);
                }
                $razorpayPaymentId = 0;
                if(isset($request->razorpayPaymentId))
                    $razorpayPaymentId = $request->razorpayPaymentId;
                $razorpay_key = "";
                $razorpay_secret = "";
                if(Config::get("client_connected") && Config::get("client_data")){
                     $client_data = Config::get("client_data");
                     $domain_name = $client_data->domain_name;
                     $razorpay_key = $client_data->gateway_key;
                     $razorpay_secret = $client_data->gateway_secret;
                }
                $razorpay = new Api($razorpay_key,$razorpay_secret);
                $payment = $razorpay->payment->fetch($razorpayPaymentId);
                $status = false;
                if ($payment['status'] === 'captured' || $payment['status'] === 'authorized'){
                    if($payment['status'] === 'authorized')
                        $payment->capture(array('amount'=>$payment['amount']));
                    $status = true;
                }
                if ($payment['status'] === 'failed'){
                    $status = false;
                }
                if($transaction->status=="pending"  && $transaction->module_table=='add_money'){
                    if($status==true){
                        $transaction->walletdata->increment('balance',$transaction->amount);
                        $transaction->status = 'success';
                        $transaction->transaction_id = $razorpayPaymentId;
                        $transaction->closing_balance = $transaction->walletdata->balance;
                        $transaction->save();
                        $notification = new Notification();
                        $notification->push_notification(array($transaction->walletdata->user_id),array(
                        'pushType'=>'BALANCE_ADDED',
                        'transaction_id'=>$transaction->transaction_id,
                        'message'=>__($transaction->amount." amount added into your wallet")));
                        return response(array(
                        'status' =>"error",
                        'statuscode' => 200,
                        'message' =>__($transaction->amount." amount added into your wallet")),200);
                    }else{
                        $transaction->status = 'failed';
                        $transaction->transaction_id = $razorpayPaymentId;
                        $transaction->save();

                        $notification = new Notification();
                        $notification->push_notification(array($transaction->walletdata->user_id),array(
                        'pushType'=>'BALANCE_FAILED',
                        'transaction_id'=>$transaction->transaction_id,
                        'message'=>__("Transaction Failed")));
                        return response(array(
                        'status' =>"error",
                        'statuscode' => 400,
                        'message' =>__('Transaction Failed')),400);
                    }
                }else{
                    return response(array(
                        'status' =>"error",
                        'statuscode' => 400,
                        'message' =>__('This Transaction Not Valid')),400);
                }
            }
            return response(array('status' => "error", 'statuscode' => 400, 'message' =>__($message)), 400);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }    


    /**
     * @SWG\Post(
     *     path="/purchase-package",
     *     description="purchase package physio",
     * tags={"Payment"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="package_id",
     *         in="query",
     *         type="string",
     *         description="package_id",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="card_id",
     *         in="query",
     *         type="string",
     *         description=" db card_id",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="order_id",
     *         in="query",
     *         type="string",
     *         description=" db order_id",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="razorpayPaymentId",
     *         in="query",
     *         type="string",
     *         description=" razorpayPaymentId",
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
    public static function postPurchasePackage(Request $request) {
        try{
            $user = Auth::user();
            // print_r($user);die;
            $requires_source_action = false;
            $rules = [
                'package_id'=>'required|exists:packages,id',
            ];
            if(isset($request->razorpayPaymentId) && isset($request->order_id)){
            }else{
                $rules['card_id'] =  'required|exists:cards,id';
            }
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            if(isset($request->order_id)){
                $transaction = Transaction::where('order_id',$request->order_id)->first();
                if(!$transaction){
                    return response(array(
                        'status' =>"error",
                        'statuscode' => 400,
                        'message' =>__('Transaction not valid')),400);
                }
                $transaction = Transaction::where('order_id',$request->order_id)->first();
                $razorpayPaymentId = 0;
                if(isset($request->razorpayPaymentId))
                    $razorpayPaymentId = $request->razorpayPaymentId;
                $razorpay_key = "";
                $razorpay_secret = "";
                if(Config::get("client_connected") && Config::get("client_data")){
                     $client_data = Config::get("client_data");
                     $domain_name = $client_data->domain_name;
                     $razorpay_key = $client_data->gateway_key;
                     $razorpay_secret = $client_data->gateway_secret;
                }
                $razorpay = new Api($razorpay_key,$razorpay_secret);
                $payment = $razorpay->payment->fetch($razorpayPaymentId);
                $status = false;
                if ($payment['status'] === 'captured' || $payment['status'] === 'authorized'){
                    if($payment['status'] === 'authorized')
                        $payment->capture(array('amount'=>$payment['amount']));
                    $status = true;
                }
                if ($payment['status'] === 'failed'){
                    $status = false;
                }
                if($transaction->status=="pending"  && $transaction->module_table=='packages'){
                    $package = Package::where('id',$request->package_id)->first();
                    if($status==true){
                        $userpackage  = UserPackage::firstOrCreate([
                            'user_id'=>$transaction->walletdata->user_id,
                            'package_id'=>$request->package_id
                        ]);
                        if($userpackage){
                            $userpackage->increment('available_requests',$package->total_requests);
                            $transaction->status = 'success';
                        }
                        $notification = new Notification();
                        $notification->push_notification(array($transaction->walletdata->user_id),array(
                            'pushType'=>'PACKAGE_PURCHASED',
                            'transaction_id'=>$transaction->transaction_id,
                            'package_name'=>$package->title,
                            'message'=>__("Package $package->title has been purchaged")));
                        $transaction->transaction_id = $razorpayPaymentId;
                        $transaction->save();
                        return response(array(
                        'status' =>"error",
                        'statuscode' => 200,
                        'message' =>__("Package $package->title has been purchaged")),200);
                    }else{
                        $transaction->status = 'failed';
                        $transaction->transaction_id = $razorpayPaymentId;
                        $transaction->save();
                        return response(array(
                        'status' =>"error",
                        'statuscode' => 400,
                        'message' =>__('Transaction Failed')),400);
                    }
                }else{
                    return response(array(
                        'status' =>"error",
                        'statuscode' => 400,
                        'message' =>__('This Transaction Not Valid')),400);
                }
            }else{
                $card = null;
                $card = Card::where(['id'=>$request->card_id,'user_id'=>$user->id])->first();
                $package = Package::where('id',$request->package_id)->first();
                $currency_code = 'INR';
                $currency = EnableService::where('type','currency')->first();
                if(isset($currency->value)){
                    $currency_code = $currency->value;
                }
                $stripe = new Stripe(env('STRIPE_TEST_KEY'));
                $transaction_type = 'purchase_package';
                if($card){
                    $message = 'Transaction failed';
                    $stripe = new Stripe(env('STRIPE_TEST_KEY'));
                    $paymentIntent = $stripe->paymentIntents()->create([
                        'amount' => $package->price,
                        'currency' => $currency_code,
                        'description'=>"consultant service",
                        'customer'=>$user->stripe_id,
                        'payment_method'=>$card->card_id,
                        'confirm'=>true,
                    ]);
                    $transaction = Transaction::create(array(
                        'amount'=>$package->price,
                        'transaction_type'=>$transaction_type,
                        'status'=>'pending',
                        'wallet_id'=>$user->wallet->id,
                        'closing_balance'=>$user->wallet->balance,
                    ));
                    if($paymentIntent['id']){
                        $transaction->transaction_id  = $paymentIntent['id'];
                        $transaction->payment_gateway  = 'stripe';
                        $transaction->module_table = 'packages';
                        $transaction->module_id = $request->package_id;
                        $transaction->save();
                    }
                    if($paymentIntent['status']=='succeeded'){
                        $url = null;
                        $payment = Payment::create(array('from'=>1,'to'=>$user->id,'transaction_id'=>$transaction->id));
                        return response(array(
                            'status' => "success",
                            'statuscode' => 200,
                            'data'=>['transaction_id'=>$transaction->transaction_id,
                            'requires_source_action'=>$requires_source_action,
                            'url'=>$url],
                            'message' =>__('Balance  Adding...')), 200);
                    }else if($paymentIntent['status']=='requires_source_action'){
                        $type = $paymentIntent['next_source_action']['type'];
                        $url = null;
                        if($type=='use_stripe_sdk'){
                            $url = $paymentIntent['next_source_action'][$type]['stripe_js'];
                        }
                        if($url){
                            $requires_source_action = true;
                        }
                        $payment = Payment::create(array('from'=>1,'to'=>$user->id,'transaction_id'=>$transaction->id));
                        return response(array(
                            'status' => "success",
                            'statuscode' => 200,
                            'data'=>['transaction_id'=>$transaction->transaction_id,
                            'requires_source_action'=>$requires_source_action,
                            'url'=>$url],
                            'message' =>__('Balance  Adding...')), 200);
                    }else{
                        $transaction->status = 'failed';
                        $transaction->save();
                        $payment = Payment::create(array('from'=>1,'to'=>$user->id,'transaction_id'=>$transaction->id));
                    }
                }else{
                    $message = 'Card Not Found';
                }
            }
            return response(array('status' => "error", 'statuscode' => 400, 'message' =>__($message)), 400);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }

    /**
     * @SWG\Post(
     *     path="/order/create",
     *     description="Create Order",
     * tags={"Payment"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="balance",
     *         in="query",
     *         type="string",
     *         description="Balance add",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="package_id",
     *         in="query",
     *         type="string",
     *         description="package_id",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="payment_method",
     *         in="query",
     *         type="string",
     *         description="payment_method for visa_master,mada",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="lat",
     *         in="query",
     *         type="string",
     *         description="lat",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="long",
     *         in="query",
     *         type="string",
     *         description="long",
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
    public function postAddOrder(Request $request) {
        try{
            $user = Auth::user();
            $input = $request->all();
            $rules = ['balance' => 'required'];
            if(isset($request->package_id)){
                $rules['package_id'] = 'required|exists:packages,id';
            }
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }       
            $message = 'Order Create Failed';
            $razorpay_key = "";
            $razorpay_secret = "";
            $payment_type = "";
            $client_data = null;
            $order = [];
            // print_r(Config::get("client_data"));die;
            if(Config::get("client_connected") && Config::get("client_data")){
                 $client_data = Config::get("client_data");
                 $domain_name = $client_data->domain_name;
                 $razorpay_key = $client_data->gateway_key;
                 $razorpay_secret = $client_data->gateway_secret;
                 $payment_type = $client_data->payment_type;
            }
            $package_id = 0;
            if(isset($request->package_id)){
                $package_id = $request->package_id;
            }
            // print_r($payment_type);;die;
            if($payment_type=='hyperpay'){
                $response =$this->createHyperPayCheckoutId($request,$client_data,$user);
                if($response['status']=='error'){
                    return response($response, 400);
                }
                $order = $response['response'];
            }elseif($payment_type=='paystack'){
                if(!$user->email){
                    return response(array('status' => "error", 'statuscode' => 400, 'message' =>__('Email required please update your profile')), 400);
                }
                $response =$this->createPayStackId($request,$client_data,$user);
                if($response['status']=='error'){
                    return response($response, 400);
                }
                $order = $response['response'];
            }else{
                $api = new Api($razorpay_key,$razorpay_secret);
                $order  = $api->order->create(array(
                    'receipt' =>'receipt_'.$user->id.'_'.time(),
                    'amount' => $request->balance,
                    'currency' => 'INR'));
            }
            $transaction_type = 'deposit';
            if($package_id){
                $transaction_type = 'purchase_package';
            }
            if($order['id']){
                $amount = $request->balance/100;
                $transaction = Transaction::create(array(
                    'amount'=>$amount,
                    'transaction_type'=>$transaction_type,
                    'status'=>'pending',
                    'wallet_id'=>$user->wallet->id,
                    'closing_balance'=>$user->wallet->balance,
                ));
                $transaction->payment_gateway = 'razorpay';
                if($payment_type){
                    $transaction->payment_gateway = $payment_type;
                }
                $transaction->order_id = $order['id'];
                $transaction->transaction_id = $order['id'];
                if($package_id){
                    $transaction->module_table = 'packages';
                }else{
                    $transaction->module_table = 'add_money';
                }
                if(isset($order['id'])){
                    $input['order_data'] = $order;
                }
                $transaction->raw_details = json_encode($input);
                $transaction->module_id = $package_id;
                $transaction->save();
                $payment = Payment::create(array('from'=>1,'to'=>$user->id,'transaction_id'=>$transaction->id));
                return response(array('status' => "success", 'statuscode' => 200, 'message' =>__('Order Created'),'data'=>['order_id'=>$order['id']]), 200);
            } 
            return response(array('status' => "error", 'statuscode' => 400, 'message' =>__($message)), 400);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }

    private function getCountryData($input,$user){
            $geolocation = $input['lat'].','.$input['long'];
            $request = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$geolocation.'&sensor=false&key=AIzaSyCWRLStt_5Kmgd877p8o_fPM2W5pdZ4WbU'; 
            $file_contents = file_get_contents($request);
            $json_decode = json_decode($file_contents);
            $response = array('testMode'=>'EXTERNAL','merchantTransactionId'=>time(),'customer.email'=>$user->email,'customer.givenName'=>$user->name,'customer.surname'=>$user->name);
            if(isset($json_decode->results[0])) {
                foreach($json_decode->results[0]->address_components as $addressComponet) {
                    if($addressComponet->types[0] == 'postal_code'){
                            $response['billing.postcode'] = $addressComponet->long_name;
                     }
                     if($addressComponet->types[0] == 'country'){
                            $response['billing.country'] = $addressComponet->short_name;
                     }
                     if($addressComponet->types[0] == 'locality'){
                            $response['billing.city'] = $addressComponet->long_name;
                     }
                     if($addressComponet->types[0] == 'administrative_area_level_1'){
                            $response['billing.state'] = $addressComponet->long_name;
                     }
                     if($addressComponet->types[0] == 'premise'){
                            $response['billing.street1'] = $addressComponet->long_name;
                     }
                }
        }
        return $response;
    }

    private function createPayStackId($request,$client_data,$user){
        try{
            $input = $request->all();
            $data_add = '';
            if(isset($input['lat'])  && isset($input['long'])){
                $data_add = $this->getCountryData($input,$user);
                $data_add = http_build_query($data_add);
            }
            // print_r($data_add);die;
            $currency_code = 'USD';
            $currency = EnableService::where('type','currency')->first();
            if(isset($currency->value)){
                $currency_code = $currency->value;
            }
            $paystack = new \Yabacon\Paystack($client_data->gateway_key);
            try{
              $tranx = $paystack->transaction->initialize([
                'amount'=>$input['balance']*100,       // in kobo
                'email'=>$user->email,         // unique to customers
                'reference'=>'ref_'.$user->id.'_'.time(), // unique to transactions
              ]);
              $tranx->data->id = $tranx->data->reference;
              return ['status'=>'success','statuscode' => 200,'message'=>'','response'=>(array) $tranx->data];
            } catch(\Yabacon\Paystack\Exception\ApiException $e){
              return ['status'=>'error','statuscode' => 400,'message'=>$e->getMessage()];
            }
        }catch(Exception $ex){
            return ['status'=>'error','statuscode' => 400,'message'=>$ex->getMessage()];
        }
    }

    private function createHyperPayCheckoutId($request,$client_data,$user){
        try{
            $input = $request->all();
            $data_add = '';
            if(isset($input['lat'])  && isset($input['long'])){
                $data_add = $this->getCountryData($input,$user);
                $data_add = http_build_query($data_add);
            }
            // print_r($data_add);die;
            $input['balance'] = $input['balance']/100;
            $method  = $client_data->entity_id_visa_master;
            $paymentType = "DB";
            if(isset($request->payment_method) && strtolower($request->payment_method)=='mada'){
                $method = $client_data->entity_id_mada;
                $paymentType = "PA";
            }
            $url = $client_data->payment_checkout_url;
            $data = "entityId=$method&amount=".$input['balance']."&currency=SAR&paymentType=$paymentType&notificationUrl=$client_data->payment_webhook_url&$data_add";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                           'Authorization:Bearer '.$client_data->payment_access_token));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $responseData = curl_exec($ch);
            if(curl_errno($ch)) {
                return ['status'=>'error','statuscode' => 400,'message'=>'Something Went Wrong Please Try Again'];
            }
            curl_close($ch);
            $res = json_decode($responseData,true);
            if($res['result']['code']!="000.200.100"){
                $message = $res['result']['description'];
                if(isset($res['result']['parameterErrors']) && isset($res['result']['parameterErrors'][0]['message'])){
                     $message = $res['result']['parameterErrors'][0]['message'];
                }
                return ['status'=>'error','statuscode' => 400,'message'=>$message];
            }
            return ['status'=>'success','statuscode' => 200,'message'=>'','response'=>$res];
        }catch(Exception $ex){
            return ['status'=>'error','statuscode' => 400,'message'=>$ex->getMessage()];
        }
    }

     /**
     * @SWG\Post(
     *     path="/complete-chat",
     *     description="Mark Complete",
     * tags={"Chat"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="request_id",
     *         in="query",
     *         type="string",
     *         description="request id",
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
    public static function postCompleteChat(Request $request) {
        try{
            $user = Auth::user();
            $rules = ['request_id' => 'required'];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $message = null;
            $service_id = \App\Model\Service::getServiceId('chat');
            $request_data = \App\Model\Request::where([
                'id'=>$request->request_id,
                'service_id'=>$service_id])
            ->first();
            if($request_data && $request_data->requesthistory->status=='in-progress'){
                $last_message = \App\Model\Message::getLastMessage($request_data);
                if($last_message){
                    $new_time = $last_message->created_at;
                    $old_time = $request_data->requesthistory->updated_at;
                    $diff_in_duration = $new_time->diffInSeconds($old_time);
                }else{
                    $new_time = Carbon::now();
                    $diff_in_duration = $new_time->diffInSeconds($request_data->requesthistory->updated_at);
                }
                $request_data->requesthistory->status = 'completed';
                $request_data->requesthistory->increment('duration',$diff_in_duration);
                $request_data->requesthistory->save();
                // $sr_service_charges = \App\Model\Subscription::where(['consultant_id'=>$request_data->sr_info->id,'service_id'=>$request_data->service_id])->first();
                // $charges_per_second = $sr_service_charges->charges/$sr_service_charges->duration;
                // $total_charges = $charges_per_second * $diff_in_duration;
                // $withdrawal_to = array(
                //     'balance'=>$total_charges,
                //     'user'=>$request_data->cus_info,
                //     'from_id'=>$request_data->sr_info->id,
                //     'request_id'=>$request_data->id,
                //     'status'=>'succeeded'
                // );
                // Transaction::createWithdrawal($withdrawal_to);
                // $deposit_to = array(
                //     'balance'=>$total_charges,
                //     'user'=>$request_data->sr_info,
                //     'from_id'=>$request_data->cus_info->id,
                //     'request_id'=>$request_data->id,
                //     'status'=>'succeeded'
                // );
                // Transaction::createDeposit($deposit_to);
                $deposit_to = array(
                        'user'=>$request_data->sr_info,
                        'from_id'=>$request_data->cus_info->id,
                        'request_id'=>$request_data->id,
                        'status'=>'succeeded'
                    );
                Transaction::updateDeposit($deposit_to);
                $notification = new Notification();
                $notification->sender_id = $request_data->to_user;
                $notification->receiver_id = $request_data->from_user;
                $notification->module_id = $request_data->id;
                $notification->module ='request';
                $notification->notification_type ='REQUEST_COMPLETED';
                $notification->message ="Your Request has been completed";
                $notification->save();

                $notification = new Notification();
                $notification->sender_id = $request_data->from_user;
                $notification->receiver_id = $request_data->to_user;
                $notification->module_id = $request_data->id;
                $notification->module ='request';
                $notification->notification_type ='REQUEST_COMPLETED';
                $notification->message ="Your Request has been completed";
                $notification->save();

                $notification->push_notification(array($request_data->to_user,$request_data->from_user),array('pushType'=>'REQUEST_COMPLETED',
                    'request_id'=>$request_data->id,
                    'message'=>__('Your Request has been completed')));
                return response(array('status' => "success", 'statuscode' => 200, 'message' =>__('Request Completed')), 200);
            }else{
                $message = 'In-Progress Chat Request Not Found';
            }
            return response(array('status' => "error", 'statuscode' => 400, 'message' =>__($message)), 400);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }

    /**
     * @SWG\Post(
     *     path="/enroll-user",
     *     description="Pay User for join class",
     * tags={"Classes"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="class_id",
     *         in="query",
     *         type="string",
     *         description="class id",
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
    public static function postPayEnroll(Request $request) {
        try{
            $user = Auth::user();
            $rules = ['class_id' => 'required|exists:ct_classes,id'];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $enrolled = EnrolledUser::where(['class_id'=>$request->class_id,'assinged_user'=>$user->id])->first();
            if($enrolled){
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>__('You are already enrolled to this class')), 400);
            }
            $message = null;
            $class = ConsultClass::where('id',$request->class_id)->first();
            if($class!=='completed'){
                if($class->enroll_users->count()==$class->limit_enroll){
                    $message = "Can't enroll class limit is full";
                }else if($user->wallet->balance < $class->price){
                    $message = "insufficient balance into your wallet please recharge";
                }else{
                    $enrolleduser = new EnrolledUser();
                    $enrolleduser->assinged_user = $user->id;
                    $enrolleduser->class_id = $class->id;
                    $enrolleduser->save();
                    $withdrawal_to = array(
                        'balance'=>$class->price,
                        'user'=>$user,
                        'from_id'=>$class->created_by,
                        'class_id'=>$class->id,
                        'status'=>'succeeded'
                    );
                    Transaction::createWithdrawal($withdrawal_to);

                    $deposit_to = array(
                        'balance'=>$class->price,
                        'user'=>$class->consultant,
                        'from_id'=>$user->id,
                        'class_id'=>$class->id,
                        'status'=>'succeeded'
                    );
                    Transaction::createDeposit($deposit_to);

                    $notification = new Notification();
                    $notification->sender_id = $user->id;
                    $notification->receiver_id = $class->created_by;
                    $notification->module_id = $class->id;
                    $notification->module ='class';
                    $notification->notification_type ='ASSINGED_USER';
                    $notification->message = __('notification.enrolled_class_text', ['user_name' => $user->name,'class_name'=>$class->name]);
                    $notification->save();

                    $notification->push_notification(array($class->created_by),array('pushType'=>'ASSINGED_USER','message'=>__('notification.enrolled_class_text', ['user_name' => $user->name,'class_name'=>$class->name])));
                    return response(array('status' => "success", 'statuscode' => 200, 'message' =>__('ASSINGED_USER')), 200);
                }
            }else{
                $message = 'Class has been completed';
            }
            return response(array('status' => "error", 'statuscode' => 400, 'message' =>__($message)), 400);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage().' '.$ex->getLine()], 500);
        }
    }


    /**
     * @SWG\Post(
     *     path="/add-bank",
     *     description="Add Bank Account",
     * tags={"Payment"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="country",
     *         in="query",
     *         type="string",
     *         description=" Country",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="currency",
     *         in="query",
     *         type="string",
     *         description="currency e.g inr",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="account_holder_name",
     *         in="query",
     *         type="string",
     *         description="The name of the person or business that owns the bank account",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="account_holder_type",
     *         in="query",
     *         type="string",
     *         description="The type of entity that holds the account. This can be either individual or company",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="ifc_code",
     *         in="query",
     *         type="string",
     *         description="The ifc_code, sort code, or other country-appropriate institution number for the bank account",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="account_number",
     *         in="query",
     *         type="string",
     *         description="The account number for the bank account, in string form. Must be a checking account",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="bank_name",
     *         in="query",
     *         type="string",
     *         description="Bank Name",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="institution_number",
     *         in="query",
     *         type="string",
     *         description="Institution Number",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="transit_number",
     *         in="query",
     *         type="string",
     *         description="Transit Number",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="customer_type",
     *         in="query",
     *         type="string",
     *         description="Independent Contractor or Temporary Agent",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="address",
     *         in="query",
     *         type="string",
     *         description="Address",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="city",
     *         in="query",
     *         type="string",
     *         description="City",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="province",
     *         in="query",
     *         type="string",
     *         description="Province",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="postal_code",
     *         in="query",
     *         type="string",
     *         description="Postal code",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="bank_id",
     *         in="query",
     *         type="string",
     *         description="Bank Name",
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

    public static function postAddBankAccount(Request $request) {
        try{
            $user = Auth::user();
            $rules = ['country' => 'required',
                     'currency'=>'required',
                     'account_holder_name'=>'required',
                     'account_holder_type'=>'required',
                     'ifc_code'=>'required',
                     'account_number'=>'required',
            ];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $message = 'something went wrong';
            $input = $request->all();
            if(isset($input['bank_id'])){
                $bankaccount = BankAccount::where(['user_id'=>$user->id,'id'=>$input['bank_id']])->first();
            }else{
                $bankaccount = BankAccount::where(['user_id'=>$user->id,'account_number'=>$request->account_number])->first();
                if($bankaccount){
                    return response(array('status' => "error", 'statuscode' => 400, 'message' =>__("Account already exist")), 400);
                }
                $bankaccount = new BankAccount();
            }
            $bankaccount->holder_name =  $request->account_holder_name;
            $bankaccount->account_number = $request->account_number;
            $bankaccount->ifc_code = $request->ifc_code;
            $bankaccount->account_type = $request->account_holder_type;
            $bankaccount->country = $request->country;
            $bankaccount->currency = $request->currency;
            $bankaccount->user_id = $user->id;
            if(isset($input['institution_number'])){
                $bankaccount->institution_number = $input['institution_number'];
            }
            if(isset($input['transit_number'])){
                $bankaccount->transit_number = $input['transit_number'];
            }

            if(isset($input['bank_name'])){
                $bankaccount->bank_name = $input['bank_name'];
            }
            if(isset($input['customer_type'])){
                $bankaccount->customer_type = $input['customer_type'];
            }
            if(isset($input['address'])){
                $bankaccount->address = $input['address'];
            }
            if(isset($input['city'])){
                $bankaccount->city = $input['city'];
            }
            if(isset($input['province'])){
                $bankaccount->province = $input['province'];
            }
            if(isset($input['postal_code'])){
                $bankaccount->postal_code = $input['postal_code'];
            }
            // $response = \Stripe\Account::createExternalAccount(
            //  $user->stripe_account_id,
            //  ['external_account' => array(
            //     'object'=>'bank_account',
            //     'country'=>$request->country,
            //     'currency'=>$request->currency,
            //     'account_holder_name'=>$request->account_holder_name,
            //     'account_holder_type'=>$request->account_holder_type,
            //     'routing_number'=>$request->ifc_code,
            //     'account_number'=>$request->account_number,
            //     )]
            // );
            if($bankaccount->save()){
                return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('Bank Added'), 'data' =>['bank_accounts'=>$user->getAttachedBanks($user)]], 200);
            }
            return response(array('status' => "error", 'statuscode' => 400, 'message' =>__($message)), 400);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }

    /**
     * @SWG\Post(
     *     path="/payouts",
     *     description="payouts",
     * tags={"Payment"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="bank_id",
     *         in="query",
     *         type="string",
     *         description=" Bank ID",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="amount",
     *         in="query",
     *         type="string",
     *         description="payout amount",
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

    public static function payoutWalletToBankAccount(Request $request) {
        try{
            $user = Auth::user();
            $rules = ['bank_id' => 'required|exists:bank_accounts,id',
                     'amount'=>'required|numeric|min:500',
            ];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $input = $request->all();
            if($user->wallet->balance < $input['amount']){
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>__("Keep a minimum amount ".$input['amount'])), 400);
            }
            $deposit_to = array(
                'balance'=>$input['amount'],
                'user'=>$user,
                'from_id'=>1,
            );
            $transaction = Transaction::createPayoutRequest($deposit_to); 
            $payoutrequest  = new PayoutRequest();
            $payoutrequest->transaction_id = $transaction->id;
            $payoutrequest->account_id = $input['bank_id'];
            $payoutrequest->vendor_id = $user->id;
            $payoutrequest->status = 'pending';
            if($payoutrequest->save()){
                return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('Payout Request Created'), 'data' =>(Object)[]], 200);
            }
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }

    // public static function payoutWalletToBankAccount(Request $request) {
    //     try{
    //         $apiContext = new \PayPal\Rest\ApiContext(
    //                 new \PayPal\Auth\OAuthTokenCredential(
    //                     'AZrVA8FYG6fQhKvnkcqQNFp5GtvzykwqX_mfD_BV-tzOQk6pI25QUDpgp4dJY-XQls7hCa_LGmblGoKW',     // ClientID
    //                     'EETlKdScsp7Q6M_hcd-AWfWtHlOXf8o03K2wxx1aiv-hGV4YiA02WuxlPq0cwm-v7xla8SD8LHl24BWG'      // ClientSecret
    //                 )
    //         );
    //         $payouts = new \PayPal\Api\Payout();
    //         $senderBatchHeader = new \PayPal\Api\PayoutSenderBatchHeader();
    //         $senderBatchHeader->setSenderBatchId(uniqid())->setEmailSubject("You have a payment");
    //         $senderItem1 = new \PayPal\Api\PayoutItem();
    //         $senderItem1->setRecipientType('EMAIL')
    //             ->setNote('Thanks you.')
    //             ->setReceiver('sb-bdedh1545452@personal.example.com')
    //             ->setSenderItemId("item_1" . uniqid())
    //             ->setAmount(new \PayPal\Api\Currency('{
    //                                 "value":"1",
    //                                 "currency":"INR"
    //                             }'));
    //         $payouts->setSenderBatchHeader($senderBatchHeader)->addItem($senderItem1);
    //         try {
    //             $output = $payouts->create(null, $apiContext);
    //             print_r($output);
    //         } catch (Exception $ex) {
    //             // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
    //             print_r($ex->getMessage());
    //             exit(1);
    //         }
    //         die;
    //         $user = Auth::user();
    //         $rules = ['amount' => 'required'];
    //         $validator = Validator::make($request->all(),$rules);
    //         if ($validator->fails()) {
    //             return response(array('status' => 'error', 'statuscode' => 400, 'message' =>
    //                 $validator->getMessageBag()->first()), 400);
    //         }
    //         $message = 'something went wrong';
    //         $input = $request->all();
    //         // print_r($request->ip());die;
    //         // $account = \Stripe\Account::update(
    //         //   'acct_1GaygKJOIeZgBsJf',
    //         //   [
    //         //     'tos_acceptance' => [
    //         //       'date' => time(),
    //         //       'ip' => $request->ip(), // Assumes you're not using a proxy
    //         //     ],
    //         //   ]
    //         // );
    //         // print_r($account);die;
    //         // $account = Card::select('id','card_id')->where(['id'=>$request->account_id,'user_id'=>$user->id,'card_type'=>'card_id'])->first();
    //         // if(!$account){
    //            // $response =  \Stripe\Payout::create([
    //            //        'amount' => 100,
    //            //        'currency' => 'inr',
    //            //        'destination' =>  'acct_1GaygKJOIeZgBsJf',
    //            //      ]);
    //         \Stripe\Stripe::setApiKey(env('STRIPE_TEST_KEY'));
    //                $response = \Stripe\Transfer::create([
    //                   'amount' => 400,
    //                   'currency' => 'inr',
    //                   'destination' => 'acct_1GaygKJOIeZgBsJf',
    //                   'description'=>"Transfer for test@example.com"
    //                 ]);
    //            print_r($response);die;
    //         // $paymentIntent = \Stripe\PaymentIntent::create([
    //         //       'amount' => 10000,
    //         //       'currency' => 'inr',
    //         //       'payment_method_types' => ['card'],
    //         //       'transfer_group' => '{ORDER10}',
    //         //     ]);
    //             // $response = \Stripe\Payout::create([
    //             //   'amount' => 100,
    //             //   'currency' => 'inr',
    //             //   'destination'=>$user->stripe_account_id
    //             // ]);
    //             // $response =  \Stripe\Transfer::create([
    //             //   'amount' => 400,
    //             //   'currency' => 'inr',
    //             //   'destination' => $user->stripe_account_id,
    //             // ]);
    //             print_r($response);die;
    //         // }else{
    //         //     $message = 'Account not found to this user';
    //         // }
    //         return response(array('status' => "error", 'statuscode' => 400, 'message' =>__($message)), 400);
    //     }catch(Exception $ex){
    //         return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
    //     }
    // }

}
