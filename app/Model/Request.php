<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Model\RequestDetail;
use App\Model\RequestDate;
use App\Model\MasterPreference;
use App\Model\LastLocation;
use App\Model\CustomInfo;
use App\Model\FilterTypeOption;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Model\Image as ModelImage;
class Request extends Model
{
    //
    protected $guarded = []; 
    
    public function getSecondOponion($data){
        $secondoponion = CustomInfo::where([
            'ref_table'=>'requests',
            'ref_table_id'=>$data->id,
            'info_type'=>'secondoponion'
        ])->first();
        if($secondoponion){
           $secondoponion->raw_detail = json_decode($secondoponion->raw_detail);
           return $secondoponion->raw_detail; 
        }
        return $secondoponion;
    }

    public static function getFreeTextSymptomDetails($data){
        $symptom_details_data = "";
        $symptom_details = CustomInfo::where([
            'ref_table'=>'requests',
            'ref_table_id'=>$data->id,
            'info_type'=>'symptom_details'
        ])->first();
        if($symptom_details){
           $symptom_details->raw_detail = json_decode($symptom_details->raw_detail);
           if(isset($symptom_details->raw_detail->symptom_details)){
                return $symptom_details->raw_detail->symptom_details;
           }
        }
        return $symptom_details_data;
    }

    public function getCustomDoctor($req_id){
        $assign_doctor = [];
        $doctor_id = CustomInfo::where([
            'ref_table'=>'requests',
            'ref_table_id'=>$req_id,
            'info_type'=>'doctor_assign'
        ])->first();
        if($doctor_id){
           $doctor_id->raw_detail = json_decode($doctor_id->raw_detail);
           if(isset($doctor_id->raw_detail->doctor_id)){
                $data = CustomInfo::where('id',$doctor_id->raw_detail->doctor_id)->first();
                if($data){
                    $assign_doctor= json_decode($data->raw_detail);
                }
           }
        }
        return $assign_doctor;
    }

    /**
     * Get the Request History From RequestHistory Model.
     */
    public function requesthistory()
    {
        return $this->hasOne('App\Model\RequestHistory','request_id');
    }

    /**
     * Get the Request History From RequestHistory Model.
     */
    public function requestdates()
    {
        return $this->hasMany('App\Model\RequestDate','request_id');
    }

    /**
     * Get the Request History From RequestHistory Model.
     */
    public function requestStatus($id)
    {
        $status = CustomInfo::where([
            'ref_table'=>'request',
            'ref_table_id'=>$id,
            'info_type'=>'request_status'
        ])->orderBy('id','ASC')->get();
        return $status;
    }

    /**
     * Get the Request History From RequestHistory Model.
     */
    public function prescription()
    {
        return $this->hasOne('App\Model\PreScription','request_id');
    }
    /**
     * Get the Request History From Transaction Model.
     */
    public function transaction()
    {
        return $this->hasOne('App\Model\Transaction','request_id');
    }

    public function transactions()
    {
        return $this->hasMany('App\Model\Transaction','request_id','id');
    }

    public function from_users() {

        return $this->belongsTo('App\User','from_user','id');
    }

    public function to_users() {

        return $this->belongsTo('App\User','to_user','id');
    }
    /**
     * Get the Service Type From Service Model.
     */
    public function servicetype()
    {
        return $this->hasOne('App\Model\Service','id','service_id');
    }

    /**
     * Get the Service Type From Service Model.
     */
    public function cus_info()
    {
        return $this->hasOne('App\User','id','from_user');
    }

    public function sr_info()
    {
        return $this->hasOne('App\User','id','to_user');
    }

    public static function getMoreData($request_status){
        $r_data = self::where('id',$request_status->id)->first();

        $request_status->service = \App\User::getServiceObjectBySp($r_data->sp_service_type_id,$r_data->to_user);
        $request_status->symptoms = [];
        $symptoms = [];
        $symptom_raw = \App\Model\UserMasterPreference::where(['request_id'=>$request_status->id])->get();
        foreach ($symptom_raw as $key => $symptom) {
            $sym_data = \App\Model\MasterPreferencesOption::select('id','name','image','description','preference_id as symptom_id')->whereHas('masterpreference', function ($query) {
                   $query->where('type','symptoms');
                })->where('id',$symptom->preference_option_id)->first();
            if($sym_data){
                $symptoms[] = $sym_data;
            }
        }
        $request_status->rating =  null;
        $request_status->comment =  null;
        $rating = \App\Model\Feedback::where(['request_id'=>$request_status->id])->first();
        if($rating){
            $request_status->rating = $rating->rating;
            $request_status->comment = $rating->comment;
        }
        $request_status->main_service_type = $request_status->servicetype->service_type;
        $request_status->service_type = $request_status->servicetype->type;
        $request_status->symptoms = $symptoms;
        $request_status->covids = MasterPreference::getMasterPreferencesByRequest($r_data->from_user,$r_data->id,'covid');
        $request_status->symptom_details = self::getFreeTextSymptomDetails($request_status);
        $request_status->symptom_image = self::getSymptomFile($request_status->id);
        $request_status->symptom_images = self::getSymptomFiles($request_status->id);
        $request_status->packages = self::getPackageDetail($request_status);
        $request_status->duties = self::getDuties($request_status);
        $request_status->user_status = $r_data->user_status;
        $request_status->user_comment = $r_data->user_comment;
        $request_status->user_status_time = $r_data->updated_at->format('Y-m-d h:i:s');
        $request_status->userIsApproved = false;
        $request_status->from_user->master_preferences = \App\Model\MasterPreference::getMasterPreferences($request_status->from_user->id);
        $request_status->to_user->master_preferences = \App\Model\MasterPreference::getMasterPreferences($request_status->to_user->id);
        if($r_data->user_status!=='pending'){
            $request_status->userIsApproved = true;
        }
        $request_status->canceled_by = null;
        $canceled = \App\Model\RequestLog::where([
            'request_id'=>$request_status->id,
            'type'=>'status_change',
            'request_status'=>'canceled'])->first();
        if($canceled){
            $request_status->canceled_by = User::select('id','name','phone','country_code')->where('id',$canceled->updated_by)->first();
        }

        return $request_status;
    }

    public static function getDuties($data){
        $duties = [];
        $duties_details = CustomInfo::where([
            'ref_table'=>'requests',
            'ref_table_id'=>$data->id,
            'info_type'=>'duties'
        ])->first();
        if($duties_details){
           $duties_details->raw_detail = json_decode($duties_details->raw_detail);
           if(isset($duties_details->raw_detail->duties)){
                $duties = MasterPreferencesOption::select(['id', 'name as option_name','preference_id','image','description'])->whereIn('id',$duties_details->raw_detail->duties)->get();
           }
        }
        return $duties;
    }

    public static function getSymptomFile($request_id){
        $image = ModelImage::where([
                'module_table'=>'request_symptoms',
                'module_table_id'=>$request_id
            ])->first();
        if($image){
            return ['image'=>$image->image_name,'type'=>$image->type];
        }else{
            return null;
        }
    }

    public static function getSymptomFiles($request_id){
        $data = [];
        $images = ModelImage::where([
                'module_table'=>'request_symptoms',
                'module_table_id'=>$request_id
            ])->get();
        foreach ($images as $key => $image) {
            $data[] = ['image'=>$image->image_name,'type'=>$image->type];
        }
        return $data;
    }

    public static function getPackageDetail($request_status){
        if($request_status->requesthistory && $request_status->requesthistory->module_table=='packages' && $request_status->requesthistory->module_id){
            $packages = \App\Model\Package::where('id',$request_status->requesthistory->module_id)->first();
            return $packages;
        }
        return null;
    }

    public static function getExtraRequestInfo($request_id,$timzone){
        $res = Self::where('id',$request_id)->first();
        $request_detail = null;
        $request_detail =  RequestDetail::where('request_id',$request_id)->first();
        if($request_detail){
            if($request_detail->phone_number){
                $request_detail->phone_number = (string)$request_detail->phone_number;
            }
            $lat_longs=[];
            if($res->sr_info->profile && $res->sr_info->profile->lat!==null &&  $res->sr_info->profile->long!==null){
                $request_detail->center_location = null;
                if($res->servicetype && $res->servicetype->service_type=='clinic_visit'){
                    $request_detail->center_location = ["name"=>$res->sr_info->profile->location_name,"lat"=>$res->sr_info->profile->lat,"long"=>$res->sr_info->profile->long];
                }
                if($request_detail->lat!==null && $request_detail->long!==null){
                    $lat_longs['user_lat'] = $request_detail->lat;
                    $lat_longs['user_long'] = $request_detail->long;
                    $lat_longs['doctor_lat'] = $res->sr_info->profile->lat;
                    $lat_longs['doctor_long'] = $res->sr_info->profile->long;
                }
            }
            $request_detail->distance = null;
            if(isset($lat_longs['user_lat'])){
                $request_detail->distance = Helper::twopoints_on_earth($lat_longs). " KM";
            }
            $request_dates =  RequestDate::where('request_id',$request_id)->get();
            $request_detail->filter_id = null;
            $request_detail->filter_name = null;
            if($res->request_category_type=='filter_option'){
                $FilterTypeOption = FilterTypeOption::where('id',$res->request_category_type_id)->first();
                if($FilterTypeOption){
                    $request_detail->filter_id = $FilterTypeOption->id;
                    $request_detail->filter_name = $FilterTypeOption->option_name;
                }
            }
            // print_r($request_dates);die;
            $start_time = null;
            $end_time = null;
            $dates = [];
            foreach ($request_dates as $key => $request_date) {
                $dates[] = Carbon::parse($request_date->start_date_time,'UTC')->setTimezone($timzone)->format('Y-m-d');
                $start_time = Carbon::parse($request_date->start_date_time,'UTC')->setTimezone($timzone)->format('h:i a');
                $end_time = Carbon::parse($request_date->end_date_time,'UTC')->setTimezone($timzone)->format('h:i a');
            }
            $request_detail->start_time = $start_time;
            $request_detail->end_time = $end_time;
            $request_detail->working_dates = implode(',',$dates);
        }
        return $request_detail;
    }

    public static function getLastLocation($req_id,$sp_id){
        $last_lo = LastLocation::select('lat','long')->where(['request_id'=>$req_id,'user_id'=>$sp_id])->orderBy('id','DESC')->first();
        if(!$last_lo){
            $last_lo = null;
        }
        return $last_lo;
    }

    public static function getReqAnaliticsBySrPro($sr_pid){
       $reqs = Self::where('to_user',$sr_pid)->get();
       $sr_services = User::getEnableServicesData($sr_pid);
       $total_request = $reqs->count();
       $total_chats = 0;
       $total_calls = 0;
       $total_completed_request = 0;
       $total_unsuccess_request = 0;
       $totalShiftCompleted = 0;
       $totalHourCompleted = 0;
       $totalShiftDecline = 0;
       $request_services = [];
       foreach ($reqs as $key => $req) {
            // if($req->user_status=='declined'){
            //     $totalShiftDecline++;
            // }elseif ($req->user_status=='approved') {
            //     $totalShiftCompleted++;
            // }
            if($req->verified_hours || $req->user_by_hours){
                if($req->verified_hours){
                    $totalHourCompleted = $totalHourCompleted + $req->verified_hours;
                }else{
                    $totalHourCompleted = $totalHourCompleted + $req->user_by_hours;
                }
            }
            if($req->requesthistory && $req->requesthistory->status=='completed'){
                $total_completed_request++;
            }
            if($req->requesthistory && ($req->requesthistory->status=='failed' || $req->requesthistory->status=='canceled')){
                $canceled = \App\Model\RequestLog::where([
                    'request_id'=>$req->id,
                    'type'=>'status_change',
                    'request_status'=>'canceled',
                    'updated_by'=>$req->to_user])->first();
                if($canceled){
                    $totalShiftDecline++;
                }
                $total_unsuccess_request++;
            }
            if($req->requesthistory && $req->servicetype->type=='chat'){
                $total_chats++;
            }
            if($req->requesthistory && $req->servicetype->type=='call'){
                $total_calls++;
            }
       }
       foreach ($sr_services as $sr_key => $sr) {
            $sr_services[$sr_key]['count'] = Self::where(['to_user'=>$sr_pid,'service_id'=>$sr['service_id']])->count();
       }
       return array(
        'totalRequest'=>$total_request,
        'totalChat'=>$total_chats,
        'totalCall'=>$total_calls,
        'completedRequest'=>$total_completed_request,
        'unSuccesfullRequest'=>$total_unsuccess_request,
        'totalShiftCompleted'=>$total_completed_request,
        'totalShiftDecline'=>$totalShiftDecline,
        'totalHourCompleted'=>$totalHourCompleted,
        'services'=>$sr_services,
        );
    }
    
}
