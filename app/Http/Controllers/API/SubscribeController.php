<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User,App\Helpers\Helper,App\Model\Feedback;
use App\Notification;
use Illuminate\Support\Facades\Auth;
use App\Model\SubscribePlan,App\Model\Plan,App\Model\Transaction;
use App\Model\Payment;
use Validator,Hash,Mail,DB;
use DateTime,DateTimeZone;
use Redirect,Response,File,Image;
class SubscribeController extends Controller
{
	/**
     * @SWG\Post(
     *     path="/subscribe-plan",
     *     description="Subscribes",
     * tags={"Subscribes"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="plan_id",
     *         in="query",
     *         type="string",
     *         description="plan ids with comma seprated who are matched with admin panel",
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

    public static function postSubscribePlan(Request $request) {
    	try{
	    	$user = Auth::user();
	    	$rules = ['plan_id'=>'required'];
	    	$validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $datenow = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
            $input = $request->all();
            $bs_plans = ["com.mp2r.basic","com.mp2r.premium","com.mp2r.executive"];
            $plan_ids = explode(',', $input['plan_id']);
            $exist_plan  = SubscribePlan::where(['user_id'=>$user->id])
            ->where('expired_on','>',$datenow)
            ->whereHas('plan', function ($query) {
                $query->whereIn('plan_id',["com.mp2r.basic","com.mp2r.premium","com.mp2r.executive"]);
            })
            ->first();
            $basics_plan = null;
            if($exist_plan && $exist_plan->plan){
                $basics_plan = $exist_plan->plan->plan_id;
            }
            foreach ($plan_ids as $key => $plan_id) {
                $plan = Plan::where('plan_id',$plan_id)->first();
                if($plan){
                    $subscribeplan  = SubscribePlan::where(
                    	['user_id'=>$user->id,
                    	'plan_id'=>$plan->id
                    ])->where('expired_on','>',$datenow)->first();
                    if(!$subscribeplan){
                        $expired_on = \Carbon\Carbon::now()->addMonth(1)->format('Y-m-d H:i:s');
                        if(in_array($plan_id, $bs_plans) && $basics_plan!==$plan_id){
                            if($exist_plan){
                                $exist_plan->delete();
                            }
                        }
                    }else{
                        $expired_on = \Carbon\Carbon::parse($subscribeplan->expired_on)->addMonth(1)->format('Y-m-d H:i:s');
                    }
                    $new_subscribe = SubscribePlan::firstOrCreate([
                    	'plan_id'=>$plan->id,
                    	'user_id'=>$user->id
                    ]);
                    $new_subscribe->expired_on = $expired_on;
                    $new_subscribe->save();
                    $transaction = Transaction::create(array(
            				'amount'=>$plan->price,
            				'transaction_type'=>'subscribe_plan',
            				'status'=>'success',
            				'wallet_id'=>$user->wallet->id,
                            'closing_balance'=>$user->wallet->balance,
            		));
                    if($transaction){
                    	$payment = Payment::create(array(
                    		'from'=>$user->id,
                    		'to'=>$user->id,
                    		'transaction_id'=>$transaction->id
                    	));
                        $transaction->module_table  = 'subscribe_plans';
                        $transaction->module_id  = $new_subscribe->id;
                        $transaction->save();
                    }
                }
            }
            $token = $user->createToken('consult_app')->accessToken;
            $user->token = $token;
            $user->profile;
            $user->roles;
            $user->subscriptions = $user->getSubscription($user);
            if($user->profile){
                $user->profile->bio = $user->profile->about;
                $user->totalRating =  $user->profile->rating;
            }
            $user->categoryData = $user->getCategoryData($user->id);
            $user->additionals = $user->getAdditionals($user->id);
            $user->insurances = $user->getInsurnceData($user->id);
            $user->custom_fields = $user->getCustomFields($user->id);
            $user->services = $user->getServices($user->id);
            $user->filters = $user->getFilters($user->id);
            if($user->hasrole('service_provider')){
                $user->patientCount = User::getTotalRequestDone($user->id);
                $user->reviewCount = Feedback::reviewCountByConsulatant($user->id); 
            }
            $user = Helper::getMoreData($user);
            return response([
            	'status' => "success", 
            	'statuscode' => 200,
            	'message' => __('Subscribed Plan'),
            	'data'=>$user
            ], 200); 
    	}catch(Exception $ex){
    		return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
    	}
    }
}
