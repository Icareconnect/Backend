<?php

namespace App\Http\Controllers\API;
use App\Feed;
use App\User;
use Socialite,Exception;
use Carbon\Carbon,Config;
use DateTime,DateTimeZone;
use Validator,Hash,Mail,DB;
use App\Model\ProteinIntake;
use Illuminate\Http\Request;
use App\Model\DailyProteinTakes;
use App\Model\DailyProteinDates;
use Redirect,Response,File,Image;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ProteinController extends Controller{

	/**
     * @SWG\Get(
     *     path="/protein-limit",
     *     description="getProteinLimit",
     * 	   tags={"Protien Intake"},
     *     security={
     *     {"Bearer": {}},
     *   	},  
     * @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     *
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Feed  $feed
     * @return \Illuminate\Http\Response
     */

    public function getProteinLimit(Request $request){
        try {
            $user = Auth::user();
            $validator = Validator::make($request->all(),[]);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $timezone = $request->header('timezone');
            if(!$timezone){
                $timezone = 'Asia/Kolkata';
            }
            $start_date = Carbon::now("UTC")->format('Y-m-d');
            $response = $this->getWaterLimitData($user,$start_date);
            return response(['status' =>"success", 'statuscode' => 200, 'message' => __('Limit '), 'data' =>$response], 200);
        } catch (Exception $e) {
           return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500); 
        }
    }

    /**
     * @SWG\Post(
     *     path="/protein-limit",
     *     description="postSetProteinLimit",
     * tags={"Protien Intake"},
     *     security={
     *     {"Bearer": {}},
     *   },
     * @SWG\Parameter(
     *       name="limit",
     *       in="query",
     *       required=true,
     *       description="limit in gms.", 
     *       type="string" 
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function postSetProteinLimit(Request $request){
        try {
            $user = Auth::user();
            $rules = ['limit' => 'required'];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $timezone = $request->header('timezone');
            if(!$timezone){
                $timezone = 'Asia/Kolkata';
            }
            $start_date = Carbon::now('UTC')->format('Y-m-d');
            $waterintake = ProteinIntake::where(['user_id' => $user->id])->first();
            if(!$waterintake){
                $waterintake = new ProteinIntake();
                $waterintake->user_id = $user->id;
            }
            $waterintake->daily_limit = $request->limit;
            $waterintake->save();

            $DailyWaterDate = DailyProteinDates::where(['user_id'=>$user->id,'date'=>$start_date])->first();
            if(!$DailyWaterDate){
                $DailyWaterDate = new DailyProteinDates();
                $DailyWaterDate->user_id = $user->id;
                $DailyWaterDate->date = $start_date;
                $DailyWaterDate->total_usage = 0;
            }
            $DailyWaterDate->daily_limit = $request->limit;
            $DailyWaterDate->save();
            $response = $this->getWaterLimitData($user,$start_date);
            return response(['status' =>"success", 'statuscode' => 200, 'message' => __('Done'), 'data' =>$response], 200);
        } catch (Exception $e) {
           return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500); 
        }
    }

    /**
     * @SWG\Get(
     *     path="/daily-usage-protein",
     *     description="getDailyUsageProtein",
     * tags={"Protien Intake"},
     *     security={
     *     {"Bearer": {}},
     *   },
     * @SWG\Parameter(
     *       name="date_time",
     *       in="query",
     *       required=true,
     *       description="date Y-m-d H:i:s fomat like 2020-11-13 18:29:59", 
     *       type="string" 
     *      ),    
     * @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     *
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function getDailyUsageProtein(Request $request){
        try {
            $user = Auth::user();
            $rules = ['date'=>'required'];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $timezone = $request->header('timezone');
            if(!$timezone){
                $timezone = 'Asia/Kolkata';
            }
            $start_time = Carbon::parse($request->date, $timezone)->setTimezone('UTC')->format('Y-m-d');
            $response = $this->getWaterLimitData($user, $start_time);
            return response(['status' =>"success", 'statuscode' => 200, 'message' => __('Limit '), 'data' =>$response], 200);
        } catch (Exception $e) {
           return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500); 
        }
    }
     /**
     * @SWG\Post(
     *     path="/drink-protein",
     *     description="postDrinkProtein",
     * tags={"Protien Intake"},
     *     security={
     *     {"Bearer": {}},
     *   },
     * @SWG\Parameter(
     *       name="quantity",
     *       in="query",
     *       required=true,
     *       description="quantity in gms.", 
     *       type="string" 
     *      ),    
     * @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     *
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function postDrinkProtein(Request $request){
        try {
            $user = Auth::user();
            $rules = ['quantity' => 'required'];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $timezone = $request->header('timezone');
            if(!$timezone){
             $timezone = 'Asia/Kolkata';
            }
            $start_date_time_utc = Carbon::now('UTC')->format('Y-m-d H:i:s');
            $start_date = Carbon::now('UTC')->format('Y-m-d');
            $DailyWaterDate = DailyProteinDates::where(['user_id'=>$user->id,'date'=>$start_date])->first();
            $DailyGlass = new DailyProteinTakes();
            $DailyGlass->user_id = $user->id;
            $DailyGlass->date_time = $start_date_time_utc;
            $DailyGlass->quantity = $request->quantity; 
            $DailyGlass->save();
            $DailyWaterDate->increment('total_usage',$request->quantity);
            $response = $this->getWaterLimitData($user,$start_date);
            return response(['status' =>"success", 'statuscode' => 200, 'message' => __('Limit '), 'data' =>$response], 200);
        } catch (Exception $e) {
           return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500); 
        }
    }
    private function getWaterLimitData($user,$start_date){
        $limit = null;
        $today_intake = 0;
        $DailyWaterDate =  null;
        $total_achieved_goal = 0;
        $waterintake = ProteinIntake::where(['user_id'=>$user->id])->first();
        if($waterintake){
            $DailyWaterDate = DailyProteinDates::where(['user_id'=>$user->id, 'date'=>$start_date])->first();
            if(!$DailyWaterDate){
                $DailyWaterDate = new DailyProteinDates();
                $DailyWaterDate->user_id = $user->id;
                $DailyWaterDate->date = $start_date;
                $DailyWaterDate->total_usage = 0;
            }
            $DailyWaterDate->daily_limit = $waterintake->daily_limit;
            $DailyWaterDate->save();
            $limit = $waterintake->daily_limit;
        }
        $DailyWaterDates = DailyProteinDates::where(['user_id' => $user->id])->get();
        foreach ($DailyWaterDates as $DailyWater) {
            if($DailyWater->total_usage >= $DailyWater->daily_limit){
                $total_achieved_goal++;
            }
        }
        if($DailyWaterDate){
            $today_intake =  $DailyWaterDate->total_usage;
        }
        return [
	    	'limit'=>$limit,
	        'today_intake'=>$today_intake,
	        'total_achieved_goal'=>$total_achieved_goal
    	];
    }
    
}
