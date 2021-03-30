<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\MasterSlot;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use Carbon\Carbon;
class MasterSlotController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $timezone = 'Asia/Kolkata';
        if(isset($request->timezone)){
            $timezone = $request->timezone;
        }
        $masterslots = MasterSlot::orderBy('id','ASC')->get();
        foreach ($masterslots as $key => $masterslot) {
            $start_time_date = Carbon::parse($masterslot->start_time,'UTC')->setTimezone($timezone);
            $end_time_date = Carbon::parse($masterslot->end_time,'UTC')->setTimezone($timezone);
            $masterslot->start_time = $start_time_date->format('H:i A');;
            $masterslot->end_time = $end_time_date->format('H:i A');;
        }
        return view('admin.master_slot.index',compact('masterslots'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAddOrEditSlot(Request $request)
    {
        $intervals = [];
        $start_times = Helper::getTimeSlot("00:00","23:59",30);
        $end_times = Helper::getTimeSlot("00:30","23:59",30);
        $masterslots = MasterSlot::orderBy('id','ASC')->where('type','all_day')->get();
        $timezone = 'Asia/Kolkata';
        if(isset($request->timezone)){
            $timezone = $request->timezone;
        }
        if($masterslots->count()>0){
            foreach ($masterslots as $key => $masterslot) {
                $start_time_date = Carbon::parse($masterslot->start_time,'UTC')->setTimezone($timezone);
                $end_time_date = Carbon::parse($masterslot->end_time,'UTC')->setTimezone($timezone);
                $start_time = $start_time_date->format('H:i');;
                $end_time = $end_time_date->format('H:i');;
                $intervals[] = [
                    "seleted_start"=>$start_time,
                    "seleted_end"=>$end_time,
                    "start_times"=>$start_times,
                    "end_times"=>$end_times
                ];
            }
        }else{
            $intervals[] = [
                "seleted_start"=>"00:00",
                "seleted_end"=>"00:30",
                "start_times"=>$start_times,
                "end_times"=>$end_times
            ];
        }
        return view('admin.master_slot.edit',compact('masterslots','intervals','start_times','end_times'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function postAddOrEditSlot(Request $request)
    {
        $input = $request->all();
        $rules = [
            'timzone' => 'required',
            'interval' => 'required'
        ];
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response(array('status' => "error", 'statuscode' => 400, 'message' =>
            $validator->getMessageBag()->first()), 400);
        }
        $timezone = 'Asia/Kolkata';
        if (isset($request->timezone)) {
            $timezone = $request->timezone;
        }
        if (isset($input['interval'][0]) && isset($input['interval'][0]['seleted_start']) && isset($input['interval'][0]['seleted_end'])) {
            MasterSlot::truncate();
            foreach ($input['interval'] as $slot) {
                if (isset($slot['seleted_start']) && isset($slot['seleted_end'])) {
                    $start_time = Carbon::parse($slot['seleted_start'], $timezone)->setTimezone('UTC')->format('H:i:s');
                    $end_time = Carbon::parse($slot['seleted_end'], $timezone)->setTimezone('UTC')->format('H:i:s');
                    $masterslot = new MasterSlot();
                    $masterslot->start_time = $start_time;
                    $masterslot->end_time = $end_time;
                    $masterslot->save();
                }
            }
            return response(['status' => "success", 'statuscode' => 200,
                                    'message' => __('success')], 200);
        }else{
            return response(array('status' => "error", 'statuscode' => 400, 'message' =>'Please Select Valid Interval'), 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function DeleteAllSlot(Request $request)
    {
        if(MasterSlot::truncate()){
            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'error']);
        }
    }
}
