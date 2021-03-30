<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;
use App\Notification;
use App\User;
use App\Model\PayoutRequest;
use Cartalyst\Stripe\Stripe;
use App\Helpers\Helper;
use Config;
use Twilio\Rest\Client;
class Transaction extends Model
{
    //
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'amount', 'transaction_type', 'status','wallet_id','closing_balance','request_id','class_id','transaction_id','payment_gateway'
    ];

    public function getClosingBalanceAttribute($value) {
        return round($value, 2);
    }
    public function getAmountAttribute($value) {
        return round($value, 2);
    }
    public static function createWithdrawal($transaction_detail){
    	$transaction = self::create(array(
				'amount'=>$transaction_detail['balance'],
				'transaction_type'=>'withdrawal',
				'status'=>'pending',
				'wallet_id'=>$transaction_detail['user']->wallet->id,
                'closing_balance'=>$transaction_detail['user']->wallet->balance,
                'request_id'=>(isset($transaction_detail['request_id'])?$transaction_detail['request_id']:null),
                'class_id'=>(isset($transaction_detail['class_id'])?$transaction_detail['class_id']:null),
			));
		if($transaction_detail['status']=='succeeded'){
			$transaction->status = 'success';
			$transaction->save();
            if(isset($transaction_detail['category_payment']) && $transaction_detail['category_payment']=='cash'){
                $transaction->payment_type = 'cash';
            }else{
			 $transaction_detail['user']->wallet->decrement('balance',$transaction_detail['balance']);
            }
            $transaction->closing_balance = $transaction_detail['user']->wallet->balance;
            $transaction->save();
			$payment = \App\Model\Payment::create(array('from'=>$transaction_detail['from_id'],'to'=>$transaction_detail['user']->id,'transaction_id'=>$transaction->id));
            $sent_to = User::find($transaction_detail['from_id']);
            $notification = new Notification();
            $notification->sender_id = $sent_to->id;
            $notification->receiver_id = $transaction_detail['user']->id;
            $notification->module_id = $payment->id;
            $notification->module ='payment';
            $notification->notification_type ='BOOKING_RESERVED';
            $notification->message =__('notification.booking_amount_de_text', ['amount' => $transaction->amount]);
            $notification->save();
            $notification->push_notification(array($transaction_detail['user']->id),array('pushType'=>'BOOKING_RESERVED','message'=>__('notification.booking_amount_de_text', ['amount' => $transaction->amount])));
		}elseif($transaction_detail['status']=='payout-pending'){
            $transaction->status = 'inprogress';
            $transaction->save();
            $payment = \App\Model\Payment::create(array('from'=>$transaction_detail['from_id'],'to'=>$transaction_detail['user']->id,'transaction_id'=>$transaction->id));
        }elseif($transaction_detail['status']=='user-pending'){
            $transaction->status = 'user-pending';
            $transaction->save();
            $payment = \App\Model\Payment::create(array('from'=>$transaction_detail['from_id'],'to'=>$transaction_detail['user']->id,'transaction_id'=>$transaction->id));
        }else{
			$transaction->status = 'failed';
            $transaction->save();
            $payment = \App\Model\Payment::create(array('from'=>$transaction_detail['from_id'],'to'=>$user->id,'transaction_id'=>$transaction->id));
		}
		return $transaction;
    }

    public static function createWithdrawalExtraPayment($transaction_detail){
        $transaction = self::create(array(
                'amount'=>$transaction_detail['balance'],
                'transaction_type'=>'withdrawal',
                'status'=>'pending',
                'wallet_id'=>$transaction_detail['user']->wallet->id,
                'closing_balance'=>$transaction_detail['user']->wallet->balance,
                'request_id'=>(isset($transaction_detail['request_id'])?$transaction_detail['request_id']:null),
                'class_id'=>(isset($transaction_detail['class_id'])?$transaction_detail['class_id']:null),
            ));
        if($transaction_detail['status']=='succeeded'){
            $transaction->status = 'success';
            $transaction->module_table = isset($transaction_detail['module_table'])?$transaction_detail['module_table']:null;
            $transaction->module_id = isset($transaction_detail['module_id'])?$transaction_detail['module_id']:null;
            $transaction->save();
            $transaction_detail['user']->wallet->decrement('balance',$transaction_detail['balance']);
            $transaction->closing_balance = $transaction_detail['user']->wallet->balance;
            $transaction->save();
            $payment = \App\Model\Payment::create(array('from'=>$transaction_detail['from_id'],'to'=>$transaction_detail['user']->id,'transaction_id'=>$transaction->id));
            $sent_to = User::find($transaction_detail['from_id']);
            $received_from = User::find($transaction_detail['from_id']);
            $notification = new Notification();
            $notification->sender_id = $sent_to->id;
            $notification->receiver_id = $transaction_detail['user']->id;
            $notification->module_id = $payment->id;
            $notification->module ='payment';
            $notification->notification_type ='BALANCE_DEDUCTED';
            $notification->message =__("$transaction->amount transferred to $sent_to->name for extra payment");
            $notification->save();
            $notification->push_notification(array($transaction_detail['user']->id),array('pushType'=>'BALANCE_DEDUCTED','message'=>__("$transaction->amount transferred to $sent_to->name for extra payment")));
            return $transaction;
        }
    }


    public static function createWithdrawalNew($transaction_detail){
        $transaction = self::create(array(
                'amount'=>$transaction_detail['balance'],
                'transaction_type'=>$transaction_detail['transaction_type'],
                'status'=>'pending',
                'wallet_id'=>$transaction_detail['user']->wallet->id,
                'closing_balance'=>$transaction_detail['user']->wallet->balance,
                'request_id'=>(isset($transaction_detail['request_id'])?$transaction_detail['request_id']:null),
                'class_id'=>(isset($transaction_detail['class_id'])?$transaction_detail['class_id']:null),
            ));
        if($transaction_detail['status']=='succeeded'){
            $transaction->status = 'success';
            $transaction->module_table = isset($transaction_detail['module_table'])?$transaction_detail['module_table']:null;
            $transaction->module_id = isset($transaction_detail['module_id'])?$transaction_detail['module_id']:null;
            $transaction->save();
            $transaction_detail['user']->wallet->decrement('balance',$transaction_detail['balance']);
            $transaction->closing_balance = $transaction_detail['user']->wallet->balance;
            $transaction->save();
            $payment = \App\Model\Payment::create(array('from'=>$transaction_detail['from_id'],'to'=>$transaction_detail['user']->id,'transaction_id'=>$transaction->id));
            $sent_to = User::find($transaction_detail['from_id']);
            $notification = new Notification();
            $notification->sender_id = $sent_to->id;
            $notification->receiver_id = $transaction_detail['user']->id;
            $notification->module_id = $payment->id;
            $notification->module ='payment';
            $notification->notification_type ='ASKED_QUESTION';
            $notification->message =__('notification.asking_question_de_text', ['amount' => $transaction->amount]);
            $notification->save();
            $notification->push_notification(array($transaction_detail['user']->id),array('pushType'=>'ASKED_QUESTION','message'=>__('notification.asking_question_de_text', ['amount' => $transaction->amount])));
        }
        return $transaction;
    }    

    public static function createWithdrawalFromSP($transaction_detail){
        $transaction = self::create(array(
                'amount'=>$transaction_detail['balance'],
                'transaction_type'=>'withdrawal',
                'status'=>'pending',
                'wallet_id'=>$transaction_detail['sp']->wallet->id,
                'closing_balance'=>$transaction_detail['sp']->wallet->balance,
                'request_id'=>(isset($transaction_detail['request_id'])?$transaction_detail['request_id']:null),
                'class_id'=>(isset($transaction_detail['class_id'])?$transaction_detail['class_id']:null),
            ));
        if($transaction_detail['status']=='succeeded'){
            $transaction->status = 'success';
            $transaction->save();
            $transaction_detail['sp']->wallet->decrement('balance',$transaction_detail['balance']);
            $transaction->closing_balance = $transaction_detail['sp']->wallet->balance;
            $transaction->save();
            $payment = \App\Model\Payment::create(array('from'=>$transaction_detail['from_id'],'to'=>$transaction_detail['sp']->id,'transaction_id'=>$transaction->id));
            $sent_to = User::find($transaction_detail['from_id']);
            $notification = new Notification();
            $notification->sender_id = $sent_to->id;
            $notification->receiver_id = $transaction_detail['sp']->id;
            $notification->module_id = $payment->id;
            $notification->module ='payment';
            $notification->notification_type ='BOOKING_RESERVED';
            $notification->message =__('notification.booking_amount_de_text', ['amount' => $transaction->amount]);
            $notification->save();
            $notification->push_notification(array($transaction_detail['sp']->id),array('pushType'=>'BOOKING_RESERVED','message'=>__('notification.booking_amount_de_text', ['amount' => $transaction->amount])));
        }elseif($transaction_detail['status']=='payout-pending'){
            $transaction->status = 'inprogress';
            $transaction->save();
            $payment = \App\Model\Payment::create(array('from'=>$transaction_detail['from_id'],'to'=>$transaction_detail['sp']->id,'transaction_id'=>$transaction->id));
        }else{
            $transaction->status = 'failed';
            $transaction->save();
            $payment = \App\Model\Payment::create(array('from'=>$transaction_detail['from_id'],'to'=>$transaction_detail['sp']->id,'transaction_id'=>$transaction->id));
        }
        return $transaction;
    }

    public static function createPayoutRequest($transaction_detail){
        $transaction = self::create(array(
                'amount'=>$transaction_detail['balance'],
                'transaction_type'=>'payouts',
                'status'=>'pending',
                'wallet_id'=>$transaction_detail['user']->wallet->id,
                'closing_balance'=>$transaction_detail['user']->wallet->balance,
            ));
        $transaction->save();
        $transaction_detail['user']->wallet->decrement('balance',$transaction_detail['balance']);
        $transaction->closing_balance = $transaction_detail['user']->wallet->balance;
        $transaction->save();
        $payment = \App\Model\Payment::create(array('from'=>$transaction_detail['from_id'],'to'=>$transaction_detail['user']->id,'transaction_id'=>$transaction->id));
        return $transaction;
    }

    public static function createDeposit($transaction_detail){
        $transaction_id = isset($transaction_detail['transaction_id'])?$transaction_detail['transaction_id']:null;
        $request_id = isset($transaction_detail['request_id'])?$transaction_detail['request_id']:null;
    	$transaction = self::create(array(
				'amount'=>$transaction_detail['balance'],
				'transaction_type'=>'deposit',
				'status'=>'pending',
				'wallet_id'=>$transaction_detail['user']->wallet->id,
                'closing_balance'=>$transaction_detail['user']->wallet->balance,
                'request_id'=>(isset($transaction_detail['request_id'])?$transaction_detail['request_id']:null),
                'class_id'=>(isset($transaction_detail['class_id'])?$transaction_detail['class_id']:null),
			));
		if($transaction_detail['status']=='succeeded'){
			$transaction->status = 'success';
            // $transaction->module_table = isset($transaction_detail['module_table'])?$transaction_detail['module_table']:null;
            // $transaction->module_id = isset($transaction_detail['module_id'])?$transaction_detail['module_id']:null;
			$transaction->save();
			$transaction_detail['user']->wallet->increment('balance',$transaction_detail['balance']);
            $transaction->closing_balance = $transaction_detail['user']->wallet->balance;
            $transaction->save();
			$payment = \App\Model\Payment::create(array('from'=>$transaction_detail['from_id'],'to'=>$transaction_detail['user']->id,'transaction_id'=>$transaction->id));
            $received_from = User::find($transaction_detail['from_id']);
            $notification = new Notification();
            $notification->sender_id = $transaction_detail['from_id'];
            $notification->receiver_id = $transaction_detail['user']->id;
            $notification->module_id = $payment->id;
            $notification->module ='payment';
            $notification->notification_type ='AMOUNT_RECEIVED';
            $notification->message =__('notification.booking_amount_re_text', ['amount' => $transaction->amount,'user_name'=>$received_from->name]);
            $notification->save();
            $notification->push_notification(array($transaction_detail['user']->id),array(
                'pushType'=>'AMOUNT_RECEIVED',
                'request_id'=>$request_id,
                'transaction_id'=>$transaction_id,
                'message'=>__('notification.booking_amount_re_text', ['amount' => $transaction->amount,'user_name'=>$received_from->name])
            ));
		}elseif($transaction_detail['status']=='vendor-pending'){
            $transaction->status = 'pending';
            $transaction->save();
        }else{
			$transaction->status = 'failed';
            $transaction->save();
            $payment = \App\Model\Payment::create(array('from'=>$transaction_detail['from_id'],'to'=>$transaction_detail['user']->id,'transaction_id'=>$transaction->id));
		}
		return $transaction;
    }

    public static function createDepositExtraPayment($transaction_detail){
        $transaction_id = isset($transaction_detail['transaction_id'])?$transaction_detail['transaction_id']:null;
        $request_id = isset($transaction_detail['request_id'])?$transaction_detail['request_id']:null;
        $transaction = self::create(array(
                'amount'=>$transaction_detail['balance'],
                'transaction_type'=>'deposit',
                'status'=>'pending',
                'wallet_id'=>$transaction_detail['user']->wallet->id,
                'closing_balance'=>$transaction_detail['user']->wallet->balance,
                'request_id'=>(isset($transaction_detail['request_id'])?$transaction_detail['request_id']:null),
                'class_id'=>(isset($transaction_detail['class_id'])?$transaction_detail['class_id']:null),
            ));
        if($transaction_detail['status']=='succeeded'){
            $transaction->status = 'success';
            $transaction->module_table = isset($transaction_detail['module_table'])?$transaction_detail['module_table']:null;
            $transaction->module_id = isset($transaction_detail['module_id'])?$transaction_detail['module_id']:null;
            $transaction->save();
            $transaction_detail['user']->wallet->increment('balance',$transaction_detail['balance']);
            $transaction->closing_balance = $transaction_detail['user']->wallet->balance;
            $transaction->save();
            $payment = \App\Model\Payment::create(array('from'=>$transaction_detail['from_id'],'to'=>$transaction_detail['user']->id,'transaction_id'=>$transaction->id));
            $received_from = User::find($transaction_detail['from_id']);
            $notification = new Notification();
            $notification->sender_id = $transaction_detail['from_id'];
            $notification->receiver_id = $transaction_detail['user']->id;
            $notification->module_id = $payment->id;
            $notification->module ='payment';
            $notification->notification_type ='PAID_EXTRA_PAYMENT';
            $notification->message =__("$transaction->amount Extra amount received from $received_from->name.");
            $notification->save();
            $notification->push_notification(array($transaction_detail['user']->id),array(
                'pushType'=>'PAID_EXTRA_PAYMENT',
                'request_id'=>$request_id,
                'transaction_id'=>$transaction_id,
                'message'=>__("$transaction->amount Extra amount received from $received_from->name.")
            ));
        }elseif($transaction_detail['status']=='vendor-pending'){
            $transaction->status = 'pending';
            $transaction->save();
        }else{
            $transaction->status = 'failed';
            $transaction->save();
            $payment = \App\Model\Payment::create(array('from'=>$transaction_detail['from_id'],'to'=>$transaction_detail['user']->id,'transaction_id'=>$transaction->id));
        }
        return $transaction;
    }

    public static function createRefund($transaction_detail){
        $transaction = self::create(array(
                'amount'=>$transaction_detail['balance'],
                'transaction_type'=>'refund',
                'status'=>'pending',
                'wallet_id'=>$transaction_detail['user']->wallet->id,
                'closing_balance'=>$transaction_detail['user']->wallet->balance,
                'request_id'=>(isset($transaction_detail['request_id'])?$transaction_detail['request_id']:null),
                'class_id'=>(isset($transaction_detail['class_id'])?$transaction_detail['class_id']:null),
            ));
        if($transaction_detail['status']=='succeeded'){
            $transaction->status = 'success';
            $transaction->save();
            $transaction_detail['user']->wallet->increment('balance',$transaction_detail['balance']);
            $transaction->closing_balance = $transaction_detail['user']->wallet->balance;
            $transaction->save();
            $payment = \App\Model\Payment::create(array('from'=>$transaction_detail['from_id'],'to'=>$transaction_detail['user']->id,'transaction_id'=>$transaction->id));
            $received_from = User::find($transaction_detail['from_id']);
            $notification = new Notification();
            $notification->sender_id = $transaction_detail['from_id'];
            $notification->receiver_id = $transaction_detail['user']->id;
            $notification->module_id = $payment->id;
            $notification->module ='payment';
            $notification->notification_type ='AMOUNT_RECEIVED';
            $notification->message =__('notification.booking_amount_re_text', ['amount' => $transaction->amount,'user_name'=>$received_from->name]);
            $notification->save();
            $notification->push_notification(array($transaction_detail['user']->id),array('pushType'=>'AMOUNT_RECEIVED','message'=>__('notification.booking_amount_re_text', ['amount' => $transaction->amount,'user_name'=>$received_from->name])));
        }elseif($transaction_detail['status']=='vendor-pending'){
            $transaction->status = 'pending';
            $transaction->save();
        }else{
            $transaction->status = 'failed';
            $transaction->save();
            $payment = \App\Model\Payment::create(array('from'=>$transaction_detail['from_id'],'to'=>$transaction_detail['user']->id,'transaction_id'=>$transaction->id));
        }
        return $transaction;
    }


    public static function createRefundForStripe($request_data,$customer=false,$charges_full=true,$per_hour){
        // $deposit_to = array(
        //     'balance'=>$request_data->requesthistory->total_charges,
        //     'user'=>$request_data->cus_info,
        //     'from_id'=>$request_data->sr_info->id,
        //     'request_id'=>$request_data->id,
        //     'status'=>'succeeded'
        // );
        $payment = $request_data->requesthistory->total_charges;
        if(!$charges_full){
            $payment = $request_data->requesthistory->total_charges - ($per_hour * 4);
            if($payment<=0){
                $payment = $request_data->requesthistory->total_charges - $per_hour;
            }
        }
        $transaction = self::where([
            'request_id'=>$request_data->id,
            'module_table'=>'request_creation',
            'wallet_id'=>$request_data->cus_info->wallet->id
        ])->where('transaction_id','!=',null)->first();
        $res = null;
        $refund_id = null;
        if($transaction){
            try{
                $key = env('STRIPE_TEST_KEY');
                $keys = Helper::getClientFeatureKeys('Payment Gateway','Stripe');
                 if(isset($keys['STRIPE_MODE']) && $keys['STRIPE_MODE']=='test'){
                    $key = $keys['STRIPE_TEST_KEY'];
                 }elseif (isset($keys['STRIPE_MODE']) && $keys['STRIPE_MODE']=='live') {
                    $key = $keys['STRIPE_LIVE_KEY'];
                }
                \Stripe\Stripe::setApiKey($key);
                $data = ['payment_intent' => $transaction->transaction_id];
                if(!$charges_full){
                    $data['amount'] = $payment*100;
                }
                $res = \Stripe\Refund::create($data);
            }catch(Exception $ex){

            }
        }
        if($res){
            $refund_id = $res['id'];
        }
        $transaction = self::create(array(
                'amount'=>$payment,
                'transaction_type'=>'refund',
                'status'=>'success',
                'wallet_id'=>$request_data->cus_info->wallet->id,
                'closing_balance'=>$request_data->cus_info->wallet->balance,
                'request_id'=>$request_data->id,
                'transaction_id'=>$refund_id,
        ));
        
        $payment_data = \App\Model\Payment::create(
            array(
            'from'=>$request_data->sr_info->id,
            'to'=>$request_data->cus_info->id,
            'transaction_id'=>$transaction->id
        ));
        $received_from = User::find($request_data->sr_info->id);
        $notification = new Notification();
        $notification->sender_id = $request_data->sr_info->id;
        $notification->receiver_id = $request_data->cus_info->id;
        $notification->module_id = $payment_data->id;
        $notification->module ='payment';
        $notification->notification_type ='AMOUNT_RECEIVED';
        $notification->message =__('notification.booking_amount_refund_text', ['amount' => $payment,'user_name'=>$received_from->name]);
        $notification->save();
        $notification->push_notification(array($request_data->cus_info->id),array('pushType'=>'AMOUNT_RECEIVED','message'=>__('notification.booking_amount_refund_text', ['amount' => $payment,'user_name'=>$received_from->name])));
        return $transaction;
    }

    public static function createRefundForSP($transaction_detail){
        $transaction = self::create(array(
                'amount'=>$transaction_detail['balance'],
                'transaction_type'=>'refund',
                'status'=>'pending',
                'wallet_id'=>$transaction_detail['sp']->wallet->id,
                'closing_balance'=>$transaction_detail['sp']->wallet->balance,
                'request_id'=>(isset($transaction_detail['request_id'])?$transaction_detail['request_id']:null),
                'class_id'=>(isset($transaction_detail['class_id'])?$transaction_detail['class_id']:null),
            ));
        if($transaction_detail['status']=='succeeded'){
            $transaction->status = 'success';
            $transaction->save();
            $transaction_detail['sp']->wallet->increment('balance',$transaction_detail['balance']);
            $transaction->closing_balance = $transaction_detail['sp']->wallet->balance;
            $transaction->save();
            $payment = \App\Model\Payment::create(array('from'=>$transaction_detail['from_id'],'to'=>$transaction_detail['sp']->id,'transaction_id'=>$transaction->id));
            $received_from = User::find($transaction_detail['from_id']);
            $notification = new Notification();
            $notification->sender_id = $transaction_detail['from_id'];
            $notification->receiver_id = $transaction_detail['sp']->id;
            $notification->module_id = $payment->id;
            $notification->module ='payment';
            $notification->notification_type ='AMOUNT_RECEIVED';
            $notification->message =__('notification.booking_amount_re_text', ['amount' => $transaction->amount,'user_name'=>$received_from->name]);
            $notification->save();
            $notification->push_notification(array($transaction_detail['sp']->id),array('pushType'=>'AMOUNT_RECEIVED',
                'message'=>__('notification.booking_amount_re_text', ['amount' => $transaction->amount,'user_name'=>$received_from->name])));
        }elseif($transaction_detail['status']=='vendor-pending'){
            $transaction->status = 'pending';
            $transaction->save();
        }else{
            $transaction->status = 'failed';
            $transaction->save();
            $payment = \App\Model\Payment::create(array('from'=>$transaction_detail['from_id'],'to'=>$transaction_detail['sp']->id,'transaction_id'=>$transaction->id));
        }
        return $transaction;
    }


    public static function updateDeposit($transaction_detail){
        $transactions = self::where(array(
                'status'=>'pending',
                'wallet_id'=>$transaction_detail['user']->wallet->id,
                'request_id'=>$transaction_detail['request_id']
            ))->get();
        if(count($transactions)<1){
            return;
        }
        $ignore_notification = false;
        if(\Config::get('client_connected') && (\Config::get('client_data')->domain_name=='intely')){
            $ignore_notification = true;
        }
        if($transaction_detail['status']=='succeeded'){
            $received_from = User::find($transaction_detail['from_id']);
            $amount = 0;
            foreach ($transactions as $key => $transaction) {
                $amount = $amount + $transaction->amount;
                $transaction->status = 'success';
                $transaction_detail['user']->wallet->increment('balance',$transaction->amount);
                $transaction->save();
                $payment = \App\Model\Payment::firstOrCreate(array('from'=>$transaction_detail['from_id'],'to'=>$transaction_detail['user']->id,'transaction_id'=>$transaction->id));
                if(!$ignore_notification){
                    $notification = new Notification();
                    $notification->sender_id = $transaction_detail['from_id'];
                    $notification->receiver_id = $transaction_detail['user']->id;
                    $notification->module_id = $payment->id;
                    $notification->module ='payment';
                    $notification->notification_type ='AMOUNT_RECEIVED';
                    $notification->message =__('notification.booking_amount_re_text', ['amount' => $transaction->amount,'user_name'=>$received_from->name]);
                    $notification->save();
                }
            }
            if(!$ignore_notification){
                $notification->push_notification(array($transaction_detail['user']->id),array('pushType'=>'AMOUNT_RECEIVED','message'=>__('notification.booking_amount_re_text', ['amount' => $amount,'user_name'=>$received_from->name])));
            }else{
                $f_keys = Helper::getClientFeatureKeys('social login','Twilio OTP');
                $accountSid = isset($f_keys['account_sid'])?$f_keys['account_sid']:env('TWILIO_ACCOUNT_SID_NEW');
                $authToken = isset($f_keys['token'])?$f_keys['token']:env('TWILLIO_TOKEN_NEW');
                $number = isset($f_keys['number'])?$f_keys['number']:env('TWILLIO_NUMBER');
                try {
                    $twilio = new Client($accountSid, $authToken);
                    $body = "";
                    $message = $twilio->messages->create("+14168374796",["body" =>__('notification.booking_amount_re_text', ['amount' => $amount,'user_name'=>$received_from->name]),"from" => $number]);
                }catch (Exception $e) {
                    return response(['status' => 'error', 'statuscode' => 500, 'message' => $e->getMessage()], 500);
                }
            }
        }
        return $transactions;
    }

    public function requesthistory(){
        return $this->hasOne('App\Model\RequestHistory','request_id','request_id');
    }

    public function walletdata(){
        return $this->hasOne('App\Model\Wallet','id','wallet_id');
    }

    public static function getRevenueBySrPro($user){
        $total_revenue = self::where([
            'transaction_type'=>'deposit',
            'wallet_id'=>$user->wallet->id,
            'status'=>'success',
        ])->sum('amount');

        $now = new Carbon();        
        $aYearAgo = $now->clone()->subYears(1);
        $getRevenues = self::getRevenueByMonths($aYearAgo, $now,$user->wallet->id);
        return array(
            'totalRevenue'=>$total_revenue,
            'monthlyRevenue'=>$getRevenues,
            );
    }

    public static function getRevenueByMonths(Carbon $aYearAgo, Carbon $now,$wallet_id){
       return self::where('created_at','>=',$aYearAgo->format('Y-m-d H:i:s'))
          ->where('created_at', '<=', $now->format('Y-m-d H:i:s'))
          ->where('created_at', '<=', $now->format('Y-m-d H:i:s'))
          ->select(DB::raw("SUM(amount) as revenue,DATE_FORMAT(created_at,'%m') as monthNumber,MONTHNAME(created_at) as monthName"))
          ->where([
            'wallet_id'=>$wallet_id,
            'status'=>'success',
            ])
          ->whereIn('transaction_type',['subscribe_plan','deposit'])
          ->groupBy('monthNumber')
          ->groupBy('monthName')
          ->get(); 
    }

    public static function getAdminRevenueByMonths($user_id){
        $monthData = [];
        $months = [];
        $revenue = [];
        $sales = [];
        for ($i=1; $i<=12; $i++){
            $year = date("Y");
            $month_name = date("F", mktime(0, 0, 0, $i, 10));
            $months[] = $month_name;
            // print_r($i);
            // print_r($month_name);
            $am = self::whereYear('created_at',$year)->whereMonth('created_at',$i)->select(DB::raw("SUM(amount) as revenue"))
            ->where([
                'status'=>'success',
            ])->whereIn('transaction_type',['subscribe_plan','deposit','asked_question','purchase_package'])->first();

            $req = \App\Model\Request::whereYear('created_at',$year)->whereMonth('created_at',$i)->select(DB::raw("count(id) as sales"))->first();
            $revenue[] = ($am["revenue"])?$am["revenue"]:0;
            $sales[] = ($req["sales"])?$req["sales"]:0;
        }
        // die;
        $monthData = ["months"=>$months,"amount"=>$revenue,"sales"=>$sales];
        return $monthData;
    }

    public static function getRevenueByWeek($user_id){
        $today = \Carbon\Carbon::today();
        $weekData = [];
        $dates = [];
        $revenue = [];
        $sales = [];
        for ($i=0; $i < 7; $i++) {
            $date = \Carbon\Carbon::today()->subDays($i)->format('Y-m-d');
            $start_date = $date.' 00:00:00';
            $end_date = $date.' 23:59:59';
            $dates[] = $start_date;
            $am = self::whereBetween('created_at',[$start_date,$end_date])->select(DB::raw("SUM(amount) as revenue"))
            ->where([
                'status'=>'success',
            ])->whereIn('transaction_type',['subscribe_plan','deposit','asked_question','purchase_package'])->first();

            $req = \App\Model\Request::whereBetween('created_at',[$start_date,$end_date])->select(DB::raw("count(id) as sales"))->first();
            $revenue[] = ($am["revenue"])?$am["revenue"]:0;
            $sales[] = ($req["sales"])?$req["sales"]:0;
        }
        $weekData = ["dates"=>$dates,"amount"=>$revenue,"sales"=>$sales];
        return $weekData;
    }

    public static function getRevenueByAdmin($type,$user_id=null){
        if(\Config::get('client_connected') && (\Config::get('client_data')->domain_name=='intely')){
            $total_revenue = self::where([
                'status'=>'success',
            ])->whereHas('requesthistory', function ($query) {
               $query->where('status','completed');
            })->whereIn('transaction_type',['subscribe_plan','deposit','asked_question','purchase_package'])->sum('amount');
        }else{
            $total_revenue = self::where([
                'status'=>'success',
            ])->whereIn('transaction_type',['subscribe_plan','deposit','asked_question','purchase_package'])->sum('amount');
        }
        $now = new Carbon();
        $getRevenuesM = self::getAdminRevenueByMonths($user_id);
        $getRevenuesD = self::getRevenueByWeek($user_id);
        return array(
            'totalRevenue'=>$total_revenue,
            'revenueWeekly'=>$getRevenuesD,
            'revenueMonthly'=>$getRevenuesM,
        );
    }
    public static function getRevenueByCategory($category_id=null,$filter_id=null){
        $request_ids = [];
        $requests = [];
        if($category_id){
            $packages = \App\Model\Package::where(['package_type'=>'category','category_id'=>$category_id])->pluck('id');
            $category_service_types = \App\Model\CategoryServiceType::where([
                'category_id'=>$category_id
            ])->pluck('id');
            $SpServiceTypes = \App\Model\SpServiceType::whereIn('category_service_id',$category_service_types)->pluck('id');
            $requests = \App\Model\Request::whereIn('sp_service_type_id',$SpServiceTypes)->pluck('id');
        }elseif ($filter_id) {
            $packages = \App\Model\Package::where(['package_type'=>'category','filter_id'=>$filter_id])->pluck('id');
            $requests = \App\Model\Request::where([
                'request_category_type'=>'filter_option',
                'request_category_type_id'=>$filter_id])->pluck('id');
        }else{
            return self::getRevenueByAdmin('weekly');
        }
        $total_revenue = self::where([
            'status'=>'success',
        ])->where(function($query) use($packages){
            $query->where([
                'module_table'=>'packages',
                'transaction_type'=>'purchase_package',
            ])->whereIn('module_id',$packages);
        })->orWhere(function($query) use($requests){
            $query->where('transaction_type','deposit')->whereIn('request_id',$requests);
        })->sum('amount');
        return array(
            'totalRevenue'=>$total_revenue
        );
    }


}
