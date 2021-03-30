<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use LRedis;
use App\User,App\Model\Message;
use Illuminate\Support\Facades\Auth;
use Validator,Hash,Mail,DB;
use DateTime,DateTimeZone;
use Redirect,Response,File,Image;
use Illuminate\Support\Facades\URL;
use App\Model\Role;
use App\Model\Wallet;
use App\Model\Profile;
use App\Model\Payment;
use App\Model\Card;
use App\Model\SocialAccount;
use Socialite,Exception;
use Intervention\Image\ImageManager;
use Carbon\Carbon;
class ChatController extends Controller{

	// public function __construct()
	// {
	// 	$this->middleware('guest');
	// }
	public function sendMessage(Request $request){
		$user = Auth::user();
		$rules = ['message'=>'required',
	    		'request_id'=>'required'];
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                $validator->getMessageBag()->first()), 400);
        }
	    $input = $request->all();
	    $redis = LRedis::connection();
	    Message::create([
            'user_id' => $user->id,
            'message' => $input['message'],
            'request_id' => $input['request_id']
        ]);
		$data = ['message' => $input['message'], 'user' => $user->id];
		$redis->publish('message', json_encode($data));
		return response()->json([]);
	}


	/**
     * @SWG\Get(
     *     path="/chat-listing",
     *     description="Get Chat Listing by user or service_provider",
     * tags={"Chat"},
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
     */
    public static function getChatListing(Request $request) {
        try{
            $user = Auth::user();
            // print_r($user);die;
            $requests = [];
            $service_type = 'chat';
            $per_page = (isset($request->per_page)?$request->per_page:10);
            $requests = \App\Model\Request::select('id','service_id','from_user','to_user','created_at as booking_date','created_at')->
            where(function($q) use ($user) {
            	if($user->hasrole('customer')){
            		$q->where('from_user',$user->id);
            	}else if($user->hasrole('service_provider')){
            		$q->where('to_user',$user->id);
            	}
			})
            ->whereHas('servicetype', function($query) use ($service_type){
                if($service_type!=='all')
                    return $query->where('service_type', $service_type);
            })
            ->whereHas('requesthistory', function($query){
                    return $query->whereNotIn('status',['pending','accept','failed','canceled']);
            })
            ->orderBy('id', 'desc')->cursorPaginate($per_page);
            foreach ($requests as $key => $request_status) {
            	$last_message = \App\Model\Message::getLastMessage($request_status);
                $request_status->unReadCount = \App\Model\Message::getUnReadCount($request_status,$user->id);
                $date = Carbon::parse($request_status->booking_date,'UTC')->setTimezone('Asia/Kolkata');
                $request_status->booking_date = $date->isoFormat('D MMMM YYYY, h:mm:ss a');
                $request_status->time = $date->isoFormat('h:mm a');
                $request_history = $request_status->requesthistory;
                $request_status->last_message = $last_message;
                $request_status->duration = $request_history->duration;
                $request_status->service_type = $request_status->servicetype->type;
                $request_status->status = $request_history->status;
                $request_status->from_user = User::select('id', 'name', 'email','phone','profile_image')->where('id',$request_status->from_user)->first();
                $request_status->to_user = User::select('id', 'name', 'email','phone','profile_image')->with('profile')->where('id',$request_status->to_user)->first();
                unset($request_status->requesthistory); 
                unset($request_status->service_id); 
                unset($request_status->servicetype);
            }
            $after = null;
            if($requests->meta['next']){
                $after = $requests->meta['next']->target;
            }
            $before = null;
            if($requests->meta['previous']){
                $before = $requests->meta['previous']->target;
            }
            $per_page = $requests->perPage();
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('Chat Listing'), 'data' =>['lists'=>$requests->items(),'after'=>$after,'before'=>$before,'per_page'=>$per_page]], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }

    /**
     * @SWG\Get(
     *     path="/chat-messages",
     *     description="Get Chat Messages accodring to request",
     * tags={"Chat"},
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
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     */
    public static function getMessages(Request $request) {
        try{
            $user = Auth::user();
            $requests = [];
            $service_type = 'chat';
            $rules = ['request_id' => 'required'];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $per_page = (isset($request->per_page)?$request->per_page:10);
            $request_dt = \App\Model\Request::where('id',$request->request_id)->
            where(function($q) use ($user) {
            	if($user->hasrole('customer')){
            		$q->where('from_user',$user->id);
            	}else if($user->hasrole('service_provider')){
            		$q->where('to_user',$user->id);
            	}
			})->first();
			$message_error = null;
            $currentTimer = null;
			if($request_dt){
                Message::markAsRead($request->request_id,$user->id);
				$messages = \App\Model\Message::select('id','message','user_id','created_at','message_type as messageType','delivered as isDelivered','read as isRead','image_url as imageUrl','status')->with(['user' => function($query) {
                            return $query->select(['id', 'name', 'email','phone','profile_image']);
                        }])->where('request_id',$request_dt->id)
	            ->orderBy('id', 'desc')->cursorPaginate($per_page);
	            foreach ($messages as $key => $message) {
	            	$receiverId = null;
	            	if($request_dt->from_user==$message->user_id){
	            		$receiverId = $request_dt->to_user;
	            	}elseif ($request_dt->to_user==$message->user_id) {
	            		$receiverId = $request_dt->from_user;
	            	}
	            	$message->sentAt = \Carbon\Carbon::parse($message->created_at)->getPreciseTimestamp(3);
	            	$message->receiverId = $receiverId;
	            	$message->senderId = $message->user_id;
                    $message->messageId = $message->id;
	            }
                if($request_dt->requesthistory->status=='in-progress'){
                    $dateznow = new DateTime("now", new DateTimeZone('UTC'));
                    $datenow = $dateznow->format('Y-m-d H:i:s');
                    $timeFirst  = strtotime($request_dt->requesthistory->updated_at);
                    $timeSecond = strtotime($datenow);
                    $currentTimer = ($timeSecond - $timeFirst);
                }
	            $after = null;
	            if($messages->meta['next']){
	                $after = $messages->meta['next']->target;
	            }
	            $before = null;
	            if($messages->meta['previous']){
	                $before = $messages->meta['previous']->target;
	            }
	            $per_page = $messages->perPage();
	            return response(['status' => "success", 'statuscode' => 200,
	                                'message' => __('Chat Listing'), 'data' =>['messages'=>$messages->items(),'after'=>$after,'before'=>$before,'per_page'=>$per_page,'request_status'=>$request_dt->requesthistory->status,'currentTimer'=>$currentTimer]], 200);
			}else{
				$message_error =' Request not found';
			}
            return response(array('status' => "error", 'statuscode' => 400, 'message' =>__($message_error)), 400);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }
}
