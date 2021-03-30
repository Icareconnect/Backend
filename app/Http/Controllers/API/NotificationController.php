<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\User;
use Illuminate\Support\Facades\Auth;
use Validator,Hash,Mail,DB;
use DateTime,DateTimeZone;
use Redirect,Response,File,Image;
use Illuminate\Support\Facades\URL;
use App\Model\Role;
use App\Model\Wallet;
use App\Model\Profile;
use App\Model\Payment;
use App\Model\Transaction;
use App\Model\Card;
use App\Model\SocialAccount;
use Socialite,Exception;
use Intervention\Image\ImageManager;
use Carbon\Carbon;
use Cartalyst\Stripe\Stripe;
use App\Notification;
class NotificationController extends Controller
{
	/**
     * @SWG\Get(
     *     path="/notifications",
     *     description="Notification List",
     * tags={"Notification"},
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
    public static function getNotificationList(Request $request) {
    	try{
	    	$user = Auth::user();
	    	$notifications = [];
            $per_page = (isset($request->per_page)?$request->per_page:10);
	    	Notification::markAsRead($user->id);
	    	$notifications = Notification::select('id','notification_type as pushType','message','module','read_status','created_at','sender_id as form_user','receiver_id as to_user','module_id')->where('receiver_id',$user->id)
	    	->orderBy('id', 'desc')->cursorPaginate($per_page);
	    	if($notifications){
	    		foreach ($notifications as $key => $notification) {
		    		$notification->form_user = User::select('id','name','profile_image')->where('id',$notification->form_user)->first();
		    		$notification->to_user = User::select('id','name','profile_image')->where('id',$notification->to_user)->first();
		    		$notification->sentAt = \Carbon\Carbon::parse($notification->created_at)->getPreciseTimestamp(3);
	    		}
	    	}
            $after = null;
            if($notifications->meta['next']){
                $after = $notifications->meta['next']->target;
            }
            $before = null;
            if($notifications->meta['previous']){
                $before = $notifications->meta['previous']->target;
            }
            $per_page = $notifications->perPage();
	    	return response(['status' => "success", 'statuscode' => 200,
	                            'message' => __('Notifications list'), 'data' =>['notifications'=>$notifications->items(),'after'=>$after,'before'=>$before,'per_page'=>$per_page]], 200);
    	}catch(Exception $ex){
    		return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
    	}
    }
}
