<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Exception;
use App\Model\Transaction;
use App\Model\UserPackage;
use App\Model\Package;
use App\Notification;
use Illuminate\Support\Str;
use Config;
use App\Helpers\Helper;
class WebHookController extends Controller
{
	protected $razorpay;
    protected $razorpay_key;
    protected $razorpay_secret;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->razorpay_key = "";
        $this->razorpay_secret = "";
        // $this->razorpay = new Api($this->razorpay_key,$this->razorpay_secret);
    }

    private function validateSignature(Request $request)
    {
        $webhookSecret = $this->razorpay_secret;
        $webhookSignature = $request->header('X-Razorpay-Signature');
        $payload = $request->getContent();
        $this->razorpay->utility->verifyWebhookSignature($payload, $webhookSignature, $webhookSecret);
    }

    public function getHandlePayStackWebhook(Request $request){}

	public function getHandleRazorPayWebhook(Request $request){

  }

    public function getHandleStripeWebhook(Request $request){
        $domain_name = 'default';
        $endpoint_secret = env('STRIPE_SECRET');
        if(Config::get("client_connected") && Config::get("client_data")){
             $keys = Helper::getClientFeatureKeys('Payment Gateway','Stripe');
             if(isset($keys['STRIPE_MODE']) && $keys['STRIPE_MODE']=='test'){
                $endpoint_secret = $keys['STRIPE_TEST_SIGNING_SECRET'];
             }elseif (isset($keys['STRIPE_MODE']) && $keys['STRIPE_MODE']=='live') {
                $endpoint_secret = $keys['STRIPE_LIVE_SIGNING_SECRET'];
             }
        }
        if(!isset($_SERVER['HTTP_STRIPE_SIGNATURE'])){
            http_response_code(400);
            exit();
        }

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
            // print_r($event);die;
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        if ($event->type == "payment_intent.succeeded") {
            $intent = $event->data->object;
            \Log::info("payment_intent.succeeded",["intent"=>$intent]);
            $transaction = Transaction::where([
                'transaction_id'=>$intent->id,
                'payment_gateway'=>'stripe']
            )->first();
            // print_r($transaction);die;
            if($transaction){
                if($transaction->status=="pending"  && $transaction->module_table=='packages'){
                    $userpackage  = UserPackage::firstOrCreate([
                        'user_id'=>$transaction->walletdata->user_id,
                        'package_id'=>$transaction->module_id
                    ]);
                    if($userpackage){
                        $package = Package::where('id',$transaction->module_id)->first();
                        $userpackage->increment('available_requests',$package->total_requests);
                        $transaction->status = 'success';
                        $transaction->save();
                    }
                    http_response_code(200);
                    exit();
                }

                if($transaction->status=="pending"  && $transaction->module_table=='request_creation'){
                    $response =  Helper::createRequest($transaction->raw_details,$transaction->id);
                    \Log::info("------------------payment_intent.request_creation-----------",["response"=>$response]);
                    http_response_code(200);
                    exit();
                }
                if($transaction->status=="success"){
                    http_response_code(200);
                    exit();
                }
                $transaction->walletdata->increment('balance',$transaction->amount);
                // if($transaction->module_table=='direct'){

                // }else{
                // }
                $transaction->status = 'success';
                $transaction->save();

                $notification = new Notification();
                $notification->push_notification(array($transaction->walletdata->user_id),array(
                    'pushType'=>'BALANCE_ADDED',
                    'transaction_id'=>$transaction->transaction_id,
                    'message'=>__($transaction->amount." amount added into your wallet")));
                \Log::info("payment_intent.succeeded.notification",["notification"=>$notification]);
            }
            http_response_code(200);
            exit();
        } elseif ($event->type == "payment_intent.payment_failed") {
            $intent = $event->data->object;
            \Log::info("payment_intent.payment_failed",["intent"=>$intent]);
            $transaction = Transaction::where([
                'transaction_id'=>$intent->id,
                'payment_gateway'=>'stripe']
            )->first();
            if($transaction){
                $transaction->status = 'failed';
                $transaction->save();

                $notification = new Notification();
                $notification->push_notification(array($transaction->walletdata->user_id),array(
                    'pushType'=>'BALANCE_FAILED',
                    'transaction_id'=>$transaction->transaction_id,
                    'message'=>__("Transaction Failed")));
            }
            http_response_code(200);
            exit();
        }
    }

    public function getHandleAlRajhiWebhook(Request $request){}

    public function curlForHyper(){}

    public function getHyperWebhook(Request $request){}

	/**
     * Handle a handlePayment Authorized.
     *
     * @param array $payload
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handlePaymentAuthorized(array $payload)
    {

        if(isset($payload['payload']['payment']['entity'])){
        	$razorpayPaymentId = $payload['payload']['payment']['entity']['id'];
        	$payment = $this->getPaymentEntity($razorpayPaymentId, $payload);
        	$success = false;
	        $errorMessage = 'The payment has failed.';
	        if ($payment['status'] === 'captured')
	        {
	            $success = true;
	        }else if ($payment['status'] === 'authorized'){
	        	$amount = $payment['amount']/100;
	        	$payment->capture(array('amount'=>$payment['amount']));
	        	$success = true;
	        	$transaction = Transaction::where('order_id',$payload['payload']['payment']['entity']['order_id'])->first();
	        	if($transaction && $transaction->status=="pending"){
		        	$transaction->amount = $amount;
		        	$transaction->walletdata->increment('balance',$amount);
		        	$transaction->status = 'success';
		        	$transaction->transaction_id = $razorpayPaymentId;
			        $transaction->closing_balance = $transaction->walletdata->balance;
			        $transaction->save();

                    $notification = new Notification();
                    $notification->push_notification(array($transaction->walletdata->user_id),array(
                    'pushType'=>'BALANCE_ADDED',
                    'transaction_id'=>$transaction->transaction_id,
                    'message'=>__($transaction->amount." amount added into your wallet")));
	        	}
	        }
	        return response(array('status' => "success", 'statuscode' => 200, 'message' =>__('handlePaymentAuthorized'),'data'=>['razorpayPaymentId'=>$razorpayPaymentId]), 200);
        }
    }

    /**
     * Handle a handlePayment Failed.
     *
     * @param array $payload
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handlePaymentFailed(array $payload)
    {

        if(isset($payload['payload']['payment']['entity'])){
        	$razorpayPaymentId = $payload['payload']['payment']['entity']['id'];
        	$payment = $this->getPaymentEntity($razorpayPaymentId, $payload);
        	$success = false;
	        $errorMessage = 'The payment has failed.';
	        if ($payment['status'] === 'captured')
	        {
	            $success = true;
	        }else if ($payment['status'] === 'failed'){
	        	$transaction = Transaction::where('order_id',$payload['payload']['payment']['entity']['order_id'])->first();
	        	if($transaction && $transaction->status=="pending"){
		        	$transaction->status = 'failed';
		        	$transaction->transaction_id = $razorpayPaymentId;
			        $transaction->save();

                    $notification = new Notification();
                    $notification->push_notification(array($transaction->walletdata->user_id),array(
                    'pushType'=>'BALANCE_FAILED',
                    'transaction_id'=>$transaction->transaction_id,
                    'message'=>__("Transaction Failed")));
	        	}
	        }
	        return response(array('status' => "success", 'statuscode' => 200, 'message' =>__('handlePaymentFailed'),'data'=>['razorpayPaymentId'=>$razorpayPaymentId]), 200);
        }
    }

    protected function getPaymentEntity($razorpayPaymentId, $data)
    {
       $payment = $this->razorpay->payment->fetch($razorpayPaymentId);
       return $payment;
    }

    /**
     * Returns the order amount, rounded as integer
     * @param WC_Order $order WooCommerce Order instance
     * @return int Order Amount
     */
    public function getOrderAmountAsInteger($order)
    {
        
        return (int) round($order->order_total * 100);
    }

	/**
     * Handle calls to missing methods on the controller.
     *
     * @param array $parameters
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function missingMethod($parameters = [])
    {
    	$log = array(
                'message'   => 'missingMethod',
                'data'      => 'Razor Pay',
            );
         \Log::info('Razor Pay '.json_encode($log));
        return response();
    }
}
