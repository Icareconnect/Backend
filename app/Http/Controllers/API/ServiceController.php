<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Config;
use App\User;
use App\Model\RecentView;
use App\Model\SpCourse;
use Illuminate\Support\Facades\Auth;
use Validator,Hash,Mail,DB;
use DateTime,DateTimeZone;
use Redirect,Response,File,Image;
use Illuminate\Support\Facades\URL;
use App\Model\Role,App\Model\FilterType;
use App\Model\Wallet,App\Model\ServiceProviderFilterOption;
use App\Model\CategoryServiceType;
use App\Model\Feedback,App\Model\Banner,App\Model\Cluster;
use App\Model\Profile;
use App\Model\Payment;
use App\Helpers\Helper;
use App\Model\Service,App\Model\SocialAccount,App\Model\EnableService;
use App\Model\Subscription;
use App\Model\Category,App\Model\PreScription,App\Model\PreScriptionMedicine,App\Model\Image as ModelImage;
use Socialite,Exception;
use Intervention\Image\ImageManager;
use Carbon\Carbon;
use App\Notification;
use App\Model\CategoryServiceProvider,App\Model\SpServiceType,App\Model\ServiceProviderSlot;
use App\Model\ServiceProviderSlotsDate;
use App\Model\UserMasterPreference;
use App\Model\Request as RequestData;
class ServiceController extends Controller{
	
    public $successStatus = 200;

    /**
     * @SWG\Get(
     *     path="/clusters",
     *     description="GET Clusters",
     * tags={"Cluster"},
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

    public static function getClusterList(Request $request) {
    	try{
            $per_page = (isset($request->per_page)?$request->per_page:10);
	    	$clusters = Cluster::orderBy('id','DESC')->cursorPaginate($per_page);
            foreach ($clusters as $key => $cluster) {
                $categories = [];
                foreach ($cluster->cluster_category as $key => $category) {
                    $categories[] = $category->category;
                }
                $cluster->categories = $categories;
                unset($cluster->cluster_category);
            }
	    	$after = null;
            if($clusters->meta['next']){
                $after = $clusters->meta['next']->target;
            }
            $before = null;
            if($clusters->meta['previous']){
                $before = $clusters->meta['previous']->target;
            }
            $per_page = $clusters->perPage();
            return response(['status' => "success", 'statuscode' => 200,'message' => __('Clusters'), 'data' =>['clusters'=>$clusters->items(),'after'=>$after,'before'=>$before,'per_page'=>$per_page]], 200);
    	}catch(Exception $ex){
    		return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
    	}
    }

    /**
     * @SWG\Get(
     *     path="/banners",
     *     description="GET Banners",
     * tags={"Banners"},
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

    public static function getBannerList(Request $request,Banner $banner) {
        try{
            $dateznow = new DateTime("now", new DateTimeZone('UTC'));
            $datenow = $dateznow->format('Y-m-d');
            $banner = $banner->newQuery();
            $per_page = (isset($request->per_page)?$request->per_page:10);
            $banner->where(function($q) use($datenow) {
                  $q->where('end_date', '>=', $datenow)
                    ->orWhere('start_date', '>=', $datenow);
            })
            ->where(function($query) use ($request){
                if(Helper::is_mp2r()){
                    $sp_ids = Helper::getHigherDoctors();
                    $query->whereIn('sp_id',$sp_ids)->orWhere('banner_type','category')->orWhere('banner_type','class');
                }
            });
            $banners = $banner->where('enable',1)->orderBy('position','ASC')->cursorPaginate($per_page);
            foreach ($banners as $key => $banner) {
                // $banner->position = strval($banner->position);
                if($banner->banner_type=='category'){
                    $banner->category;
                    $subcategory = Category::where('parent_id',$banner->category_id)->where('enable','=','1')->count();
                    if($subcategory > 0){
                       $banner->category->is_subcategory = true;
                    }else{
                        $banner->category->is_subcategory = false;
                    }
                    $banner->category->is_filters = false;
                    if($banner->category->filters->count() > 0){
                        $banner->category->is_filters = true;
                    }
                }elseif ($banner->banner_type=='class') {
                    $banner->class;
                }elseif ($banner->banner_type=='service_provider') {
                    $banner->service_provider;
                }
            }
            $banner_raw = [];
            $after = null;
            if($banners->meta['next']){
                $after = $banners->meta['next']->target;
            }
            $before = null;
            if($banners->meta['previous']){
                $before = $banners->meta['previous']->target;
            }
            $per_page = $banners->perPage();
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('Banners'), 'data' =>['banners'=>$banners->items(),'after'=>$after,'before'=>$before,'per_page'=>$per_page]], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }     



    /**
     * @SWG\Get(
     *     path="/advertisement",
     *     description="GET Advertisements",
     * tags={"Banners"},
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

    public static function getAdvertiseMent(Request $request) {
        try{
            $user = Auth::user();
            $dateznow = new DateTime("now", new DateTimeZone('UTC'));
            $datenow = $dateznow->format('Y-m-d');
            $advertisements = Banner::where(function($q) use($datenow) {
                  $q->where('end_date', '>=', $datenow)
                    ->orWhere('start_date', '>=', $datenow);
            })->where([
                'banner_type'=>'service_provider',
                'sp_id'=>$user->id,
            ])->orderBy('position','ASC')->get();
            foreach ($advertisements as $key => $banner) {
                    $banner->service_provider;
            }
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('Advertisements'), 'data' =>['advertisements'=>$advertisements]], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    } 

    /**
     * @SWG\Get(
     *     path="/wallet-sp",
     *     description="Wallet Balance",
     * tags={"Service Provider"},
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

    public static function getWalletBalance(Request $request) {
        try{
            $user = Auth::user();
            $balance = 0;
            $wallet = Wallet::where('user_id',$user->id)->first();
            if($wallet){
                $balance = $wallet->balance;
            }
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('Wallet Balance'), 'data' =>['balance'=>$balance]], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }

    /**
     * @SWG\Get(
     *     path="/get-filters",
     *     description="Set Filters Selection For Service Provider",
     * tags={"Service Provider Side Filter"},
     *  @SWG\Parameter(
     *         name="category_id",
     *         in="query",
     *         type="string",
     *         description="Category Id",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="user_id",
     *         in="query",
     *         type="string",
     *         description="Service Provider ID",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="duties",
     *         in="query",
     *         type="string",
     *         description="duties for comma seprated",
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

    public static function getFiltersForServiceProvider(Request $request) {
        try{
            // $user = Auth::user();
            // if(!$user->hasrole('service_provider')){
            //     return response(array('status' => "error", 'statuscode' => 400, 'message' =>'Invalid role, must be as service_provider'), 400);
            // }
            $rules = [];
            if(isset($request->category_id)){
                $rules['category_id'] = "required|integer|exists:categories,id";
            }
            if(isset($request->duties)){
                $rules['duties'] = "required";
            }
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $input = $request->all();
            $filters = [];
            $user_id = isset($input['user_id'])?$input['user_id']:null;
            // $categoryData = $user->getCategoryData($user->id);
            if(isset($request->category_id)){
                   $filters = FilterType::getFiltersByCategory($request->category_id,$user_id);
            }

            if(isset($request->duties)){
                $duties = explode(',',$request->duties);
                $filters = \App\Model\MasterPreOptionFilter::getFiltersByDuties($duties);
            }

            return response(['status' => "success", 'statuscode' => 200,'message' => __('Filters'), 'data' =>['filters'=>$filters]], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }

    /**
     * @SWG\Post(
     *     path="/set-filters",
     *     description="Set Filters Selection For Service Provider",
     * tags={"Service Provider Side Filter"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="filters[]",
     *         in="query",
     *         type="array",
     *         description="filters[{'filter_id':1,'filter_option_ids':['1','2']}]",
     *         required=true,
    *           type="array",
    *          @SWG\Items(type="string"),
        *       collectionFormat="multi",
        *
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

    public static function setFiltersForServiceProvider(Request $request) {
        try{
            $user = Auth::user();
            if(!$user->hasrole('service_provider')){
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>'Invalid role, must be as service_provider'), 400);
            }
            $rules = [
                'filters' => "required|array|min:1",
                'filters' => "filled",
                'filters.*.filter_id' => 'required|integer|exists:filter_types,id',
                'filters.*.filter_option_ids' => 'required|array'
            ];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $input = $request->all();
            foreach ($input['filters'] as $key => $filter) {
                ServiceProviderFilterOption::where([
                    'sp_id'=>$user->id,
                    'filter_type_id'=>$filter['filter_id'],
                ])->delete();
                foreach ($filter['filter_option_ids'] as $filter_option_key => $filter_option) {
                    ServiceProviderFilterOption::firstOrCreate([
                        'sp_id'=>$user->id,
                        'filter_type_id'=>$filter['filter_id'],
                        'filter_option_id'=>$filter_option,
                    ]);
                }
            }
            return response(['status' => "success", 'statuscode' => 200,'message' => __('Filter Updated'), 'data' =>(object)[]], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }

    
    /**
     * @SWG\Get(
     *     path="/wallet-history-sp",
     *     description="Wallet History",
     * tags={"Service Provider"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="transaction_type",
     *         in="query",
     *         type="string",
     *         description="transaction_type e.g deposit,withdrawal,add_money,all default is all",
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
    public static function getWalletHistory(Request $request) {
    	try{
	    	$user = Auth::user();
	    	$payments = [];
            $transaction_type=null;
            if(isset($request->transaction_type) && $request->transaction_type!=='all'){
                $transaction_type = $request->transaction_type;
            }
            $per_page = (isset($request->per_page)?$request->per_page:10);
	    	$payments = Payment::where('to',$user->id)->
            whereHas('transaction', function ($query) use($transaction_type) {
                            if($transaction_type){
                                $query->where('transaction_type', $transaction_type);
                            }
                        })->orderBy('id', 'desc')->cursorPaginate($per_page);
	    	foreach ($payments as $key => $payment) {
	    		$payment->from = User::select('name','email','id','profile_image')->with('profile')->where('id',$payment->from)->first();
	    		$payment->to = User::select('name','email','id','profile_image')->with('profile')->where('id',$payment->to)->first();
	    		$transaction_type = \App\Model\Transaction::select('amount','transaction_type','status','closing_balance','request_id','payout_message')->where('id',$payment->transaction_id)->first();
                $payment->call_duration = null;
                $payment->service_type = null;
                if($transaction_type->requesthistory){
                    $payment->call_duration = $transaction_type->requesthistory->duration;
                    $payment->service_type = $transaction_type->requesthistory->request->servicetype->type;
                }
                $payment->amount = $transaction_type->amount;
                $payment->payout_message = $transaction_type->payout_message;
                $payment->type = $transaction_type->transaction_type;
                $payment->status = $transaction_type->status;
                $payment->closing_balance = $transaction_type->closing_balance;
	    	}
            $after = null;
            if($payments->meta['next']){
                $after = $payments->meta['next']->target;
            }
            $before = null;
            if($payments->meta['previous']){
                $before = $payments->meta['previous']->target;
            }
            $per_page = $payments->perPage();
	    	return response(['status' => "success", 'statuscode' => 200,
	                            'message' => __('Payment History'), 'data' =>['payments'=>$payments->items(),'after'=>$after,'before'=>$before,'per_page'=>$per_page]], 200);
    	}catch(Exception $ex){
    		return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
    	}
    }

    /**
     * @SWG\Get(
     *     path="/patient-list",
     *     description="getPatientList",
     * tags={"Service Provider"},
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
    public function getPatientList(Request $request,\App\Model\Request $requests){
        try{
            $user = Auth::user();
            $per_page = (isset($request->per_page)?$request->per_page:10);
            $requests = $requests->newQuery();
            $patients = $requests->select('*','booking_date as last_consult_date')->with('cus_info')
            ->where('to_user',$user->id)
            ->groupBy('from_user')->orderBy('id', 'asc')
                ->cursorPaginate($per_page);
            foreach ($patients as $key => $patient) {
                $patient->id = (int)$patient->cus_info->id;
                $patient->name = $patient->cus_info->name;
                $patient->email = $patient->cus_info->email;
                $patient->profile_image = $patient->cus_info->profile_image;
                unset($patient->cus_info);
                unset($patient->booking_date);
                unset($patient->from_user);
                unset($patient->to_user);
            }
            $after = null;
            if($patients->meta['next']){
                $after = $patients->meta['next']->target;
            }
            $before = null;
            if($patients->meta['previous']){
                $before = $patients->meta['previous']->target;
            }
            $per_page = $patients->perPage();
            return response(['status' => "success", 'statuscode' => 200,
                                    'message' => __('patients'), 'data' =>['patients'=>$patients->items(),'after'=>$after,'before'=>$before,'per_page'=>$per_page]], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }

    /**
     * @SWG\Get(
     *     path="/pending-request-by-date",
     *     description="getPendingRequestByDate",
     * tags={"Service Provider"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="date",
     *         in="query",
     *         type="string",
     *         description="date e.g YYYY-MM-DD=>2000-02-20",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="second_oponion",
     *         in="query",
     *         type="string",
     *         description="second_oponion true or false",
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
    public static function getPendingRequestByDate(Request $request) {
    	try{
	    	$user = Auth::user();
	    	$rules = ['date' => 'required|date|date_format:Y-m-d'];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
	    	$requests = [];
	    	$from_date = $request->date.' 00:00:00';
	    	$end_date = $request->date.' 23:59:59';
	    	$requests = \App\Model\Request::select('id','service_id','booking_date','from_user','to_user','request_type')
            ->whereHas('requesthistory', function ($query) {
        					$query->where('status', 'pending');
    					})
            ->when('request_type', function($query) use ($request){
                if(isset($request->second_oponion) && ($request->second_oponion===true || $request->second_oponion==='true')){
                    return $query->where('request_type','second_oponion');
                }else{
                    return $query->where('request_type','!=','second_oponion');
                }
            })
            ->where('to_user',$user->id)->whereBetween('booking_date', [$from_date, $end_date])->get();
	    	foreach ($requests as $key => $request_status) {
                $request_status->is_second_oponion = false;
                if($request_status->request_type=='second_oponion')
                    $request_status->is_second_oponion = true;
	    		$date = Carbon::parse($request_status->booking_date,'UTC')->setTimezone('Asia/Kolkata');
	    		$request_status->booking_date = $date->isoFormat('D MMMM YYYY, h:mm:ss a');
	    		$request_status->time = $date->isoFormat('h:mm a');
	    		$request_history = $request_status->requesthistory;
	    		$request_status->service_type = $request_status->servicetype->type;
	    		$request_status->status = $request_history->status;
	    		$request_status->from_user = User::select('name','email','id','profile_image','phone','country_code')->with(['profile'])->where('id',$request_status->from_user)->first();
	    		$request_status->to_user = User::select('name','email','id','profile_image','phone','country_code')->with(['profile'])->where('id',$request_status->to_user)->first();
	    		unset($request_status->requesthistory); 
	    		unset($request_status->service_id); 
	    		unset($request_status->servicetype);
	    	}
	    	return response(['status' => "success", 'statuscode' => 200,
	                            'message' => __('Pending Request '), 'data' =>['requests'=>$requests]], 200);
    	}catch(Exception $ex){
    		return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
    	}
    }

    /**
     * @SWG\Get(
     *     path="/appointment-dates",
     *     description="getAppointmentByMonthDates",
     * tags={"Service Provider"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="date",
     *         in="query",
     *         type="string",
     *         description="date e.g YYYY-MM-DD=>2000-02-20",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="type",
     *         in="query",
     *         type="string",
     *         description="upcoming,archived",
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
    public static function getAppointmentByMonthDates(Request $request) {
        try{
            $user = Auth::user();
            $rules = ['date' => 'required|date|date_format:Y-m-d'];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $requests = [];
            $d = [];
            $m = \Carbon\Carbon::parse($request->date)->format('m');
            $y = \Carbon\Carbon::parse($request->date)->format('Y');
            $requests = \App\Model\Request::select('id','service_id','booking_date','from_user','to_user','request_type')
            ->whereHas('requesthistory',function($query) use($request){
                if(isset($request->type) && $request->type=='upcoming'){
                    return $query->whereNotIn('status',['canceled','failed','completed']);
                }elseif (isset($request->type) && $request->type=='archived') {
                    return $query->whereIn('status',['canceled','failed','completed']);
                }else{
                    return $query->whereNotIn('status',['canceled','failed','completed']);
                }
            })->where('to_user',$user->id)->whereRaw('YEAR(booking_date) = ?',[$y])->whereRaw('MONTH(booking_date) = ?',[$m])->groupBy(DB::raw('Date(booking_date)'))->get();
            foreach ($requests as $key => $rq) {
                $d[] = \Carbon\Carbon::parse($rq->booking_date)->format('Y-m-d');
            }
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('Dates'), 'data' =>$d], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }
    /**
     * @SWG\Get(
     *     path="/requests",
     *     description="Get All Requests",
     * tags={"Service Provider"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="date",
     *         in="query",
     *         type="string",
     *         description="date e.g YYYY-MM-DD=>2000-02-20",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="service_type",
     *         in="query",
     *         type="string",
     *         description="service_type chat,call,all",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="service_id",
     *         in="query",
     *         type="string",
     *         description="service_id id of chat,call,all",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="second_oponion",
     *         in="query",
     *         type="string",
     *         description="second_oponion true or false",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="type",
     *         in="query",
     *         type="string",
     *         description="upcoming,archived",
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
    public static function getRequests(Request $request) {
    	try{
	    	$user = Auth::user();
            $isAprroved = false;
            if($user->account_verified){
                $isAprroved = true;
            }
            $from_date = null;
            $end_date = null;
            if(isset($request->date)){
                $rules = ['date' => 'required|date|date_format:Y-m-d'];
                $validator = Validator::make($request->all(),$rules);
                if ($validator->fails()) {
                    return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                        $validator->getMessageBag()->first()), 400);
                }
            }
            $timezone = $request->header('timezone');
            if(!$timezone){
                $timezone = 'Asia/Kolkata';
            }
            // print_r($request->second_oponion);die;
	    	$requests = [];
            $service_type = isset($request->service_type)?$request->service_type:'all';
            $service_id = isset($request->service_id)?$request->service_id:null;
            // Query 
            $per_page = (isset($request->per_page)?$request->per_page:10);
	    	$requests = \App\Model\Request::select('id','service_id','from_user','to_user','booking_date','created_at','booking_date as bookingDateUTC','request_type')
            ->whereHas('servicetype', function($query) use ($service_type,$service_id){
                if($service_type!=='all')
                    return $query->where('type', $service_type);
                if($service_id)
                    return $query->where('id', $service_id);

            })
            ->whereHas('requesthistory',function($query) use($request){
                if(isset($request->type) && $request->type=='upcoming'){
                    return $query->whereNotIn('status',['canceled','failed','completed']);
                }elseif (isset($request->type) && $request->type=='archived') {
                    return $query->whereIn('status',['canceled','failed','completed']);
                }
            })
            ->when('booking_date', function($query) use ($request,$timezone){
                if(isset($request->date)){
                    $from_date = $request->date.' 00:00:00';
                    $end_date = $request->date.' 23:59:59';
                    $fromUTC = Carbon::parse($from_date, $timezone)->setTimezone('UTC');
                    $toUTC = Carbon::parse($end_date, $timezone)->setTimezone('UTC');
                    return $query->whereBetween('booking_date', [$fromUTC, $toUTC]);
                }
            })
            ->when('request_type', function($query) use ($request){
                if(isset($request->second_oponion) && ($request->second_oponion===true || $request->second_oponion==='true')){
                    return $query->where('request_type','second_oponion');
                }else{
                    return $query->where('request_type','!=','second_oponion');
                }
            })
            ->where('to_user',$user->id)->orderBy('id', 'desc')->cursorPaginate($per_page);
	    	foreach ($requests as $key => $request_status) {
                $request_status->is_second_oponion = false;
                if($request_status->request_type=='second_oponion'){
                    $request_status->is_second_oponion = true;
                    $request_status->second_oponion = $request_status->getSecondOponion($request_status);
                }
                $request_status->is_prescription = false;    
                if($request_status->prescription){
                    $request_status->is_prescription = true;
                    unset($request_status->prescription);    
                }
                $dateznow = new DateTime("now", new DateTimeZone('UTC'));
                $datenow = $dateznow->format('Y-m-d H:i:s');
                if(\Config::get('client_connected') && (\Config::get('client_data')->domain_name=='healtcaremydoctor')){
                    $next_hour_time = strtotime($datenow);
                }else{
                    $next_hour_time = strtotime($datenow) + 3600;
                }
	    		$date = Carbon::parse($request_status->booking_date,'UTC')->setTimezone($timezone);
	    		$request_status->booking_date = $date->isoFormat('D MMMM YYYY, h:mm:ss a');
	    		$request_status->time = $date->isoFormat('h:mm a');
	    		$request_history = $request_status->requesthistory;
                if($request_history){
                    $request_status->price = $request_history->total_charges;
                    $request_status->duration = $request_history->duration;
    	    		$request_status->status = $request_history->status;
                    $request_status->schedule_type = $request_history->schedule_type;
                }
                if(strtotime($request_status->bookingDateUTC)>=$next_hour_time && $request_status->status=='pending'){
                    $request_status->canReschedule = true;
                    $request_status->canCancel = true;
                }else{
                    $request_status->canReschedule = false;
                    $request_status->canCancel = false;
                }
                $request_status->extra_detail = RequestData::getExtraRequestInfo($request_status->id,$timezone);
	    		$request_status->service_type = $request_status->servicetype->type;
	    		$request_status->from_user = User::select('name','email','id','profile_image','phone','country_code')->with(['profile'])->where('id',$request_status->from_user)->first();
	    		$request_status->to_user = User::select('name','email','id','profile_image','phone','country_code')->with(['profile'])->where('id',$request_status->to_user)->first();
                $request_status->to_user->categoryData = $request_status->to_user->getCategoryData($request_status->to_user->id);
	    		// unset($request_status->service_id); 
	    		unset($request_status->servicetype);
                $request_status = RequestData::getMoreData($request_status);
	    		unset($request_status->requesthistory); 
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
	                            'message' => __('Requests'), 'data' =>['requests'=>$requests->items(),'after'=>$after,'before'=>$before,'per_page'=>$per_page,'isAprroved'=>$isAprroved]], 200);
    	}catch(Exception $ex){
    		return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
    	}
    }



    /**
     * @SWG\Get(
     *     path="/request-detail",
     *     description="Get Request Detail",
     * tags={"Service Provider"},
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
    public  function getRequestDetailById(Request $request) {
        try{
            $user = Auth::user();
            $isAprroved = false;
            if($user->account_verified){
                $isAprroved = true;
            }
            $from_date = null;
            $end_date = null;
            $rules = ['request_id' => 'required|exists:requests,id'];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $request_status = \App\Model\Request::select('id','service_id','from_user','to_user','booking_date','created_at','booking_date as bookingDateUTC','request_type','total_hours','user_by_hours','verified_hours')->where('id',$request->request_id)->first();
            $request_status->is_second_oponion = false;
            if($request_status->request_type=='second_oponion'){
                $request_status->is_second_oponion = true;
                $request_status->second_oponion = $request_status->getSecondOponion($request_status);
            }
            $dateznow = new DateTime("now", new DateTimeZone('UTC'));
            $datenow = $dateznow->format('Y-m-d H:i:s');
            if(\Config::get('client_connected') && (\Config::get('client_data')->domain_name=='healtcaremydoctor')){
                $next_hour_time = strtotime($datenow);
            }else{
                $next_hour_time = strtotime($datenow) + 3600;
            }
            $request_history = $request_status->requesthistory;
            if($request_history){
                $request_status->price = $request_history->total_charges;
                $request_status->duration = $request_history->duration;
                $request_status->status = $request_history->status;
                $request_status->schedule_type = $request_history->schedule_type;
                $request_status->extra_payment = null;
                if($request_history->extra_payment_status){
                    $request_status->extra_payment = ['balance'=>$request_history->extra_payment,'status'=>$request_history->extra_payment_status,'description'=>$request_history->extra_payment_description,'created_at'=>$request_history->extra_payment_datetime];
                }
            }
            if(strtotime($request_status->bookingDateUTC)>=$next_hour_time && $request_status->status=='pending'){
                $request_status->canReschedule = true;
                $request_status->canCancel = true;
            }else{
                $request_status->canReschedule = false;
                $request_status->canCancel = false;
            }
            $request_status->is_prescription = false;    
            if($request_status->prescription){
                $request_status->is_prescription = true;
                unset($request_status->prescription);
                $request_status->pre_scription =  PreScription::where('request_id',$request_status->id)->orderBy("id","DESC")->first();
                if($request_status->pre_scription && $request_status->pre_scription->type=="digital"){
                    $request_status->pre_scription->medicines;
                    foreach ($request_status->pre_scription->medicines as $key => $medicine) {
                        $medicine->dosage_timing = json_decode($medicine->dosage_timing);
                    }
                }else if($request_status->pre_scription && $request_status->pre_scription->type=="manual"){
                    $request_status->pre_scription->images = ModelImage::where([
                        'module_table'=>'pre_scriptions',
                        'module_table_id'=>$request_status->pre_scription->id
                    ])->pluck('image_name')->toArray();
                }
            }
            $timezone = $request->header('timezone');
            if(!$timezone){
                $timezone = 'Asia/Kolkata';
            }
            $request_status->extra_detail = RequestData::getExtraRequestInfo($request_status->id,$timezone);
            $date = Carbon::parse($request_status->booking_date,'UTC')->setTimezone($timezone);
            $request_status->booking_date = $date->isoFormat('D MMMM YYYY, h:mm:ss a');
            $request_status->time = $date->isoFormat('h:mm a');
            $request_history = $request_status->requesthistory;
            if($request_history){
                $request_status->status = $request_history->status;
                $request_status->schedule_type = $request_history->schedule_type;
            }
            $request_status->last_location = RequestData::getLastLocation($request_status->id,$request_status->to_user);
            $request_status->from_user = User::select('name','email','id','profile_image','phone','country_code')->with(['profile'])->where('id',$request_status->from_user)->first();
            $request_status->to_user = User::select('name','email','id','profile_image','phone','country_code')->with(['profile'])->where('id',$request_status->to_user)->first();
            $request_status->to_user->categoryData = $request_status->to_user->getCategoryData($request_status->to_user->id);
            $request_status->from_user->master_preferences = \App\Model\MasterPreference::getMasterPreferences($request_status->from_user->id);
            $request_status = RequestData::getMoreData($request_status);
            unset($request_status->requesthistory); 
            unset($request_status->servicetype);
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('Request Detail'), 'data' =>['request_detail'=>$request_status]], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }

    /**
     * @SWG\Get(
     *     path="/doctor-list",
     *     description="getDoctorList",
     * tags={"Service Provider"},
     *  @SWG\Parameter(
     *         name="service_type",
     *         in="query",
     *         type="string",
     *         description="service_type e.g chat,call,all,consult_online, home_care, clinic_appointment",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="service_id",
     *         in="query",
     *         type="string",
     *         description="service_id",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="category_id",
     *         in="query",
     *         type="string",
     *         description="service provider category id",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="filter_option_ids",
     *         in="query",
     *         type="string",
     *         description="filter_option_ids comma sepreated option ids",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="search",
     *         in="query",
     *         type="string",
     *         description="search name of vendor",
     *         required=false,
     *     ),  
     *  @SWG\Parameter(
     *         name="lat",
     *         in="query",
     *         type="string",
     *         description="lattitude ",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="long",
     *         in="query",
     *         type="string",
     *         description="longitude",
     *         required=false,
     *     ),     
     *  @SWG\Parameter(
     *         name="time",
     *         in="query",
     *         type="string",
     *         description="start time",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="end_time",
     *         in="query",
     *         type="string",
     *         description="end time",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="date",
     *         in="query",
     *         type="string",
     *         description="dates comma seprated values",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="state",
     *         in="query",
     *         type="string",
     *         description="state name",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="duties",
     *         in="query",
     *         type="string",
     *         description="duties ids of preference_option_id",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="address_id",
     *         in="query",
     *         type="string",
     *         description="address_id of preference_option_id intely",
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
    public static function getDoctorList(Request $request,SpServiceType $subscription) {
        try{
            $doctors = [];
            $service_id = null;
            $service_ids = null;
            $service_type = null;
            $per_page = (isset($request->per_page)?$request->per_page:10);
            $cursor_Paginate =  true;
            if(\Config::get('client_connected') && (\Config::get('client_data')->domain_name=='mp2r')){
                $cursor_Paginate =  false;
                $request->radius = 50;
                $set_radius = \App\Model\EnableService::where('type','set_radius')->first();
                if($set_radius)
                    $request->radius = $set_radius->value;
                $per_page = (isset($request->per_page)?$request->per_page:10);
            }
            if(\Config::get('client_connected') && (\Config::get('client_data')->domain_name=='intely')){
                
                $request->radius = 20;
                $set_radius = \App\Model\EnableService::where('type','set_radius')->first();
                if($set_radius)
                    $request->radius = $set_radius->value;
            }
            if(\Config::get('client_connected') && (\Config::get('client_data')->domain_name=='healtcaremydoctor' || \Config::get('client_data')->domain_name=='heal')){
                $cursor_Paginate =  false;
                $request->radius = 100000000;
            }
            if(\Config::get('client_connected') && \Config::get('client_data')->domain_name=='physiotherapist'){
                $request->radius = 20;
                $set_radius = \App\Model\EnableService::where('type','set_radius')->first();
                if($set_radius)
                    $request->radius = $set_radius->value;
            }
            $pageNumber = (isset($request->page)?$request->page:1);
            $service_id = isset($request->service_id)?$request->service_id:null;
            $request->service_type = isset($request->service_type)?$request->service_type:'all';
            $input = $request->all();
            $subscription = $subscription->newQuery();
            $state_id = null;
            $state_name = null;
            if(isset($request->state)){
                $state_id = 0;
                $state_name = $request->state;
                $state = \App\Model\State::where('name',$request->state)->first();
                if($state){
                    $state_id = $state->id;
                } 
            }
            /* for Consultant Listing */
            $consultant_ids = User::whereHas('roles', function ($query) {
                           $query->where('name','service_provider');
                        })->orderBy('id','DESC')->pluck('id')->toArray();
            if(Config::get('client_connected') && Config::get("client_data")->domain_name=="intely"){
                $datenow = \Carbon\Carbon::now()->format('Y-m-d');
                $consultant_ids = \App\Model\CustomUserField::whereHas('customfield', function ($query) {
                        $query->where('field_name','Start Date');
                    })->where(function($query) use($datenow){
                        $query->where('field_value','<=',$datenow)->orWhereHas('user', function ($q) {
                        $q->where('manual_available',1);
                    });
                })->whereIn('user_id',$consultant_ids)->groupBy('user_id')->pluck('user_id')->toArray();

                if(count($consultant_ids)==0){
                    $consultant_ids = \App\Model\CustomUserField::whereHas('customfield', function ($query) {
                        $query->where('field_name','Start Date');
                        })->where(function($query) use($datenow){
                            $query->where('field_value','<=',$datenow)->orWhereHas('user', function ($q) {
                            $q->where('premium_enable','!=',NULL);
                        });
                    })->whereIn('user_id',$consultant_ids)->groupBy('user_id')->pluck('user_id')->toArray();                    
                }
                if(Auth::guard('api')->check()){
                    $log_user = Auth::guard('api')->user();
                    $consultant_ids = \App\Model\MasterPreference::getUsersByMasterPre($log_user->id,$consultant_ids);
                }
                if(isset($input['address_id'])){
                    $adrs = \App\Model\CustomInfo::where([
                        'info_type'=>'user_address',
                        'id'=>$input['address_id'],
                    ])->first();
                    if($adrs && $adrs->raw_detail){
                        $add_det = json_decode($adrs->raw_detail);
                        if(isset($add_det->save_as_preference) && $add_det->save_as_preference->id){
                            $consultant_ids = \App\Model\MasterPreference::getUsersByMasterPreById($add_det->save_as_preference->id,$consultant_ids);
                        }
                    }
                }
                if(isset($request->date) && isset($request->time) && isset($request->end_time)){
                    $timezone = $request->header('timezone');
                    if(!$timezone){$timezone = 'Asia/Kolkata';}
                    $dates = explode(',',$input['date']);
                    $datenow = Carbon::parse($dates[0].' '.$request->time,$timezone)->setTimezone('UTC')->format('Y-m-d H:i:s');
                    foreach ($dates as $key => $r_date) {
                        $consultant_ids = self::isSlotBooked($r_date,$request->time,$request->end_time,$timezone,$consultant_ids);
                    }
                }
            }
            if($request->service_type!='all'){
                if($request->service_type=='home_care'){
                    $request->service_type = 'Home Appointment';
                }else if($request->service_type=='clinic_appointment'){
                    $request->service_type = 'Clinic Appointment';
                }
                $service_type = Service::select('id')
                ->where(function($q) use($request) {
                              $q->where('type', $request->service_type)
                                ->orWhere('service_type', $request->service_type);
                })->first();
                if($service_type){
                    $service_ids[] = $service_type->id;
                }
                if($request->service_type=='consult_online'){
                   $service_ids = Service::select('id')->whereIn('type',['video call','call','audio','Call','Video Call','Audio'])->pluck('id')->toArray();
                }
                if(strtolower($request->service_type)=='emergency'){
                   $service_ids = Service::select('id')->pluck('id')->toArray();
                }
            }
            if($request->service_type!=='all'){
                $categoryservicetypeids = [];
                if(is_array($service_ids) && count($service_ids)>0){
                    $categoryservicetypeids = CategoryServiceType::whereIn('service_id',$service_ids)->pluck('id');
                }
                $subscription->whereIn('category_service_id',$categoryservicetypeids);
            }

            if($service_id!=null){
                $categoryservicetypeids = [];
                $categoryservicetypeids = CategoryServiceType::where('service_id',$service_id)->pluck('id');
                $subscription->whereIn('category_service_id',$categoryservicetypeids);
            }
            if($request->has('filter_option_ids')){
                $filter_option_ids = explode(",",$request->filter_option_ids);
                $consultant_ids = ServiceProviderFilterOption::whereIn('filter_option_id',$filter_option_ids)->whereIn('sp_id',$consultant_ids)->groupBy('sp_id')->pluck('sp_id');
            }

            if($request->has('course_id')){
                // $course_ids = explode(",",$request->course_id);
                $consultant_ids = SpCourse::where('course_id',$request->course_id)->groupBy('sp_id')->pluck('sp_id');
            }
            if($request->has('duties')){
                $duties = explode(",",$request->duties);
                $ids = [];
                foreach ($duties as $duty_id) {
                    if($duty_id){
                        $exist =  \App\Model\UserMasterPreference::whereIn('user_id',$consultant_ids)->where('preference_option_id',$duty_id)->pluck('user_id')->toArray();
                        $consultant_ids = $exist;
                    }
                }
            }
            // print_r($consultant_ids);die;
            $available = true;
            if($request->has('search')){
                if($request->search){
                    $available = false;
                    $consultant_ids = User::whereLike('name',$request->search)->whereIn('id',$consultant_ids)->groupBy('id')->pluck('id');
                }
            }
            if(strtolower($request->service_type)=='emergency'){
                $consultant_ids = \App\Model\CustomUserField::whereHas('customfield', function ($query) use($request) {
                    $query->where('field_name',$request->service_type);
                })->where('field_value','1')->whereIn('user_id',$consultant_ids)->pluck('user_id');
            }
            if(\Config::get('client_connected') && ((\Config::get('client_data')->domain_name=='mp2r') || \Config::get('client_data')->domain_name=='food') && $available){
                $consultant_ids = Helper::checkVendorsAvailableToday($consultant_ids);
            }
            /* For Nurse APP */
            if($request->has('lat') && $request->has('long') && isset($input['lat']) && isset($input['long']) &&  $input['lat']!==null && $input['long']!==null){
                $sqlDistance = DB::raw("( 111.045 * acos( cos( radians(" . $input['lat'] . ") )* cos( radians(  profiles.lat ) ) * cos( radians( profiles.long ) - radians(" . $input['long']  . ") ) + sin( radians(" . $input['lat']  . ") ) * sin( radians(  profiles.lat ) ) ) )");
                    $consultant_ids =  DB::table('profiles')
                    ->select('*')
                    ->selectRaw("{$sqlDistance} AS distance")
                    ->havingRaw('distance BETWEEN ? AND ?', [0,isset($request->radius)?$request->radius/100:50/100])
                    ->orderBy('distance',"DESC")
                    ->whereIn('user_id',$consultant_ids)->pluck('user_id')->toArray();
            }
            $timezone = $request->header('timezone');
            if(!$timezone){
                $timezone = 'Asia/Kolkata';
            }
            if ($request->has('category_id')) {
                if(\Config::get('client_connected') && ((\Config::get('client_data')->domain_name=='mp2r') || \Config::get('client_data')->domain_name=='food')){
                    $subcategories = [];
                    $category = Category::where([
                        'id'=>$request->category_id,
                        'parent_id'=>null
                    ])->first();
                    if($category){
                        $subcategories = Category::where([
                            'parent_id'=>$category->id,
                            'enable'=>'1'
                        ])->pluck('id')->toArray();
                    }
                    $subcategories[] = $request->category_id;
                    $subscription->whereHas('categoryServiceProvider', function($q) use($subcategories){
                            $q->whereIn('category_id',$subcategories);
                    });
                }else{
                        $subscription->whereHas('categoryServiceProvider', function($q) use($request){
                            if(isset($request->category_id)){
                                $q->where('category_id',$request->category_id);
                            }
                        });
                }
            }
            if(Helper::checkFeatureExist(['client_id'=>\Config::get('client_id'),'feature_name'=>'monthly plan'])){
                if(Auth::guard('api')->check() && Helper::chargeFromSP()){
                    $user = Auth::guard('api')->user();
                    if($user->hasrole('customer')){
                        $sp_data  = Helper::getDocotorInsuranceByUser($user->id,$consultant_ids);
                        if($sp_data['check']){
                            $consultant_ids = $sp_data['sp_ids'];
                        }

                    }
                }
                $consultant_ids  = Helper::getPaidDoctors($consultant_ids);
            }
            if(\Config::get('client_connected') && ((\Config::get('client_data')->domain_name=='mp2r') || \Config::get('client_data')->domain_name=='food') && $available){
                $consultant_ids = Helper::checkVendorsAvailableToday($consultant_ids);
            }
            // print_r($consultant_ids);die;
            $subscription->whereIn('sp_id',$consultant_ids);
            // print_r($consultant_ids);die;
            $subscription->where('available','1')->with('doctor_data')->groupBy('sp_id')
            ->whereHas('doctor_data', function($query){
                    return $query->where('account_verified','!=',null);
            })
            ->whereHas('doctor_data.roles', function($query){
                    return $query->where('name','service_provider');
            });
            if(is_object($consultant_ids))
                $consultant_ids = $consultant_ids->toArray();
            if(!$cursor_Paginate){
                $subscription->join('profiles as pp', 'pp.user_id', '=', 'sp_service_types.sp_id');
                if($state_id!==null){
                        $subscription->where(function($q) use($state_id,$state_name) {
                              $q->where('pp.state', $state_id)
                                ->orWhere('pp.state', $state_name);
                        });
                }
                $subscription->select('sp_service_types.*','pp.id AS profile_id');
                $subscription->orderBy('pp.rating', 'DESC');
                $subscription->orderByRaw('FIELD(sp_id,'.implode(",", $consultant_ids).')');
                $doctors = $subscription->paginate($per_page, ['*'], 'page', $pageNumber);
            }else{
               $doctors = $subscription->orderBy('id','asc')->cursorPaginate($per_page);
            }
            $unit_price = EnableService::where('type','unit_price')->first();
            $slot_duration = EnableService::where('type','slot_duration')->first();
            foreach ($doctors as $key => $doctor) {
                $user_table = User::find($doctor->doctor_data->id);
                $doctor->doctor_data->filters = $user_table->getFilters($user_table->id);
                $doctor->doctor_data->selected_filter_options = $user_table->getSelectedFiltersByCategory($user_table->id);
                if(Config::get('client_connected') && Config::get("client_data")->domain_name=="intely"){
                    $doctor->fixed_price = false; 
                    if(isset($doctor->doctor_data->selected_filter_options[0]) && $doctor->doctor_data->selected_filter_options[0]['price']){
                        $doctor->fixed_price = true; 
                        $doctor->price = $doctor->doctor_data->selected_filter_options[0]['price'];
                    }else{
                        if($doctor->category_service_type->price_fixed){
                            $doctor->fixed_price = true; 
                            $doctor->price = $doctor->category_service_type->price_fixed; 
                        }
                    }
                }
                $doctor->unit_price = $unit_price->value * 60; 
                $user_table->profile; 
                $doctor->doctor_data->categoryData = $user_table->getCategoryData($doctor->doctor_data->id);
                $doctor->doctor_data->additionals = $user_table->getAdditionals($doctor->doctor_data->id);
                $doctor->doctor_data->insurances = $user_table->getInsurnceData($doctor->doctor_data->id);
                $doctor->doctor_data->subscriptions = $user_table->getSubscription($user_table);
                $doctor->doctor_data->custom_fields = $user_table->getCustomFields($user_table->id);
                $doctor->doctor_data->patientCount = User::getTotalRequestDone($doctor->doctor_data->id);
                $doctor->doctor_data->reviewCount = Feedback::reviewCountByConsulatant($user_table->id);
                $doctor->doctor_data->account_verified = ($user_table->account_verified)?true:false;
                $doctor->doctor_data->totalRating = 0;
                if(isset($doctor->category_service_type) && isset($doctor->category_service_type->service)){
                    $doctor->service_type = $doctor->category_service_type->service->type;
                    $doctor->main_service_type = $doctor->category_service_type->service->service_type;
                    unset($doctor->category_service_type);
                }
                if($user_table->profile){
                    $doctor->doctor_data->profile->bio = $user_table->profile->about;
                    $doctor->doctor_data->totalRating = $user_table->profile->rating;
                    $doctor->doctor_data->profile->location = ["name"=>$user_table->profile->location_name,"lat"=>$user_table->profile->lat,"long"=>$user_table->profile->long];
                }
                $doctor->doctor_data = Helper::getMoreData($doctor->doctor_data);
            }
            $after = null;
            $before = null;
            $next_page = null;
            $pre_page = null;
            if(!$cursor_Paginate){
                if($doctors->hasMorePages()){
                    $next_page = $doctors->currentPage() + 1;
                }
                $pre_page = $doctors->currentPage() - 1;
                $per_page = $doctors->perPage();
                return response([
                    'status' => "success",
                    'statuscode' => 200,
                    'message' => __('Doctor List '),
                    'data' =>[
                        'doctors'=>$doctors->items(),
                        'after'=>$after,
                        'before'=>$before,
                        'per_page'=>$per_page,
                        'next_page'=>$next_page,
                        'pre_page'=>$pre_page
                    ]], 200);
            }else{
                if($doctors->meta['next']){
                    $after = $doctors->meta['next']->target;
                }
                if($doctors->meta['previous']){
                    $before = $doctors->meta['previous']->target;
                }
                $per_page = $doctors->perPage();
                return response(['status' => "success", 'statuscode' => 200,
                                    'message' => __('Doctor List '), 'data' =>[
                                        'doctors'=>$doctors->items(),
                                        'after'=>$after,
                                        'before'=>$before,
                                        'per_page'=>$per_page,
                                        'next_page'=>$next_page,
                                        'pre_page'=>$pre_page,
                                    ]], 200);
            }
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage().' '.$ex->getLine()], 500);
        }
    }

    private static function isSlotBooked($date,$start_time,$end_time,$timezone,$consultant_ids){
        $start_date_time = Carbon::parse($date.' '.$start_time,$timezone)->addMinutes(159)->setTimezone('UTC')->format('Y-m-d H:i:s');
        $end_date_time = Carbon::parse($date.' '.$end_time,$timezone)->addMinutes(159)->setTimezone('UTC')->format('Y-m-d H:i:s');
        $exist = \App\Model\RequestDate::where(function($query) use($start_date_time,$end_date_time){
            $query->whereBetween('start_date_time',[$start_date_time,$end_date_time])
            ->orWhereBetween('end_date_time',[$start_date_time,$end_date_time]);
        })->whereHas('requesthistory', function ($query){
            $query->where('status','!=','canceled');
            $query->where('status','!=','failed');
        })->whereHas('request', function ($query) use($consultant_ids) {
            $query->whereIn('to_user',$consultant_ids);
        })->groupBy('request_id')->pluck('request_id')->toArray();
        if(count($exist)>0){
           $ids = \App\Model\Request::whereIn('id',$exist)->groupBy('to_user')->pluck('to_user')->toArray();
           if(count($ids)>0){
                $consultant_ids = array_diff($consultant_ids,$ids);
           }
        }
        return array_unique($consultant_ids);
    } 



    /**
     * @SWG\Get(
     *     path="/sp-list",
     *     description="getSPList",
     * tags={"Service Provider"},
     *  @SWG\Parameter(
     *         name="service_type",
     *         in="query",
     *         type="string",
     *         description="service_type e.g chat,call,all,consult_online, home_care, clinic_appointment",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="service_id",
     *         in="query",
     *         type="string",
     *         description="service_id",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="category_id",
     *         in="query",
     *         type="string",
     *         description="service provider category id",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="filter_option_ids",
     *         in="query",
     *         type="string",
     *         description="filter_option_ids comma sepreated option ids",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="search",
     *         in="query",
     *         type="string",
     *         description="search name of vendor",
     *         required=false,
     *     ),  
     *  @SWG\Parameter(
     *         name="lat",
     *         in="query",
     *         type="string",
     *         description="lattitude ",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="long",
     *         in="query",
     *         type="string",
     *         description="longitude",
     *         required=false,
     *     ),     
     *  @SWG\Parameter(
     *         name="state",
     *         in="query",
     *         type="string",
     *         description="state name",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="city",
     *         in="query",
     *         type="string",
     *         description="city name",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="zip_code",
     *         in="query",
     *         type="string",
     *         description="Zip Code",
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
    public static function getSPList(Request $request,SpServiceType $subscription) {
        try{
            $doctors = [];
            $service_id = null;
            $service_ids = null;
            $service_type = null;
            $per_page = (isset($request->per_page)?$request->per_page:10);
            // print_r($per_page);die;
            $request->radius = 1000;
            $pageNumber = (isset($request->page)?$request->page:1);
            $service_id = isset($request->service_id)?$request->service_id:null;
            $request->service_type = isset($request->service_type)?$request->service_type:'all';
            $input = $request->all();
            $subscription = $subscription->newQuery();
            $state_id = null;
            $state_name = null;
            $city_id = null;
            $city_name = null;
            $zip_code = null;
            if(isset($request->state)){
                $state_name = $request->state;
                $request->radius = 10000;
                $state_id = 0;
                $state = \App\Model\State::where('name',$request->state)->first();
                if($state){
                    $state_id = $state->id;
                } 
            }
            if(isset($request->city)){
                $city_name = $request->city;
                $request->radius = 10000;
                $city_id = 0;
                $city = \App\Model\City::where('name',$request->city)->first();
                if($city){
                    $city_id = $city->id;
                } 
            }
            if(isset($request->zip_code)){
                $request->radius = 10000;
                $zip_code = $request->zip_code; 
            }
            $user = Auth::user();
            /* for Consultant Listing */
            if($user){
                $consultant_ids = User::whereHas('roles', function ($query) {
                           $query->where('name','service_provider');
                        })->where('id','!=',$user->id)->orderBy('id','DESC')->pluck('id')->toArray();
            }else{
                $consultant_ids = User::whereHas('roles', function ($query) {
                           $query->where('name','service_provider');
                        })->orderBy('id','DESC')->pluck('id')->toArray();
            }
            if($request->service_type!='all'){
                if($request->service_type=='home_care'){
                    $request->service_type = 'Home Appointment';
                }else if($request->service_type=='clinic_appointment'){
                    $request->service_type = 'Clinic Appointment';
                }
                $service_type = Service::select('id')->where('type',$request->service_type)->first();
                if($service_type){
                    $service_ids[] = $service_type->id;
                }
                if($request->service_type=='consult_online'){
                   $service_ids = Service::select('id')->whereIn('type',['video call','call','audio','Call','Video Call','Audio'])->pluck('id')->toArray();
                }
            }
            if($request->service_type!=='all'){
                $categoryservicetypeids = [];
                if(is_array($service_ids) && count($service_ids)>0){
                    $categoryservicetypeids = CategoryServiceType::whereIn('service_id',$service_ids)->pluck('id');
                }
                $subscription->whereIn('category_service_id',$categoryservicetypeids);
            }

            if($service_id!=null){
                $categoryservicetypeids = [];
                $categoryservicetypeids = CategoryServiceType::where('service_id',$service_id)->pluck('id');
                $subscription->whereIn('category_service_id',$categoryservicetypeids);
            }
            if($request->has('filter_option_ids')){
                $filter_option_ids = explode(",",$request->filter_option_ids);
                $consultant_ids = ServiceProviderFilterOption::whereIn('filter_option_id',$filter_option_ids)->whereIn('sp_id',$consultant_ids)->groupBy('sp_id')->pluck('sp_id');
            }
            if($request->has('search')){
                if($request->search){
                    $consultant_ids = User::whereLike('name', $request->search)->whereIn('id',$consultant_ids)->groupBy('id')->pluck('id');
                }
            }
            /* For Nurse APP */
            if($request->has('lat') && $request->has('long') && isset($input['lat']) && isset($input['long']) &&  $input['lat']!==null && $input['long']!==null){
                $sqlDistance = DB::raw("( 111.045 * acos( cos( radians(" . $input['lat'] . ") )* cos( radians(  profiles.lat ) ) * cos( radians( profiles.long ) - radians(" . $input['long']  . ") ) + sin( radians(" . $input['lat']  . ") ) * sin( radians(  profiles.lat ) ) ) )");
                    $consultant_ids =  DB::table('profiles')
                    ->select('*')
                    ->selectRaw("{$sqlDistance} AS distance")
                    ->havingRaw('distance BETWEEN ? AND ?', [0,isset($request->radius)?$request->radius/100:50/100])
                    ->orderBy('distance',"DESC")
                    ->whereIn('user_id',$consultant_ids)->pluck('user_id')->toArray();
            }
            $timezone = $request->header('timezone');
            if(!$timezone){
                $timezone = 'Asia/Kolkata';
            }
            if ($request->has('category_id')) {
                if(\Config::get('client_connected') && ((\Config::get('client_data')->domain_name=='mp2r') || \Config::get('client_data')->domain_name=='food')){
                    $subcategories = [];
                    $category = Category::where([
                        'id'=>$request->category_id,
                        'parent_id'=>null
                    ])->first();
                    if($category){
                        $subcategories = Category::where([
                            'parent_id'=>$category->id,
                            'enable'=>'1'
                        ])->pluck('id')->toArray();
                    }
                    $subcategories[] = $request->category_id;
                    $subscription->whereHas('categoryServiceProvider', function($q) use($subcategories){
                            $q->whereIn('category_id',$subcategories);
                    });
                }else{
                        $subscription->whereHas('categoryServiceProvider', function($q) use($request){
                            if(isset($request->category_id)){
                                $q->where('category_id',$request->category_id);
                            }
                        });
                }
            }
            if(Helper::checkFeatureExist(['client_id'=>\Config::get('client_id'),'feature_name'=>'monthly plan'])){
                $consultant_ids  = Helper::getPaidDoctors($consultant_ids);
            }
            if($zip_code!==null){
                $consultant_ids = \App\Model\CustomUserField::whereHas('customfield', function ($query) {
                    $query->where('field_name','Zip Code');
                })->where('field_value',$zip_code)->whereIn('user_id',$consultant_ids)->pluck('user_id')->toArray();
            }
            // print_r($consultant_ids);die;
            $subscription->whereIn('sp_id',$consultant_ids);
            // print_r($consultant_ids);die;
            $subscription->where('available','1')->with('doctor_data')->groupBy('sp_id')
            ->whereHas('doctor_data', function($query){
                    return $query->where('account_verified','!=',null);
            })
            ->whereHas('doctor_data.roles', function($query){
                    return $query->where('name','service_provider');
            });
            $subscription->join('profiles as pp', 'pp.user_id', '=', 'sp_service_types.sp_id');
            if($state_id!==null){
                // print_r($state_id);die;
                $subscription->where(function($q) use($state_id,$state_name) {
                      $q->where('pp.state', $state_id)
                        ->orWhere('pp.state', $state_name);
                });
            }
            if($city_id!==null){
                $subscription->where(function($q) use($city_id,$city_name) {
                      $q->where('pp.city', $city_id)
                        ->orWhere('pp.city', $city_name);
                });
            }
            $subscription->select('sp_service_types.*','pp.id AS profile_id');
            $subscription->orderBy('pp.rating', 'DESC');
            $subscription->orderByRaw('FIELD(sp_id,'.implode(",", $consultant_ids).')');
            $doctors = $subscription->paginate($per_page);
            foreach ($doctors as $key => $doctor) {
                $user_table = User::find($doctor->doctor_data->id);
                $user_table->profile; 
                $doctor->doctor_data->categoryData = $user_table->getCategoryData($doctor->doctor_data->id);
                $doctor->doctor_data->additionals = $user_table->getAdditionals($doctor->doctor_data->id);
                $doctor->doctor_data->insurances = $user_table->getInsurnceData($doctor->doctor_data->id);
                $doctor->doctor_data->filters = $user_table->getFilters($user_table->id);
                $doctor->doctor_data->subscriptions = $user_table->getSubscription($user_table);
                $doctor->doctor_data->custom_fields = $user_table->getCustomFields($user_table->id);
                $doctor->doctor_data->patientCount = User::getTotalRequestDone($doctor->doctor_data->id);
                $doctor->doctor_data->reviewCount = Feedback::reviewCountByConsulatant($user_table->id);
                $doctor->doctor_data->account_verified = ($user_table->account_verified)?true:false;
                $doctor->doctor_data->totalRating = 0;
                if(isset($doctor->category_service_type) && isset($doctor->category_service_type->service)){
                    $doctor->service_type = $doctor->category_service_type->service->type;
                    unset($doctor->category_service_type);
                }
                if($user_table->profile){
                    $doctor->doctor_data->profile->bio = $user_table->profile->about;
                    $doctor->doctor_data->totalRating = $user_table->profile->rating;
                    $doctor->doctor_data->profile->location = ["name"=>$user_table->profile->location_name,"lat"=>$user_table->profile->lat,"long"=>$user_table->profile->long];
                }
                $doctor->doctor_data = Helper::getMoreData($doctor->doctor_data);
            }
            $after = null;
            $before = null;
            if($doctors->hasMorePages()){
                $next_page = $doctors->currentPage() + 1;
            }else{
                $next_page = 0;
            }
            $pre_page = $doctors->currentPage() - 1;
            $per_page = $doctors->perPage();
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('Doctor List '), 'data' =>['doctors'=>$doctors->items(),'after'=>$after,'before'=>$before,'per_page'=>$per_page,'next_page'=>$next_page,'pre_page'=>$pre_page]], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage().' '.$ex->getLine()], 500);
        }
    }  


    /**
     * @SWG\Get(
     *     path="/recent-view",
     *     description="get Recent List",
     * tags={"Service Provider"},
     *  @SWG\Parameter(
     *         name="after",
     *         in="query",
     *         type="string",
     *         description=" after id",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="before",
     *         in="query",
     *         type="string",
     *         description=" before id",
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
    public static function getRecentList(Request $request,RecentView $subscription) {
        try{
            $doctors = [];
            $service_id = null;
            $service_ids = null;
            $service_type = null;
            $per_page = (isset($request->per_page)?$request->per_page:20);
            // print_r($per_page);die;
            $input = $request->all();
            $subscription = $subscription->newQuery();
            $user = Auth::user();
            /* for Consultant Listing */
            $consultant_ids = User::whereHas('roles', function ($query) {
                       $query->where('name','service_provider');
                    })->orderBy('id','DESC')->pluck('id')->toArray();
            $subscription->where([
                'user_id'=>$user->id,
                'type'=>'expert',
            ])->whereIn('whose_id',$consultant_ids);
            $doctors = $subscription->orderBy('id','asc')->cursorPaginate($per_page);
            foreach ($doctors as $key => $doctor) {
                $user_table = User::find($doctor->doctor_data->id);
                $user_table->profile; 
                $doctor->doctor_data->categoryData = $user_table->getCategoryData($doctor->doctor_data->id);
                $doctor->doctor_data->additionals = $user_table->getAdditionals($doctor->doctor_data->id);
                $doctor->doctor_data->insurances = $user_table->getInsurnceData($doctor->doctor_data->id);
                $doctor->doctor_data->filters = $user_table->getFilters($user_table->id);
                $doctor->doctor_data->subscriptions = $user_table->getSubscription($user_table);
                $doctor->doctor_data->custom_fields = $user_table->getCustomFields($user_table->id);
                $doctor->doctor_data->patientCount = User::getTotalRequestDone($doctor->doctor_data->id);
                $doctor->doctor_data->reviewCount = Feedback::reviewCountByConsulatant($user_table->id);
                $doctor->doctor_data->account_verified = ($user_table->account_verified)?true:false;
                $doctor->doctor_data->totalRating = 0;
                if(isset($doctor->category_service_type) && isset($doctor->category_service_type->service)){
                    $doctor->service_type = $doctor->category_service_type->service->type;
                    unset($doctor->category_service_type);
                }
                if($user_table->profile){
                    $doctor->doctor_data->profile->bio = $user_table->profile->about;
                    $doctor->doctor_data->totalRating = $user_table->profile->rating;
                    $doctor->doctor_data->profile->location = ["name"=>$user_table->profile->location_name,"lat"=>$user_table->profile->lat,"long"=>$user_table->profile->long];
                }
                $doctor->doctor_data = Helper::getMoreData($doctor->doctor_data);
            }
            if($doctors->meta['next']){
                $after = $doctors->meta['next']->target;
            }
            if($doctors->meta['previous']){
                $before = $doctors->meta['previous']->target;
            }
            $after = null;
            $before = null;
            $per_page = $doctors->perPage();
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('Doctor List '), 
                                'data' =>[
                                    'doctors'=>$doctors->items(),
                                    'after'=>$after,
                                    'before'=>$before,
                                    'per_page'=>$per_page,
                                ]], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage().' '.$ex->getLine()], 500);
        }
    }


/**
     * @SWG\Get(
     *     path="/auto-allocate",
     *     description="get Doctor Detail",
     * tags={"Service Provider"},

     *  @SWG\Parameter(
     *         name="service_id",
     *         in="query",
     *         type="string",
     *         description="service_id",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="category_id",
     *         in="query",
     *         type="string",
     *         description="service provider category id",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="search",
     *         in="query",
     *         type="string",
     *         description="search name of vendor",
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
    public static function getDoctorData(Request $request,SpServiceType $subscription) {
        try{
            $doctors = [];
            $service_id = null;
            $per_page = (isset($request->per_page)?$request->per_page:10);
            $service_id = isset($request->service_id)?$request->service_id:null;
            $request->service_type = isset($request->service_type)?$request->service_type:'all';
            $rules = ['category_id' => 'required|exists:categories,id'];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            if($request->service_type!='all'){
                $service_type = Service::select('id')->where('type',$request->service_type)->first();
                if($service_type)
                    $service_id = $service_type->id;
            }
            $subscription = $subscription->newQuery();
            if($service_id!=null){
                $categoryservicetypeids = [];
                $categoryservicetypeids = CategoryServiceType::where('service_id',$service_id)->pluck('id');
                $subscription->whereIn('category_service_id',$categoryservicetypeids);
            }
            if($request->has('filter_option_ids')){
                $filter_option_ids = explode(",",$request->filter_option_ids);
                $consultant_ids = [];
                $consultant_ids = ServiceProviderFilterOption::whereIn('filter_option_id',$filter_option_ids)->groupBy('sp_id')->pluck('sp_id');
                $subscription->whereIn('sp_id',$consultant_ids);
            }
            if($request->has('search')){
                if($request->search){
                    $consultant_ids = [];
                    $consultant_ids = User::whereLike('name', $request->search)->groupBy('id')->pluck('id');
                    $subscription->whereIn('sp_id',$consultant_ids);
                }
            }
            if ($request->has('category_id')) {
                $subscription->whereHas('categoryServiceProvider', function($q) use($request){
                    if(isset($request->category_id))
                        $q->where('category_id',$request->category_id);
                });
            }
            $doctor = $subscription->where('available','1')->with('doctor_data')->groupBy('sp_id')
            ->whereHas('doctor_data', function($query){
                    return $query->where('account_verified','!=',null);
            })
            ->orderByRaw("RAND()")
            ->first();
            if($doctor)
                $doctor = User::getDoctorDetail($doctor->doctor_data->id);
            return response(['status' => "success", 'statuscode' => 200,
                                    'message' => __('Doctor Detail '), 'data' =>['dcotor_detail'=>$doctor]], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }

    /**
     * @SWG\Get(
     *     path="/services",
     *     description="Get Services From Category",
     * tags={"Service Provider"},
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *  @SWG\Parameter(
     *         name="category_id",
     *         in="query",
     *         type="string",
     *         description="category id",
     *         required=false,
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     */
    public static function getServiceList(Request $request) {
        try{
            $services = [];
            $service_id = null;
            $per_page = (isset($request->per_page)?$request->per_page:10);
            $rules = ['category_id' => 'required'];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $input = $request->all();
            $unit_price = EnableService::where('type','unit_price')->first();
            $service_ids = Service::where('enable',1)->pluck('id')->toArray();
            $slot_duration = EnableService::where('type','slot_duration')->first();
            // print_r($service_ids);die;
            $services = CategoryServiceType::where([
                'category_id'=>$input['category_id'],
                'is_active'=>"1",
            ])->whereIn('service_id',$service_ids)->orderBy('id', 'asc')
            ->cursorPaginate($per_page);
            $services_data = [];
            foreach ($services as $key => $categoryservice) {
                if($categoryservice->service){
                    $categoryservice->unit_price = $unit_price->value * 60;
                    if(\Config::get('client_connected') && \Config::get("client_data")->domain_name=="healtcaremydoctor"){
                        $categoryservice->fixed_price = false; 
                        if($categoryservice->price_fixed){
                            $categoryservice->fixed_price = true;
                            $categoryservice->unit_price = $slot_duration->value * 60; 
                        }
                   } 
                    $categoryservice->name = $categoryservice->service->type;
                    $categoryservice->main_service_type = $categoryservice->service->service_type;
                    $categoryservice->color_code = $categoryservice->service->color_code;
                    $categoryservice->description = $categoryservice->service->description;
                    $categoryservice->need_availability = $categoryservice->service->need_availability;
                    $categoryservice->price_type = null;
                    if($categoryservice->price_fixed!==null){
                        $categoryservice->price_type = 'fixed_price';
                        unset($categoryservice->price_minimum);
                        unset($categoryservice->price_maximum);
                    }else{
                        unset($categoryservice->price_fixed);
                        $categoryservice->price_type = 'price_range';
                    }
                    unset($categoryservice->service);
                    $services_data[] = $categoryservice; 
                }
            }
            $after = null;
            if($services->meta['next']){
                $after = $services->meta['next']->target;
            }
            $before = null;
            if($services->meta['previous']){
                $before = $services->meta['previous']->target;
            }
            $per_page = $services->perPage();
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('Services List '), 'data' =>['services'=>$services->items(),'after'=>$after,'before'=>$before,'per_page'=>$per_page]], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }

    /**
     * @SWG\Get(
     *     path="/doctor-detail",
     *     description="getDoctorDetail",
     * tags={"Service Provider"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="doctor_id",
     *         in="query",
     *         type="string",
     *         description="doctor_id",
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
    public static function getDoctorDetailById(Request $request) {
        try{
            $rules = ['doctor_id' => 'required'];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $doctor = User::getDoctorDetail($request->doctor_id);
            if($doctor){
                if(Auth::guard('api')->check() && Auth::guard('api')->user()){

                    $user = Auth::guard('api')->user();
                    \App\Model\RecentView::where([
                        'user_id'=>$user->id,
                        'whose_id'=>$request->doctor_id,
                        'type'=>'expert'
                    ])->delete();

                    $recentview = New \App\Model\RecentView();
                    $recentview->user_id = $user->id;
                    $recentview->whose_id = $request->doctor_id;
                    $recentview->type = 'expert';
                    $recentview->save();
                }
                return response(['status' => "success", 'statuscode' => 200,
                                    'message' => __('Doctor List '), 'data' =>['dcotor_detail'=>$doctor]], 200);
            }else{
                return response(['status' => "error", 'statuscode' => 400,
                                    'message' => __('Doctor Not Found ')], 400);
            }
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }

    /**
     * @SWG\Get(
     *     path="/get-slots",
     *     description="getDoctorDetail",
     * tags={"Service Provider"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="doctor_id",
     *         in="query",
     *         type="string",
     *         description="doctor_id",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="date",
     *         in="query",
     *         type="string",
     *         description="date 2010-01-20",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="applyoption",
     *         in="query",
     *         type="string",
     *         description="weekwise",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="service_id",
     *         in="query",
     *         type="string",
     *         description="service_id",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="category_id",
     *         in="query",
     *         type="string",
     *         description="category_id",
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
    public static function getSlotsByDates(Request $request) {
        try{
            $input = $request->all();
            if(\Config::get('client_connected') && \Config::get('client_data')->domain_name=='physiotherapist'){
                $rules = [
                    'doctor_id' => 'required|exists:users,id',
                    'category_id' => 'required|exists:categories,id',
                ];
            }else{
                $rules = [
                    'doctor_id' => 'required|exists:users,id',
                    'service_id' => 'required|exists:services,id',
                    'category_id' => 'required|exists:categories,id',
                ];
            }
            if(!isset($request->applyoption)){
                $rules['date'] = 'required|date_format:Y-m-d';
            }
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            if(!isset($input['service_id'])){
                $service = CategoryServiceType::where('category_id',$input['category_id'])->first();
                $input['service_id'] = $service->service_id;
            }
            $timezone = $request->header('timezone');
            if(!$timezone){
                $timezone = 'Asia/Kolkata';
            }
            if(isset($request->applyoption) && $request->applyoption=='weekwise'){
                 $sp_slot_array = [];
                 $actual_days = [];
                 $days = [0,1,2,3,4,5,6];
                 $single_day = 0;
                 foreach ($days as $key => $day) {
                    $sp_slot = ServiceProviderSlot::where([
                        'service_provider_id'=>$input['doctor_id'],
                        'service_id'=>$input['service_id'],
                        'day'=>$day,
                        'category_id'=>$input['category_id'],
                    ])->first();
                    if($sp_slot){
                        $single_day = $day;
                        $actual_days[] = true;
                    }else{
                        $actual_days[] = false;
                    }
                 }
                 $sp_slots = ServiceProviderSlot::where([
                        'service_provider_id'=>$input['doctor_id'],
                        'service_id'=>$input['service_id'],
                        'category_id'=>$input['category_id'],
                        'day'=>$single_day,
                    ])->get();
                 $array_of_time = array ();
                if($sp_slots->count()>0){
                    foreach ($sp_slots as $key => $sp_slot) {
                        $start_time_date = Carbon::parse($sp_slot->start_time,'UTC')->setTimezone($timezone);
                        $start_time = $start_time_date->isoFormat('h:mm a');
                        $end_time_date = Carbon::parse($sp_slot->end_time,'UTC')->setTimezone($timezone);
                        $end_time = $end_time_date->isoFormat('h:mm a');
                        $sp_slot_array[] = array('start_time'=>$start_time,'end_time'=>$end_time);
                    }
                }
                return response(['status' => "success", 'statuscode' => 200,'message' => __('Slot List '), 'data' =>['slots'=>$sp_slot_array,'interval'=>null,'date'=>null,'days'=>$actual_days]], 200);
            }else{
                $weekMap = ['SU'=>0,'MO'=>1,'TU'=>2,'WE'=>3,'TH'=>4,'FR'=>5,'SA'=>6];
                $feature = Helper::getClientFeatureExistWithFeatureType('Dynamic Sections','Master Interval');
                $duration_by_setting = true;
                $slots = [];
                $slot_duration = EnableService::where('type','slot_duration')->first();
                $add_mins  = 30 * 60;
                if($slot_duration){
                    $add_mins = $slot_duration->value * 60;
                }
                // print_r($feature);die;
                if($feature){
                    $slots =  Helper::getMasterSlots();
                }
                if(count($slots) > 0){
                    $duration_by_setting = false;
                    $sp_slots = $slots;
                }else{
                    $sp_slots = ServiceProviderSlotsDate::where([
                        'service_provider_id'=>$input['doctor_id'],
                        'service_id'=>$input['service_id'],
                        'date'=>$input['date'],
                        'category_id'=>$input['category_id'],
                    ])->get();
                    $sp_slot_array = [];
                    if($sp_slots->count()==0){
                        $day = strtoupper(substr(Carbon::parse($input['date'])->format('l'), 0, 2));
                        $day_number = $weekMap[$day];
                        $sp_slots = ServiceProviderSlot::where([
                            'service_provider_id'=>$input['doctor_id'],
                            'service_id'=>$input['service_id'],
                            'day'=>$day_number,
                            'category_id'=>$input['category_id'],
                        ])->get();

                    }
                }
                $dateznow = new DateTime("now", new DateTimeZone($timezone));
                $datenow = $dateznow->format('Y-m-d H:i:s');
                $current_date = $dateznow->format('Y-m-d');
                $currentTime    = strtotime ($datenow);
                // print_r($currentTime);die;
                // echo " current time $currentTime \n";
                $array_of_time = array ();
                if($sp_slots->count()>0){
                    foreach ($sp_slots as $key => $sp_slot) {
                        $start_time_date = Carbon::parse($sp_slot->start_time,'UTC')->setTimezone($timezone);
                        $start_time = $start_time_date->isoFormat('h:mm a');
                        $end_time_date = Carbon::parse($sp_slot->end_time,'UTC')->setTimezone($timezone);
                        $end_time = $end_time_date->isoFormat('h:mm a');
                        $starttime    = strtotime ($start_time); //change to strtotime
                        $endtime      = strtotime ($end_time); //change to strtotime
                        while ($starttime < $endtime) // loop between time
                        {
                           $time = date ("h:i a", $starttime);
                           $starttime_slot = date ("H:i:s", $starttime);
                           $starttime_slot_one_m = date ("H:i:s", $starttime + 60);
                           if($duration_by_setting){
                                $endDT = $starttime + $add_mins;
                                $end_time_new = date ("h:i a", $endtime);
                           }else{
                                $endDT = $endtime;
                                $end_time_new = date ("h:i a", $endtime);
                           }
                           // $starttime += $add_mins; // to check endtie=me
                           // $endtime_slot = date ("H:i:s", $starttime);

                           $endtime_slot = date("H:i:s", $endDT);
                           $start_time_slot_utcdate = Carbon::parse($input['date'].' '.$starttime_slot,$timezone)->setTimezone('UTC');
                           $starttime_slot_one_m = Carbon::parse($input['date'].' '.$starttime_slot_one_m,$timezone)->setTimezone('UTC');
                           $end_time_slot_utcdate = Carbon::parse($input['date'].' '.$endtime_slot,$timezone)->setTimezone('UTC');
                           // print_r($end_time_slot_utcdate);
                           $exist = \App\Model\Request::where('to_user',$input['doctor_id'])
                           // ->where('booking_date','<=',$end_time_slot_utcdate)
                            ->where('booking_date','=',$start_time_slot_utcdate)
                            ->orWhereBetween('booking_end_date',[$starttime_slot_one_m,$end_time_slot_utcdate])
                           ->whereHas('requesthistory', function ($query) {
                                $query->where('status','!=','canceled');
                            })
                           ->get();
                           $available = true;
                           if($exist->count()>0){
                                $available = false;
                           }
                           if(isset($sp_slot->working_today) && $sp_slot->working_today=='n'){
                                $available = false;
                           }
                            // print_r($input['date']);die;
                           if($current_date==$input['date'] && $starttime>=$currentTime){
                                $time = date ("h:i a", $starttime);
                                $array_of_time[] = ["time"=>$time,"end_time"=>$end_time_new,"available"=>$available];
                           }else if($input['date'] > $current_date){
                                $time = date ("h:i a", $starttime);
                                $array_of_time[] = ["time"=>$time,"end_time"=>$end_time_new,"available"=>$available];
                           }
                           if($duration_by_setting){
                                $starttime += $add_mins;
                           }else{
                                $starttime += 60*60;
                           }
                        }
                        $sp_slot_array[] = array('start_time'=>$start_time,'end_time'=>$end_time);
                    }
                }
                return response(['status' => "success", 'statuscode' => 200,'message' => __('Slot List '), 'data' =>['slots'=>$sp_slot_array,'interval'=>$array_of_time,'date'=>$input['date']]], 200);
            }
            
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage().' '.$ex->getLine()], 500);
        }
    }



    /**
     * @SWG\Get(
     *     path="/get-date-slots",
     *     description="getDoctorDetail",
     * tags={"Service Provider"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="doctor_id",
     *         in="query",
     *         type="string",
     *         description="doctor_id",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="date",
     *         in="query",
     *         type="string",
     *         description="date 2010-01-20",
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
    public static function getSlotsByDatesdoctor(Request $request) {
        try{
            $input = $request->all();
            //if(\Config::get('client_connected') && \Config::get('client_data')->domain_name=='iedu'){
                $rules = [
                    'doctor_id' => 'required|exists:users,id',
                    'date' =>  'required|date_format:Y-m-d',
                ];
            //}
            
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            
            $timezone = $request->header('timezone');
            if(!$timezone){
                $timezone = 'Asia/Kolkata';
            }
            if(isset($request->applyoption) && $request->applyoption=='weekwise'){
                 $sp_slot_array = [];
                 $actual_days = [];
                 $days = [0,1,2,3,4,5,6];
                 $single_day = 0;
                 foreach ($days as $key => $day) {
                    $sp_slot = ServiceProviderSlot::where([
                        'service_provider_id'=>$input['doctor_id'],
                        'day'=>$day,
                    ])->first();
                    if($sp_slot){
                        $single_day = $day;
                        $actual_days[] = true;
                    }else{
                        $actual_days[] = false;
                    }
                 }
                 $sp_slots = ServiceProviderSlot::where([
                        'service_provider_id'=>$input['doctor_id'],
                        'day'=>$single_day,
                    ])->get();
                 $array_of_time = array ();
                if($sp_slots->count()>0){
                    foreach ($sp_slots as $key => $sp_slot) {
                        $start_time_date = Carbon::parse($sp_slot->start_time,'UTC')->setTimezone($timezone);
                        $start_time = $start_time_date->isoFormat('h:mm a');
                        $end_time_date = Carbon::parse($sp_slot->end_time,'UTC')->setTimezone($timezone);
                        $end_time = $end_time_date->isoFormat('h:mm a');
                        $sp_slot_array[] = array('start_time'=>$start_time,'end_time'=>$end_time);
                    }
                }
                return response(['status' => "success", 'statuscode' => 200,'message' => __('Slot List '), 'data' =>['slots'=>$sp_slot_array,'interval'=>null,'date'=>null,'days'=>$actual_days]], 200);
            }else{
                $weekMap = ['SU'=>0,'MO'=>1,'TU'=>2,'WE'=>3,'TH'=>4,'FR'=>5,'SA'=>6];
                $feature = Helper::getClientFeatureExistWithFeatureType('Dynamic Sections','Master Interval');
                $duration_by_setting = true;
                $slots = [];
                $slot_duration = EnableService::where('type','slot_duration')->first();
                $add_mins  = 30 * 60;
                if($slot_duration){
                    $add_mins = $slot_duration->value * 60;
                }
                // print_r($feature);die;
                if($feature){
                    $slots =  Helper::getMasterSlots();
                }
                if(count($slots) > 0){
                    $duration_by_setting = false;
                    $sp_slots = $slots;
                }else{
                    $sp_slots = ServiceProviderSlotsDate::where([
                        'service_provider_id'=>$input['doctor_id'],
                        'date'=>$input['date'],
                    ])->get();
                    $sp_slot_array = [];
                    if($sp_slots->count()==0){
                        $day = strtoupper(substr(Carbon::parse($input['date'])->format('l'), 0, 2));
                        $day_number = $weekMap[$day];
                        $sp_slots = ServiceProviderSlot::where([
                            'service_provider_id'=>$input['doctor_id'],
                            'day'=>$day_number,
                        ])->get();

                    }
                }
                $dateznow = new DateTime("now", new DateTimeZone($timezone));
                $datenow = $dateznow->format('Y-m-d H:i:s');
                $current_date = $dateznow->format('Y-m-d');
                $currentTime    = strtotime ($datenow);
                // print_r($currentTime);die;
                // echo " current time $currentTime \n";
                $array_of_time = array ();
                if($sp_slots->count()>0){
                    foreach ($sp_slots as $key => $sp_slot) {
                        $start_time_date = Carbon::parse($sp_slot->start_time,'UTC')->setTimezone($timezone);
                        $start_time = $start_time_date->isoFormat('h:mm a');
                        $end_time_date = Carbon::parse($sp_slot->end_time,'UTC')->setTimezone($timezone);
                        $end_time = $end_time_date->isoFormat('h:mm a');
                        $starttime    = strtotime ($start_time); //change to strtotime
                        $endtime      = strtotime ($end_time); //change to strtotime
                        while ($starttime < $endtime) // loop between time
                        {
                           $time = date ("h:i a", $starttime);
                           $starttime_slot = date ("H:i:s", $starttime);
                           $starttime_slot_one_m = date ("H:i:s", $starttime + 60);
                           if($duration_by_setting){
                                $endDT = $starttime + $add_mins;
                                $end_time_new = date ("h:i a", $endtime);
                           }else{
                                $endDT = $endtime;
                                $end_time_new = date ("h:i a", $endtime);
                           }
                           // $starttime += $add_mins; // to check endtie=me
                           // $endtime_slot = date ("H:i:s", $starttime);

                           $endtime_slot = date("H:i:s", $endDT);
                           $start_time_slot_utcdate = Carbon::parse($input['date'].' '.$starttime_slot,$timezone)->setTimezone('UTC');
                           $starttime_slot_one_m = Carbon::parse($input['date'].' '.$starttime_slot_one_m,$timezone)->setTimezone('UTC');
                           $end_time_slot_utcdate = Carbon::parse($input['date'].' '.$endtime_slot,$timezone)->setTimezone('UTC');
                           // print_r($end_time_slot_utcdate);
                           $exist = \App\Model\Request::where('to_user',$input['doctor_id'])
                           // ->where('booking_date','<=',$end_time_slot_utcdate)
                            ->where('booking_date','=',$start_time_slot_utcdate)
                            ->orWhereBetween('booking_end_date',[$starttime_slot_one_m,$end_time_slot_utcdate])
                           ->whereHas('requesthistory', function ($query) {
                                $query->where('status','!=','canceled');
                            })
                           ->get();
                           $available = true;
                           if($exist->count()>0){
                                $available = false;
                           }
                           if(isset($sp_slot->working_today) && $sp_slot->working_today=='n'){
                                $available = false;
                           }
                            // print_r($input['date']);die;
                           if($current_date==$input['date'] && $starttime>=$currentTime){
                                $time = date ("h:i a", $starttime);
                                $array_of_time[] = ["time"=>$time,"end_time"=>$end_time_new,"available"=>$available];
                           }else if($input['date'] > $current_date){
                                $time = date ("h:i a", $starttime);
                                $array_of_time[] = ["time"=>$time,"end_time"=>$end_time_new,"available"=>$available];
                           }
                           if($duration_by_setting){
                                $starttime += $add_mins;
                           }else{
                                $starttime += 60*60;
                           }
                        }
                        $sp_slot_array[] = array('start_time'=>$start_time,'end_time'=>$end_time);
                    }
                }
                return response(['status' => "success", 'statuscode' => 200,'message' => __('Slot List '), 'data' =>['slots'=>$sp_slot_array,'interval'=>$array_of_time,'date'=>$input['date']]], 200);
            }
            
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage().' '.$ex->getLine()], 500);
        }
    }

    /**
     * @SWG\Get(
     *     path="/get-physio-slots",
     *     description="getCustomSlots",
     * tags={"Service Provider"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="date",
     *         in="query",
     *         type="string",
     *         description="date 2010-01-20",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="applyoption",
     *         in="query",
     *         type="string",
     *         description="weekwise",
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
    public  function getCustomSlots(Request $request) {
        try{
            $user = Auth::user();
            $input = $request->all();
            if(isset($request->date)){
                $rules['date'] = 'required|date_format:Y-m-d';
            }else{
                $rules['applyoption'] = 'required';
            }
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            // print_r($user);die;
            $category = $user->getCategoryData($user->id);
            if(!$category){
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>'Please Setup your profile like set category etc'), 400);
            }
            $service = CategoryServiceType::where('category_id',$category->id)->first();
            $service_id = $service->service_id;
            $timezone = $request->header('timezone');
            if(!$timezone){
                $timezone = 'Asia/Kolkata';
            }
            $feature = Helper::getClientFeatureExistWithFeatureType('Dynamic Sections','Master Interval');
            if($feature){
                $masterslots = \App\Model\MasterSlot::orderBy('id','ASC')->get();
            }
            if(isset($request->applyoption) && $request->applyoption=='weekwise'){
                 $sp_slot_array = [];
                 $actual_days = [];
                 $days = [0,1,2,3,4,5,6];
                 $single_day = 0;
                 foreach ($days as $key => $day) {
                    $sp_slot = ServiceProviderSlot::where([
                        'service_provider_id'=>$user->id,
                        'service_id'=>$service_id,
                        'day'=>$day,
                        'category_id'=>$category->id,
                    ])->first();
                    if($sp_slot){
                        $single_day = $day;
                        $actual_days[] = true;
                    }else{
                        $actual_days[] = false;
                    }
                 }
                 foreach ($masterslots as $key => $masterslot) {
                     $sp_slot = ServiceProviderSlot::where([
                            'service_provider_id'=>$user->id,
                            'service_id'=>$service_id,
                            'category_id'=>$category->id,
                            'start_time'=>$masterslot->start_time,
                            'end_time'=>$masterslot->end_time,
                        ])->first();
                     if($sp_slot){
                        $masterslot->isSelected = true;
                     }else{
                        $masterslot->isSelected = false;
                    }
                 }
                foreach ($masterslots as $key => $sp_slot) {
                    $start_time_date = Carbon::parse($sp_slot->start_time,'UTC')->setTimezone($timezone);
                    $start_time = $start_time_date->isoFormat('h:mm a');
                    $end_time_date = Carbon::parse($sp_slot->end_time,'UTC')->setTimezone($timezone);
                    $end_time = $end_time_date->isoFormat('h:mm a');
                    $sp_slot_array[] = array('start_time'=>$start_time,'end_time'=>$end_time,'isSelected'=>$sp_slot->isSelected);
                }
                return response(['status' => "success", 'statuscode' => 200,'message' => __('Slot List '), 'data' =>['slots'=>$sp_slot_array,'interval'=>null,'date'=>null,'days'=>$actual_days]], 200);
            }else{
                $weekMap = ['SU'=>0,'MO'=>1,'TU'=>2,'WE'=>3,'TH'=>4,'FR'=>5,'SA'=>6];
                $duration_by_setting = true;
                $slots = [];
                $slot_duration = EnableService::where('type','slot_duration')->first();
                $add_mins  = 30 * 60;
                if($slot_duration){
                    $add_mins = $slot_duration->value * 60;
                }
                $feature = Helper::getClientFeatureExistWithFeatureType('Dynamic Sections','Master Interval');
                if($feature){
                    $masterslots =  Helper::getMasterSlots();
                }
                if(count($slots) > 0){
                    $sp_slots = $slots;
                }else{
                    $sp_slots = ServiceProviderSlotsDate::where([
                        'service_provider_id'=>$user->id,
                        'service_id'=>$service_id,
                        'date'=>$input['date'],
                        'category_id'=>$category->id,
                    ])->get();
                    $sp_slot_array = [];
                    if($sp_slots->count()==0){
                        $day = strtoupper(substr(Carbon::parse($input['date'])->format('l'), 0, 2));
                        $day_number = $weekMap[$day];
                        $sp_slots = ServiceProviderSlot::where([
                            'service_provider_id'=>$user->id,
                            'service_id'=>$service_id,
                            'day'=>$day_number,
                            'category_id'=>$category->id,
                        ])->get();

                    }
                }

                foreach ($masterslots as $key => $masterslot) {
                     $sp_slot = ServiceProviderSlotsDate::where([
                            'service_provider_id'=>$user->id,
                            'service_id'=>$service_id,
                            'category_id'=>$category->id,
                            'start_time'=>$masterslot->start_time,
                            'end_time'=>$masterslot->end_time,
                            'date'=>$input['date'],
                     ])->first();
                     if($sp_slot){
                        $masterslot->isSelected = true;
                     }else{
                        $masterslot->isSelected = false;
                        $day = strtoupper(substr(Carbon::parse($input['date'])->format('l'), 0, 2));
                        $day_number = $weekMap[$day];
                        $sp_slot = ServiceProviderSlot::where([
                                'service_provider_id'=>$user->id,
                                'service_id'=>$service_id,
                                'category_id'=>$category->id,
                                'day'=>$day_number,
                                'start_time'=>$masterslot->start_time,
                                'end_time'=>$masterslot->end_time,
                            ])->first();
                        if($sp_slot){
                            $masterslot->isSelected = true;
                        }
                     }
                }
                $dateznow = new DateTime("now", new DateTimeZone($timezone));
                $datenow = $dateznow->format('Y-m-d H:i:s');
                $current_date = $dateznow->format('Y-m-d');
                $currentTime    = strtotime ($datenow);
                // print_r($currentTime);die;
                // echo " current time $currentTime \n";
                $array_of_time = array ();
                if($masterslots->count()>0){
                    foreach ($masterslots as $key => $sp_slot) {
                        $start_time_date = Carbon::parse($sp_slot->start_time,'UTC')->setTimezone($timezone);
                        $start_time = $start_time_date->isoFormat('h:mm a');
                        $end_time_date = Carbon::parse($sp_slot->end_time,'UTC')->setTimezone($timezone);
                        $end_time = $end_time_date->isoFormat('h:mm a');
                        $starttime    = strtotime ($start_time); //change to strtotime
                        $endtime      = strtotime ($end_time); //change to strtotime
                        while ($starttime < $endtime) // loop between time
                        {
                           $time = date ("h:i a", $starttime);
                           $starttime_slot = date ("H:i:s", $starttime);
                           $starttime_slot_one_m = date ("H:i:s", $starttime + 60);
                           if($duration_by_setting){
                                $endDT = $starttime + $add_mins;
                                $end_time_new = date ("h:i a", $endtime);
                           }else{
                                $endDT = $endtime;
                                $end_time_new = date ("h:i a", $endtime);
                           }
                           // $starttime += $add_mins; // to check endtie=me
                           // $endtime_slot = date ("H:i:s", $starttime);

                           $endtime_slot = date("H:i:s", $endDT);
                           $start_time_slot_utcdate = Carbon::parse($input['date'].' '.$starttime_slot,$timezone)->setTimezone('UTC');
                           $starttime_slot_one_m = Carbon::parse($input['date'].' '.$starttime_slot_one_m,$timezone)->setTimezone('UTC');
                           $end_time_slot_utcdate = Carbon::parse($input['date'].' '.$endtime_slot,$timezone)->setTimezone('UTC');
                           // print_r($end_time_slot_utcdate);
                           $exist = \App\Model\Request::where('to_user',$user->id)
                           // ->where('booking_date','<=',$end_time_slot_utcdate)
                            ->where('booking_date','=',$start_time_slot_utcdate)
                            ->orWhereBetween('booking_end_date',[$starttime_slot_one_m,$end_time_slot_utcdate])
                           ->whereHas('requesthistory', function ($query) {
                                $query->where('status','!=','canceled');
                            })
                           ->get();
                           $available = true;
                           if($exist->count()>0){
                                $available = false;
                           }
                           if($current_date==$input['date'] && $starttime>=$currentTime){
                                $time = date ("h:i a", $starttime);
                                $array_of_time[] = ["time"=>$time,"end_time"=>$end_time_new,"available"=>$available];
                           }else if($input['date'] > $current_date){
                                $time = date ("h:i a", $starttime);
                                $array_of_time[] = ["time"=>$time,"end_time"=>$end_time_new,"available"=>$available];
                           }
                           if($duration_by_setting){
                                $starttime += $add_mins;
                           }else{
                                $starttime += 60*60;
                           }
                        }
                        $sp_slot_array[] = array('start_time'=>$start_time,'end_time'=>$end_time,'isSelected'=>$sp_slot->isSelected);
                    }
                }
                return response(['status' => "success", 'statuscode' => 200,'message' => __('Slot List '), 'data' =>['slots'=>$sp_slot_array,'interval'=>$array_of_time,'date'=>$input['date']]], 200);
            }
            
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage().' '.$ex->getLine()], 500);
        }
    }

    /**
     * @SWG\Post(
     *     path="/post-physio-slots",
     *     description="postCustomSlots",
     * tags={"Service Provider"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="applyoption",
     *         in="query",
     *         type="string",
     *         description="[]",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="days",
     *         in="query",
     *         type="string",
     *         description="[]",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="slots",
     *         in="query",
     *         type="string",
     *         description="[]",
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
    public  function postCustomSlots(Request $request) {
        try{
            $user = Auth::user();
            $input = $request->all();
            $rules = [];
            $rules['applyoption'] = 'required';
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $category = $user->getCategoryData($user->id);
            if(!$category){
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>'Please Setup your profile like set category etc'), 400);
            }
            $service = CategoryServiceType::where('category_id',$category->id)->first();
            $service_id = $service->service_id;
            $timezone = $request->header('timezone');
            if(!$timezone){
                $timezone = 'Asia/Kolkata';
            }
            if($input['applyoption']=='weekwise'){
                if(isset($input['days'])){
                    ServiceProviderSlot::where([
                        'service_provider_id'=>$user->id,
                        'service_id'=>$service_id,
                        'category_id'=>$category->id,
                    ])->delete();
                    foreach ($input['days'] as $day=>$flag) {
                        if($flag){
                           foreach ($input['slots'] as $slot) {
                                if($slot['isSelected']){
                                    $start_time = Carbon::parse($slot['start_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                                    $end_time = Carbon::parse($slot['end_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                                    $spavailability = new ServiceProviderSlot();
                                    $spavailability->service_provider_id = $user->id;
                                    $spavailability->service_id = $service_id;
                                    $spavailability->category_id = $category->id;
                                    $spavailability->start_time = $start_time;
                                    $spavailability->end_time = $end_time;
                                    $spavailability->day = $day;
                                    $spavailability->save();
                                }
                           }
                        }
                    }
                }
            }else if($input['applyoption']=='multiple_days'){
                if(isset($input['days'])){
                    foreach ($input['days'] as $day=>$flag) {
                        if($flag){
                            ServiceProviderSlot::where([
                                'service_provider_id'=>$user->id,
                                'service_id'=>$service_id,
                                'category_id'=>$category->id,
                                'day'=>$day,
                            ])->delete();
                           foreach ($input['slots'] as $slot) {
                                if($slot['isSelected']){
                                    $start_time = Carbon::parse($slot['start_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                                    $end_time = Carbon::parse($slot['end_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                                    $spavailability = new ServiceProviderSlot();
                                    $spavailability->service_provider_id = $user->id;
                                    $spavailability->service_id = $service_id;
                                    $spavailability->category_id = $category->id;
                                    $spavailability->start_time = $start_time;
                                    $spavailability->end_time = $end_time;
                                    $spavailability->day = $day;
                                    $spavailability->save();
                                }
                           }
                        }
                    }
                }
            }else if($input['applyoption']=='specific_date'){
                ServiceProviderSlotsDate::where([
                    'service_provider_id'=>$user->id,
                    'service_id'=>$service_id,
                    'date'=>$input['date'],
                    'category_id'=>$category->id,
                ])->delete();
               foreach ($input['slots'] as $slot) {
                if($slot['isSelected']){
                    $start_time = Carbon::parse($slot['start_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                    $end_time = Carbon::parse($slot['end_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                    $spavailability = new ServiceProviderSlotsDate();
                    $spavailability->service_provider_id = $user->id;
                    $spavailability->service_id = $service_id;
                    $spavailability->category_id = $category->id;
                    $spavailability->start_time = $start_time;
                    $spavailability->end_time = $end_time;
                    $spavailability->date = $input['date'];
                    $spavailability->save();
                }
               }
            }else if($input['applyoption']=='specific_day'){
                $weekMap = ['SU'=>0,'MO'=>1,'TU'=>2,'WE'=>3,'TH'=>4,'FR'=>5,'SA'=>6];
                $day = strtoupper(substr(Carbon::parse($input['date'])->format('l'), 0, 2));
                $day_number = $weekMap[$day];
                ServiceProviderSlot::where([
                    'service_provider_id'=>$user->id,
                    'service_id'=>$service_id,
                    'day'=>$day_number,
                    'category_id'=>$category->id,
                ])->delete();
               foreach ($input['slots'] as $slot) {
                if($slot['isSelected']){
                    $start_time = Carbon::parse($slot['start_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                    $end_time = Carbon::parse($slot['end_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                    $spavailability = new ServiceProviderSlot();
                    $spavailability->service_provider_id = $user->id;
                    $spavailability->service_id = $service_id;
                    $spavailability->category_id = $category->id;
                    $spavailability->start_time = $start_time;
                    $spavailability->end_time = $end_time;
                    $spavailability->day = $day_number;
                    $spavailability->save();
                }
               }
            }else if($input['applyoption']=='weekdays'){//monday-to-friday
                $weekdays = [1,2,3,4,5];
                ServiceProviderSlot::where([
                    'service_provider_id'=>$user->id,
                    'service_id'=>$service_id,
                    'category_id'=>$category->id,
                ])->whereIn('day',$weekdays)->delete();
                foreach ($weekdays as $day) {
                   foreach ($input['slots'] as $slot) {
                    if($slot['isSelected']){
                        $start_time = Carbon::parse($slot['start_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                        $end_time = Carbon::parse($slot['end_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                        $spavailability = new ServiceProviderSlot();
                        $spavailability->service_provider_id = $user->id;
                        $spavailability->service_id = $service_id;
                        $spavailability->category_id = $category->id;
                        $spavailability->start_time = $start_time;
                        $spavailability->end_time = $end_time;
                        $spavailability->day = $day;
                        $spavailability->save();
                    }
                   }
                }
            }
            return response(['status' => "success", 'statuscode' => 200,'message' => __('Slot List Updated')], 200);

        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage().' '.$ex->getLine()], 500);
        }
    }    

    /**
     * @SWG\Get(
     *     path="/dates-slots",
     *     description="getDoctor Dates Slots",
     * tags={"Service Provider"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="dates",
     *         in="query",
     *         type="string",
     *         description="date 2010-01-20,2010-01-20,2010-01-20",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="service_id",
     *         in="query",
     *         type="string",
     *         description="service_id",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="category_id",
     *         in="query",
     *         type="string",
     *         description="category_id",
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
    public static function getSlotsByMultipleDates(Request $request) {
        try{
            $user =  Auth::user();
            $rules = [
                'service_id' => 'required|exists:services,id',
                'category_id' => 'required|exists:categories,id',
                'dates' => 'required',
            ];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $input = $request->all();
            $timezone = $request->header('timezone');
            if(!$timezone){
                $timezone = 'Asia/Kolkata';
            }
            $dates = explode(',', $input['dates']);
            $data = [];
            foreach ($dates as $key => $date) {
                $weekMap = ['SU'=>0,'MO'=>1,'TU'=>2,'WE'=>3,'TH'=>4,'FR'=>5,'SA'=>6];
                $sp_slots = ServiceProviderSlotsDate::where([
                    'service_provider_id'=>$user->id,
                    'service_id'=>$input['service_id'],
                    'date'=>$date,
                    'category_id'=>$input['category_id'],
                ])->get();
                $sp_slot_array = [];
                if($sp_slots->count()==0){
                    $day = strtoupper(substr(Carbon::parse($date)->format('l'), 0, 2));
                    $day_number = $weekMap[$day];
                    $sp_slots = ServiceProviderSlot::where([
                        'service_provider_id'=>$user->id,
                        'service_id'=>$input['service_id'],
                        'day'=>$day_number,
                        'category_id'=>$input['category_id'],
                    ])->get();

                }
                // print_r($sp_slots);die;
                $array_of_time = array ();
                if($sp_slots->count()>0){
                    foreach ($sp_slots as $key => $sp_slot) {
                        $start_time_date = Carbon::parse($sp_slot->start_time,'UTC')->setTimezone($timezone);
                        $start_time = $start_time_date->isoFormat('h:mm a');
                        $end_time_date = Carbon::parse($sp_slot->end_time,'UTC')->setTimezone($timezone);
                        $end_time = $end_time_date->isoFormat('h:mm a');
                        $starttime    = strtotime ($start_time); //change to strtotime
                        $endtime      = strtotime ($end_time); //change to strtotime
                        $sp_slot_array[] = array('start_time'=>$start_time,'end_time'=>$end_time);
                    }
                }
                $data[] = ["date"=>$date,"sp_slot_array"=>$sp_slot_array];
            }
            // $dates =
            return response(['status' => "success", 'statuscode' => 200,'message' => __('Slot List '), 'data' =>$data], 200);
            
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage().' '.$ex->getLine()], 500);
        }
    }

    /**
     * @SWG\Get(
     *     path="/review-list",
     *     description="getDoctorReviewList",
     * tags={"Service Provider"},
     *  @SWG\Parameter(
     *         name="doctor_id",
     *         in="query",
     *         type="string",
     *         description="doctor_id",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="before",
     *         in="query",
     *         type="string",
     *         description="before id",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="after",
     *         in="query",
     *         type="string",
     *         description="after id",
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
    public static function getDoctorReviewList(Request $request) {
        try{
            $rules = ['doctor_id' => 'required'];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $message = null;
            $per_page = (isset($request->per_page)?$request->per_page:10);
            $doctor = User::find($request->doctor_id);
            if($doctor && $doctor->isDoctor($request->doctor_id)){
                $review_list = Feedback::select('id','from_user','rating','comment')->where('consultant_id',$request->doctor_id)->with(['user' => function($query) {
                            return $query->select(['id', 'name', 'email','phone','profile_image']);
                        }])->orderBy('id', 'desc')->cursorPaginate($per_page);
                $after = null;
                if($review_list->meta['next']){
                    $after = $review_list->meta['next']->target;
                }
                $before = null;
                if($review_list->meta['previous']){
                    $before = $review_list->meta['previous']->target;
                }
                $per_page = $review_list->perPage();
                return response(['status' => "success", 'statuscode' => 200,
                                    'message' => __('Review List '), 'data' =>['review_list'=>$review_list->items(),'after'=>$after,'before'=>$before,'per_page'=>$per_page]], 200);
            }else{
                return response(['status' => "error", 'statuscode' => 400,
                                    'message' => __('Doctor Not Found ')], 400);
            }
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }

    /**
     * @SWG\Post(
     *     path="/subscribe-service",
     *     description="getDoctorList",
     * tags={"Service Provider"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="service_type",
     *         in="query",
     *         type="string",
     *         description="service_type e.g chat,call,feed",
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
    public static function postSubscribe(Request $request) {
        try{
            $user = Auth::user();
            if(!$user->hasrole('service_provider')){
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>'Invalid user role, must be role as service_provider'), 400);
            }
            $rules = ['service_type' => 'required'];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $request->service_type = strtolower($request->service_type);
            $doctors = [];
            $service_id = null;
            $service_type = Service::select('id')
            ->where('type',$request->service_type)
            ->first();
            if(!$service_type){
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>'Serive type not found'), 400);
            }
            $service_id = $service_type->id;
            $subscribe = Subscription::where(['service_id'=>$service_id,'consultant_id'=>$user->id])->first();
            if($subscribe){
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>'You are already subscribed to this service'), 400);
            }else{
                $charges = '0';
                $duration = 60;
                if($request->service_type=='chat' && !$user->profile->chat_price){
                    return response(array('status' => "error", 'statuscode' => 400, 'message' =>'Please set your chat price in edit profile section'), 400);
                }else{
                    $charges = $user->profile->chat_price;

                }
                if($request->service_type=='call' && !$user->profile->call_price){
                    return response(array('status' => "error", 'statuscode' => 400, 'message' =>'Please set your call price in edit profile section'), 400);
                }else{
                    $charges = $user->profile->call_price;
                }
                $subscribe = new Subscription();
                $subscribe->service_id = $service_id;
                $subscribe->consultant_id = $user->id;
                $subscribe->charges = $charges;
                $subscribe->duration = $duration;
                $subscribe->save();
                $user->subscriptions = $user->getSubscription($user);
            }
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('Subscribed  '), 'data' =>['subscriptions'=>$user->subscriptions]], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }


    /**
     * @SWG\Post(
     *     path="/block-dates",
     *     description="Update block dates by services",
     * tags={"Service Provider"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="category_id",
     *         in="query",
     *         type="string",
     *         description="category id",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="service_id",
     *         in="query",
     *         type="string",
     *         description="main service id",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="block_dates",
     *         in="query",
     *         type="string",
     *         description="['2010-08-19','2010-08-20']",
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
    public function postBlockDates(Request $request){
        try{
            $user = Auth::user();
            if(!$user->hasrole('service_provider')){
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>'Invalid user role, must be role as service_provider'), 400);
            }
            $input = $request->all();
            $rules = [
                    'category_id'=>'required|integer|exists:categories,id'
            ];
            $rules["service_id"] = "required|integer|exists:services,id";
            $rules["block_dates"] = "required";
            // $rules["block_dates.*.id"] = "required|integer|exists:category_service_types,id";
            // $rules["block_dates.*.dates"] = "required";
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            if(!is_array($input['block_dates'])){
              $input['block_dates'] = json_decode($input['block_dates']);
            }
            $timezone = $request->header('timezone');
            if(!$timezone){
                $timezone = 'Asia/Kolkata';
            }
            if(isset($input['block_dates'])){
                ServiceProviderSlotsDate::where([
                    'service_provider_id'=>$user->id,
                    'service_id'=>$input['service_id'],
                    'working_today'=>'n',
                    'category_id'=>$input['category_id'],
                ])->delete();
                foreach ($input['block_dates'] as $block_date) {
                     if($block_date){
                        $start_time = Carbon::parse($block_date.' 00:00:00',$timezone)->setTimezone('UTC')->format('H:i:s');
                        $end_time = Carbon::parse($block_date.' 23:59:59',$timezone)->setTimezone('UTC')->format('H:i:s');
                         ServiceProviderSlotsDate::where([
                            'service_provider_id'=>$user->id,
                            'date'=>$block_date,
                            'start_time'=>$start_time,
                            'end_time'=>$end_time,
                            'service_id'=>$input['service_id'],
                            'category_id'=>$input['category_id'],
                        ])->delete();
                        $spavailability = new ServiceProviderSlotsDate();
                        $spavailability->service_provider_id = $user->id;
                        $spavailability->service_id = $input['service_id'];
                        $spavailability->category_id = $input['category_id'];
                        $spavailability->start_time = $start_time;
                        $spavailability->end_time = $end_time;
                        $spavailability->date = $block_date;
                        $spavailability->working_today = 'n';
                        $spavailability->save();
                     }
                }
            }
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('Blocked  '), 'data' => (Object)[]], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage().' '.$ex->getLine()], 500);
        }
    }
    



    /**
     * @SWG\Get(
     *     path="/block-dates",
     *     description="Get block dates by services",
     * tags={"Service Provider"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="category_id",
     *         in="query",
     *         type="string",
     *         description="category id",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="service_id",
     *         in="query",
     *         type="string",
     *         description="main service id",
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
    public function getBlockDates(Request $request){
        try{
            $user = Auth::user();
            if(!$user->hasrole('service_provider')){
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>'Invalid user role, must be role as service_provider'), 400);
            }
            $input = $request->all();
            $rules = [
                    'category_id'=>'required|integer|exists:categories,id'
            ];
            $rules["service_id"] = "required|integer|exists:services,id";
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $dates = ServiceProviderSlotsDate::where([
                'service_provider_id'=>$user->id,
                'service_id'=>$input['service_id'],
                'working_today'=>'n',
                'category_id'=>$input['category_id'],
            ])->pluck('date')->toArray();
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('Blocked Dates'), 'data' => ['block_dates'=>$dates]], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage().' '.$ex->getLine()], 500);
        }
    }




    /**
     * @SWG\Post(
     *     path="/update-services",
     *     description="Update Services Filter,Category,Service,Availibilty",
     * tags={"Service Provider"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="category_id",
     *         in="query",
     *         type="string",
     *         description="category ids",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="category_iedu_id",
     *         in="query",
     *         type="string",
     *         description="category iedu id",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="filters",
     *         in="query",
     *         type="string",
     *         description="filters array [{'filter_id':9,'filter_option_ids':[13,14]}]",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="category_services_type",
     *         in="query",
     *         type="string",
     *         description="[{'id':2,'available':'1','price':10,'minimmum_heads_up':5,'availability':{'applyoption':'specific_day','day':2,'date':'2010-08-19','days':[true,false,true,true,true,true,false],'slots':[{'start_time':'11:00','end_time':'16:30'}]}}]",
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
    public static function postSubscribeServiceOrFilters(Request $request) {
        try{
            $user = Auth::user();
            if(!$user->hasrole('service_provider')){
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>'Invalid user role, must be role as service_provider'), 400);
            }
            $input = $request->all();
            if($user->account_step<5){
                $rules = [
                        'category_id'=>'required|integer|exists:categories,id',
                        'category_services_type' => 'required|array|min:1',
                        'category_services_type.*.id' => 'required|integer|exists:category_service_types,id',
                        'category_services_type.*.available' => 'required|string',
                ];
                if(isset($input['category_id'])){
                    $category = Category::where('id',$input['category_id'])->first();
                    if($category->filters->count() > 0){
                        $rules["filters"] = "required|array|min:1";
                        $rules["filters.*.filter_id"] = "required|integer|exists:filter_types,id";
                        $rules["filters.*.filter_option_ids"] = "required|array|min:1";
                    }
                }
            }else{
                $rules = [
                        'category_id'=>'required|integer|exists:categories,id'
                ];
                if(isset($input['filters'])){
                    $rules["filters"] = "required|array|min:1";
                    $rules["filters.*.filter_id"] = "required|integer|exists:filter_types,id";
                    $rules["filters.*.filter_option_ids"] = "required|array|min:1";
                }
                if(isset($input['category_services_type'])){
                    $rules["category_services_type"] = "required|array|min:1";
                    $rules["category_services_type.*.id"] = "required|integer|exists:category_service_types,id";
                    $rules["category_services_type.*.available"] = "required|string";
                    $rules["category_services_type.*.isAvailabilityChanged"] = "required";
                }
            }
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            if(isset($input['category_services_type'])){
                foreach ($input['category_services_type'] as $key_sr=>$category_service_type) {
                    if($category_service_type['available']=="1"){
                        $rules["category_services_type.$key_sr.price"] = "required";
                        $rules["category_services_type.$key_sr.minimmum_heads_up"] = "required";
                    }
                    $service = CategoryServiceType::where('id',$category_service_type['id'])->first();
                    if($service && $service->service->need_availability && $category_service_type['available']=="1"){
                        if(!isset($category_service_type['isAvailabilityChanged']) || $category_service_type['isAvailabilityChanged']){
                            $rules["category_services_type.$key_sr.availability"] ="required"; 
                            $rules["category_services_type.$key_sr.availability.applyoption"] ="required";
                            if(isset($category_service_type["availability"]) && isset($category_service_type["availability"]["applyoption"]) && ($category_service_type["availability"]["applyoption"]=="weekwise" || $category_service_type["availability"]["applyoption"]=="multiple_days")){
                                $rules["category_services_type.$key_sr.availability.days"] ="required|array|min:1"; 
                                $rules["category_services_type.$key_sr.availability.slots"] ="required|array|min:1"; 
                                $rules["category_services_type.$key_sr.availability.slots.*.start_time"] ="required|date_format:H:i"; 
                                $rules["category_services_type.$key_sr.availability.slots.*.end_time"] ="required|date_format:H:i"; 

                            }else if(isset($category_service_type["availability"]) && isset($category_service_type["availability"]["applyoption"]) && $category_service_type["availability"]["applyoption"] =="specific_date"){
                                $rules["category_services_type.$key_sr.availability.date"] ="required|date_format:Y-m-d";
                                $rules["category_services_type.$key_sr.availability.slots"] ="required|array|min:1"; 
                                $rules["category_services_type.$key_sr.availability.slots.*.start_time"] ="required|date_format:H:i"; 
                                $rules["category_services_type.$key_sr.availability.slots.*.end_time"] ="required|date_format:H:i"; 

                            }else if(isset($category_service_type["availability"]) && isset($category_service_type["availability"]["applyoption"]) && $category_service_type["availability"]["applyoption"]=="specific_day"){
                                $rules["category_services_type.$key_sr.availability.date"] ="required|date_format:Y-m-d";
                                $rules["category_services_type.$key_sr.availability.slots"] ="required|array|min:1"; 
                                $rules["category_services_type.$key_sr.availability.slots.*.start_time"] ="required|date_format:H:i"; 
                                $rules["category_services_type.$key_sr.availability.slots.*.end_time"] ="required|date_format:H:i"; 

                            }else if(isset($category_service_type["availability"]) && isset($category_service_type["availability"]["applyoption"]) && $category_service_type["availability"]["applyoption"]=="weekdays"){
                                $rules["category_services_type.$key_sr.availability.slots"] ="required|array|min:1"; 
                                $rules["category_services_type.$key_sr.availability.slots.*.start_time"] ="required|date_format:H:i"; 
                                $rules["category_services_type.$key_sr.availability.slots.*.end_time"] ="required|date_format:H:i"; 

                            }
                        }
                    }
                }
            }
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $timezone = $request->header('timezone');
            if(!$timezone){
                $timezone = 'Asia/Kolkata';
            }
            $duration = '60';
            $unit_price = EnableService::where('type','unit_price')->first();
            if($unit_price){
                $duration = $unit_price->value * 60;
            }
            if(isset($input['category_services_type'])){
                // $delete = SpServiceType::where(['sp_id'=>$user->id])->delete();
                foreach ($input['category_services_type'] as $category_service_type) {
                    $spservicetype = SpServiceType::firstOrCreate([
                        'sp_id'=>$user->id,
                        'category_service_id'=>$category_service_type['id']
                    ]);
                    if(isset($category_service_type['clinic_address'])){
                        $address = \App\Model\CustomInfo::firstOrCreate([
                            'info_type'=>'service_address',
                            'ref_table'=>'sp_service_types',
                            'ref_table_id'=>$spservicetype->id,
                            'status'=>'success',
                        ]);
                        $address->raw_detail = json_encode($category_service_type['clinic_address']);
                        $address->save();
                    }
                    if($spservicetype){
                        $service = CategoryServiceType::where('id',$category_service_type['id'])->first();
                        $spservicetype->available = $category_service_type['available'];
                        if($category_service_type['available']=="1")
                            $spservicetype->minimmum_heads_up = $category_service_type['minimmum_heads_up'];
                        if($service->price_fixed!==null){
                            $spservicetype->price = $service->price_fixed;
                        }else{
                            if($category_service_type['available']=="1"){
                                if($category_service_type['price'] >= $service->price_minimum && $category_service_type['price']<=$service->price_maximum){
                                    $spservicetype->price = $category_service_type['price'];
                                }else{
                                    return response(array('status' => "error", 'statuscode' => 400, 'message' =>'Please select price into the range price_fixed'), 400);
                                }
                            }
                        }
                        $spservicetype->duration = $duration;
                        $spservicetype->save();
                        if($service && $service->service->need_availability){
                            if(!isset($category_service_type['isAvailabilityChanged']) || $category_service_type['isAvailabilityChanged']){
                                $availability = $category_service_type["availability"];
                                if($availability['applyoption']=='weekwise'){
                                    if(isset($availability['days'])){
                                        ServiceProviderSlot::where([
                                            'service_provider_id'=>$user->id,
                                            'service_id'=>$service->service_id,
                                            'category_id'=>$input['category_id'],
                                        ])->delete();
                                        foreach ($availability['days'] as $day=>$flag) {
                                            if($flag){
                                               foreach ($availability['slots'] as $slot) {
                                                    $start_time = Carbon::parse($slot['start_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                                                    $end_time = Carbon::parse($slot['end_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                                                    $spavailability = new ServiceProviderSlot();
                                                    $spavailability->service_provider_id = $user->id;
                                                    $spavailability->service_id = $service->service_id;
                                                    $spavailability->category_id = $input['category_id'];
                                                    $spavailability->start_time = $start_time;
                                                    $spavailability->end_time = $end_time;
                                                    $spavailability->day = $day;
                                                    $spavailability->save();
                                               }
                                            }
                                        }
                                    }
                                }else if($availability['applyoption']=='multiple_days'){
                                    if(isset($availability['days'])){
                                        foreach ($availability['days'] as $day=>$flag) {
                                            if($flag){
                                                ServiceProviderSlot::where([
                                                    'service_provider_id'=>$user->id,
                                                    'service_id'=>$service->service_id,
                                                    'category_id'=>$input['category_id'],
                                                    'day'=>$day,
                                                ])->delete();
                                               foreach ($availability['slots'] as $slot) {
                                                    $start_time = Carbon::parse($slot['start_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                                                    $end_time = Carbon::parse($slot['end_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                                                    $spavailability = new ServiceProviderSlot();
                                                    $spavailability->service_provider_id = $user->id;
                                                    $spavailability->service_id = $service->service_id;
                                                    $spavailability->category_id = $input['category_id'];
                                                    $spavailability->start_time = $start_time;
                                                    $spavailability->end_time = $end_time;
                                                    $spavailability->day = $day;
                                                    $spavailability->save();
                                               }
                                            }
                                        }
                                    }
                                }else if($availability['applyoption']=='specific_date'){
                                    ServiceProviderSlotsDate::where([
                                        'service_provider_id'=>$user->id,
                                        'service_id'=>$service->service_id,
                                        'date'=>$availability['date'],
                                        'category_id'=>$input['category_id'],
                                    ])->delete();
                                   foreach ($availability['slots'] as $slot) {
                                        $start_time = Carbon::parse($slot['start_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                                        $end_time = Carbon::parse($slot['end_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                                        $spavailability = new ServiceProviderSlotsDate();
                                        $spavailability->service_provider_id = $user->id;
                                        $spavailability->service_id = $service->service_id;
                                        $spavailability->category_id = $input['category_id'];
                                        $spavailability->start_time = $start_time;
                                        $spavailability->end_time = $end_time;
                                        $spavailability->date = $availability['date'];
                                        $spavailability->save();
                                   }
                                }else if($availability['applyoption']=='specific_day'){
                                    $weekMap = ['SU'=>0,'MO'=>1,'TU'=>2,'WE'=>3,'TH'=>4,'FR'=>5,'SA'=>6];
                                    $day = strtoupper(substr(Carbon::parse($availability['date'])->format('l'), 0, 2));
                                    $day_number = $weekMap[$day];
                                    ServiceProviderSlot::where([
                                        'service_provider_id'=>$user->id,
                                        'service_id'=>$service->service_id,
                                        'day'=>$day_number,
                                        'category_id'=>$input['category_id'],
                                    ])->delete();
                                   foreach ($availability['slots'] as $slot) {
                                        $start_time = Carbon::parse($slot['start_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                                        $end_time = Carbon::parse($slot['end_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                                        $spavailability = new ServiceProviderSlot();
                                        $spavailability->service_provider_id = $user->id;
                                        $spavailability->service_id = $service->service_id;
                                        $spavailability->category_id = $input['category_id'];
                                        $spavailability->start_time = $start_time;
                                        $spavailability->end_time = $end_time;
                                        $spavailability->day = $day_number;
                                        $spavailability->save();
                                   }
                                }else if($availability['applyoption']=='weekdays'){//monday-to-friday
                                    $weekdays = [1,2,3,4,5];
                                    ServiceProviderSlot::where([
                                        'service_provider_id'=>$user->id,
                                        'service_id'=>$service->service_id,
                                        'category_id'=>$input['category_id'],
                                    ])->whereIn('day',$weekdays)->delete();
                                    foreach ($weekdays as $day) {
                                       foreach ($availability['slots'] as $slot) {
                                            $start_time = Carbon::parse($slot['start_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                                            $end_time = Carbon::parse($slot['end_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                                            $spavailability = new ServiceProviderSlot();
                                            $spavailability->service_provider_id = $user->id;
                                            $spavailability->service_id = $service->service_id;
                                            $spavailability->category_id = $input['category_id'];
                                            $spavailability->start_time = $start_time;
                                            $spavailability->end_time = $end_time;
                                            $spavailability->day = $day;
                                            $spavailability->save();
                                       }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if(isset($input['category_id'])){
                $category_service = CategoryServiceProvider::where(['sp_id'=>$user->id])->first();
                if(!$category_service){
                    $category_service =  new CategoryServiceProvider();
                    $category_service->sp_id = $user->id;
                }
                $category_service->category_id = $input['category_id'];
                $category_service->save();
            }

            if(isset($input['category_iedu_id'])){

                $category_service = CategoryServiceProvider::where(['sp_id'=>$user->id])->first();

                foreach ($input['category_iedu_id'] as $key => $category_iedu_id) {
                   
                
                    if(!$category_service){
                        $category_service =  new CategoryServiceProvider();
                        $category_service->sp_id = $user->id;
                    }
                    $category_service->category_id = $category_iedu_id;
                    $category_service->save();
                }

            }
            if(isset($input['filters'])){
                foreach ($input['filters'] as $key => $filter) {
                        ServiceProviderFilterOption::where([
                            'sp_id'=>$user->id,
                            'filter_type_id'=>$filter['filter_id'],
                        ])->delete();
                    foreach ($filter['filter_option_ids'] as $filter_option_key => $filter_option) {
                        ServiceProviderFilterOption::firstOrCreate([
                            'sp_id'=>$user->id,
                            'filter_type_id'=>$filter['filter_id'],
                            'filter_option_id'=>$filter_option,
                        ]);
                    }
                }
            }
            $user->account_step = 5;
            $user->save();
            $user->subscriptions = $user->getSubscription($user);
            $user->profile;
            $user->roles;
            if($user->profile){
                $user->profile->bio = $user->profile->about;
                $user->totalRating =  $user->profile->rating;
            }
            $user->categoryData = $user->getCategoryData($user->id);
            $user->additionals = $user->getAdditionals($user->id);
            $user->services = $user->getServices($user->id);
            $user->filters = $user->getFilters($user->id);
            $user->patientCount = User::getTotalRequestDone($user->id);
            $user->reviewCount = Feedback::reviewCountByConsulatant($user->id);
            $user->custom_fields = $user->getCustomFields($user->id);
            $token = $user->createToken('consult_app')->accessToken;
            $user->token = $token; 
            $user = Helper::getMoreData($user);
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('Subscribed  '), 'data' => $user], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage().' '.$ex->getLine()], 500);
        }
    }


    /**
     * @SWG\Post(
     *     path="/update-sp-categories",
     *     description="Update Services Category,Service,Availibilty",
     * tags={"Service Provider"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="category_id",
     *         in="query",
     *         type="string",
     *         description="category ids",
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
    public static function postServiceOrFilters(Request $request) {
        try{
            $user = Auth::user();
            if(!$user->hasrole('service_provider')){
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>'Invalid user role, must be role as service_provider'), 400);
            }
            $input = $request->all();
           
                $rules = [
                    'category_id'=>'required',    
                ];


                if(!isset($request->isOnlyCategories)){
                    $rules['price']= 'required';
                    $rules['slots']= 'required';
                    
                }
                
            
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            
            
            $timezone = $request->header('timezone');
            if(!$timezone){
                $timezone = 'Asia/Kolkata';
            }
            $duration = '60';
            $unit_price = EnableService::where('type','unit_price')->first();
            if($unit_price){
                $duration = $unit_price->value * 60;
            }
            if(isset($input['category_id']) && !isset($request->isOnlyCategories)){
                
                
                $category=explode(',', $input['category_id']);

                CategoryServiceProvider::where([
                            'sp_id'=>$user->id,
                            
                ])->delete();
                
                foreach($category  as $cat_id) {


                    $category_service_type = CategoryServiceType::where(['category_id'=>$cat_id])->first();
                    if(!$category_service_type){
                        // print_r('ddcx');die;
                        $category_service_type = new CategoryServiceType();
                        $category_service_type->category_id = $cat_id;
                        $category_service_type->service_id = 1;
                        $category_service_type->is_active = 1;
                        $category_service_type->price_minimum = 1;
                        $category_service_type->price_maximum = 10000;
                        $category_service_type->minimum_duration = 5;
                        $category_service_type->gap_duration = 5;
                        $category_service_type->save();
                    }
                    $spservicetype = SpServiceType::firstOrCreate([
                        'sp_id'=>$user->id,
                        'category_service_id'=>$category_service_type->id
                    ]);

                    $spservicetype->duration = $duration;

                    $spservicetype->available = "1";

                    $spservicetype->price = $input['price'];

                    $spservicetype->save();


                    // $category_service = CategoryServiceProvider::where(['sp_id'=>$user->id,'category_id'=>$cat_id])->first();
                    // if(!$category_service){
                        $category_service =  new CategoryServiceProvider();
                        $category_service->sp_id = $user->id;
                        $category_service->category_id = $cat_id;
                        $category_service->save();
                    //}

                }
            }else
            {
                $category=explode(',', $input['category_id']);

                CategoryServiceProvider::where([
                            'sp_id'=>$user->id,
                            
                ])->delete();
                foreach($category  as $cat_id) {
               
                    $category_service =  new CategoryServiceProvider();
                    $category_service->sp_id = $user->id;
                    $category_service->category_id = $cat_id;
                    $category_service->save();
                    
                }


            }


            if(isset($input['slots']) && is_array($input['slots'])){
                $availability = $input['slots'];
                if($availability['applyoption']=='weekwise'){
                    if(isset($availability['days'])){
                        ServiceProviderSlot::where([
                            'service_provider_id'=>$user->id,
                            'service_id'=>$category_service_type->service_id,
                            'category_id'=>$input['category_id'],
                        ])->delete();
                        foreach ($availability['days'] as $day=>$flag) {
                            if($flag){
                               foreach ($availability['slots'] as $slot) {
                                    $start_time = Carbon::parse($slot['start_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                                    $end_time = Carbon::parse($slot['end_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                                    $spavailability = new ServiceProviderSlot();
                                    $spavailability->service_provider_id = $user->id;
                                    $spavailability->service_id = $category_service_type->service_id;
                                    $spavailability->category_id = $input['category_id'];
                                    $spavailability->start_time = $start_time;
                                    $spavailability->end_time = $end_time;
                                    $spavailability->day = $day;
                                    $spavailability->save();
                               }
                            }
                        }
                    }
                }else if($availability['applyoption']=='multiple_days'){
                    if(isset($availability['days'])){
                        foreach ($availability['days'] as $day=>$flag) {
                            if($flag){
                                ServiceProviderSlot::where([
                                    'service_provider_id'=>$user->id,
                                    'service_id'=>$category_service_type->service_id,
                                    'category_id'=>$input['category_id'],
                                    'day'=>$day,
                                ])->delete();
                               foreach ($availability['slots'] as $slot) {
                                    $start_time = Carbon::parse($slot['start_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                                    $end_time = Carbon::parse($slot['end_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                                    $spavailability = new ServiceProviderSlot();
                                    $spavailability->service_provider_id = $user->id;
                                    $spavailability->service_id = $category_service_type->service_id;
                                    $spavailability->category_id = $input['category_id'];
                                    $spavailability->start_time = $start_time;
                                    $spavailability->end_time = $end_time;
                                    $spavailability->day = $day;
                                    $spavailability->save();
                               }
                            }
                        }
                    }
                }else if($availability['applyoption']=='specific_date'){
                    ServiceProviderSlotsDate::where([
                        'service_provider_id'=>$user->id,
                        'service_id'=>$category_service_type->service_id,
                        'date'=>$availability['date'],
                        'category_id'=>$input['category_id'],
                    ])->delete();
                   foreach ($availability['slots'] as $slot) {
                        $start_time = Carbon::parse($slot['start_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                        $end_time = Carbon::parse($slot['end_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                        $spavailability = new ServiceProviderSlotsDate();
                        $spavailability->service_provider_id = $user->id;
                        $spavailability->service_id = $category_service_type->service_id;
                        $spavailability->category_id = $input['category_id'];
                        $spavailability->start_time = $start_time;
                        $spavailability->end_time = $end_time;
                        $spavailability->date = $availability['date'];
                        $spavailability->save();
                   }
                }else if($availability['applyoption']=='specific_day'){
                    $weekMap = ['SU'=>0,'MO'=>1,'TU'=>2,'WE'=>3,'TH'=>4,'FR'=>5,'SA'=>6];
                    $day = strtoupper(substr(Carbon::parse($availability['date'])->format('l'), 0, 2));
                    $day_number = $weekMap[$day];
                    ServiceProviderSlot::where([
                        'service_provider_id'=>$user->id,
                        'service_id'=>$category_service_type->service_id,
                        'day'=>$day_number,
                        'category_id'=>$input['category_id'],
                    ])->delete();
                   foreach ($availability['slots'] as $slot) {
                        $start_time = Carbon::parse($slot['start_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                        $end_time = Carbon::parse($slot['end_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                        $spavailability = new ServiceProviderSlot();
                        $spavailability->service_provider_id = $user->id;
                        $spavailability->service_id = $category_service_type->service_id;
                        $spavailability->category_id = $input['category_id'];
                        $spavailability->start_time = $start_time;
                        $spavailability->end_time = $end_time;
                        $spavailability->day = $day_number;
                        $spavailability->save();
                   }
                }else if($availability['applyoption']=='weekdays'){//monday-to-friday
                    $weekdays = [1,2,3,4,5];
                    ServiceProviderSlot::where([
                        'service_provider_id'=>$user->id,
                        'service_id'=>$category_service_type->service_id,
                        'category_id'=>$input['category_id'],
                    ])->whereIn('day',$weekdays)->delete();
                    foreach ($weekdays as $day) {
                       foreach ($availability['slots'] as $slot) {
                            $start_time = Carbon::parse($slot['start_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                            $end_time = Carbon::parse($slot['end_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                            $spavailability = new ServiceProviderSlot();
                            $spavailability->service_provider_id = $user->id;
                            $spavailability->service_id = $category_service_type->service_id;
                            $spavailability->category_id = $input['category_id'];
                            $spavailability->start_time = $start_time;
                            $spavailability->end_time = $end_time;
                            $spavailability->day = $day;
                            $spavailability->save();
                       }
                    }
                }
            }

            $user->categoriesData = $user->getCategorysData($user->id);
            $user->additionalsdocument = $user->getAdditionalsDocument($user->id);
            $user->spPrice = $user->getSpPrice($user->id);

            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('Subscribed  '), 'data' => $user], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage().' '.$ex->getLine()], 500);
        }
    }


    /**
     * @SWG\Post(
     *     path="/manual-update-services",
     *     description="Update Services Filter,Category,Service,Availibilty",
     * tags={"Service Provider"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="category_id",
     *         in="query",
     *         type="string",
     *         description="category id",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="filters",
     *         in="query",
     *         type="string",
     *         description="filters array [{'filter_id':9,'filter_option_ids':[13,14]}]",
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
    public static function postMannualSubscribeService(Request $request) {
        try{
            $user = Auth::user();
            // if(!$user->hasrole('service_provider')){
            //     return response(array('status' => "error", 'statuscode' => 400, 'message' =>'Invalid user role, must be role as service_provider'), 400);
            // }
             $rules = [
                    'category_id'=>'required|integer|exists:categories,id'
            ];
            $input = $request->all();
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $service = CategoryServiceType::where('category_id',$input['category_id'])->first();
            $input['category_services_type'] = [['id'=>$service->id,'available'=>'1','price'=>10,'minimmum_heads_up'=>5,'availability'=>['applyoption'=>'weekdays','days'=>[1,2,3,4,5],'slots'=>[['start_time'=>'09:00','end_time'=>'18:30']]]]];
            $timezone = $request->header('timezone');
            if(!$timezone){
                $timezone = 'Asia/Kolkata';
            }
            $input['slots'] = [['start_time'=>'09:00','end_time'=>'18:30']];
            $feature = Helper::getClientFeatureExistWithFeatureType('Dynamic Sections','Master Interval');
            if($feature){
                $slots =  Helper::getMasterSlots($timezone);
                if(count($slots) > 0){
                    $input['slots'] = $slots->toArray();
                }
            }
            $duration = '60';
            $unit_price = EnableService::where('type','unit_price')->first();
            if($unit_price){
                $duration = $unit_price->value * 60;
            }
            if(isset($input['category_services_type'])){
                foreach ($input['category_services_type'] as $category_service_type) {
                    $spservicetype = SpServiceType::firstOrCreate([
                        'sp_id'=>$user->id,
                        'category_service_id'=>$category_service_type['id']
                    ]);
                        // print_r($category_service_type);die;
                    if($spservicetype){
                        $service = CategoryServiceType::where('id',$category_service_type['id'])->first();
                        $spservicetype->available = $category_service_type['available'];
                        if($category_service_type['available']=="1")
                            $spservicetype->minimmum_heads_up = $category_service_type['minimmum_heads_up'];
                        if($service->price_fixed){
                            $spservicetype->price = $service->price_fixed;
                        }else{
                            if($category_service_type['available']=="1"){
                                if($category_service_type['price'] >= $service->price_minimum && $category_service_type['price']<=$service->price_maximum){
                                    $spservicetype->price = $category_service_type['price'];
                                }else{
                                    return response(array('status' => "error", 'statuscode' => 400, 'message' =>'Please select price into the range price_fixed'), 400);
                                }
                            }
                        }
                        $spservicetype->duration = $duration;
                        $spservicetype->save();
                        if($service && $service->service->need_availability){
                            if(!isset($category_service_type['isAvailabilityChanged']) || $category_service_type['isAvailabilityChanged']){
                                $availability = $category_service_type["availability"];
                                if($availability['applyoption']=='weekdays'){//monday-to-friday
                                    $weekdays = [1,2,3,4,5];
                                    ServiceProviderSlot::where([
                                        'service_provider_id'=>$user->id,
                                        'service_id'=>$service->service_id,
                                        'category_id'=>$input['category_id'],
                                    ])->whereIn('day',$weekdays)->delete();
                                    foreach ($weekdays as $day) {
                                       foreach ($input['slots'] as $slot) {
                                            $start_time = Carbon::parse($slot['start_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                                            $end_time = Carbon::parse($slot['end_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                                            $spavailability = new ServiceProviderSlot();
                                            $spavailability->service_provider_id = $user->id;
                                            $spavailability->service_id = $service->service_id;
                                            $spavailability->category_id = $input['category_id'];
                                            $spavailability->start_time = $start_time;
                                            $spavailability->end_time = $end_time;
                                            $spavailability->day = $day;
                                            $spavailability->save();
                                       }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if(isset($input['category_id'])){
                $category_service = CategoryServiceProvider::where(['sp_id'=>$user->id])->first();
                if(!$category_service){
                    $category_service =  new CategoryServiceProvider();
                    $category_service->sp_id = $user->id;
                }
                $category_service->category_id = $input['category_id'];
                $category_service->save();
            }
            if(isset($input['filters'])){
                foreach ($input['filters'] as $key => $filter) {
                        ServiceProviderFilterOption::where([
                            'sp_id'=>$user->id,
                            'filter_type_id'=>$filter['filter_id'],
                        ])->delete();
                    foreach ($filter['filter_option_ids'] as $filter_option_key => $filter_option) {
                        ServiceProviderFilterOption::firstOrCreate([
                            'sp_id'=>$user->id,
                            'filter_type_id'=>$filter['filter_id'],
                            'filter_option_id'=>$filter_option,
                        ]);
                    }
                }
            }
            $user->account_step = 5;
            $user->save();
            $user->subscriptions = $user->getSubscription($user);
            if($user->profile){
                $user->profile->bio = $user->profile->about;
                $user->totalRating =  $user->profile->rating;
            }
            $user->categoryData = $user->getCategoryData($user->id);
            $user->additionals = $user->getAdditionals($user->id);
            $user->services = $user->getServices($user->id);
            $user->filters = $user->getFilters($user->id);
            $user->patientCount = User::getTotalRequestDone($user->id);
            $user->reviewCount = Feedback::reviewCountByConsulatant($user->id);
            $token = $user->createToken('consult_app')->accessToken;
            $user->token = $token; 
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('Subscribed  '), 'data' => $user], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage().' '.$ex->getLine()], 500);
        }
    }

    

    /**
     * @SWG\Post(
     *     path="/accept-request",
     *     description="getPendingRequestByDate",
     * tags={"Service Provider"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="request_id",
     *         in="query",
     *         type="number",
     *         description=" Request Id",
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
    public static function postAcceptRequest(Request $request) {
    	try{
	    	$user = Auth::user();
	    	$rules = ['request_id' => 'required'];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
	    	$sr_request = \App\Model\Request::where('id',$request->request_id)->first();
	    	$message = 'Something went wrong';
	    	if($sr_request){
	    		if($sr_request->requesthistory->status=='pending'){
		    		$re_history = \App\Model\RequestHistory::where('request_id',$sr_request->id)->first();
		    		$re_history->status = 'accept';
		    		$re_history->save();
                    $notification = new Notification();
                    $notification->sender_id = $sr_request->to_user;
                    $notification->receiver_id = $sr_request->from_user;
                    $notification->module_id = $sr_request->id;
                    $notification->module ='request';
                    $notification->notification_type ='REQUEST_ACCEPTED';
                    $notification->message =__('notification.accept_req_text', ['vendor_name' => $sr_request->sr_info->name]);;
                    $notification->save();
                    $notification->push_notification(array($sr_request->from_user),array('pushType'=>'REQUEST_ACCEPTED','request_id'=>$sr_request->id,'message'=>__('notification.accept_req_text', ['vendor_name' => $sr_request->sr_info->name])));
		    		return response(['status' => "success", 'statuscode' => 200,'message' => __('Request Accepted '),'data'=>(object)[]], 200);
	    		}else{
	    			$message = 'Already Accepted';
	    		}
	    	}else{
	    		$message = 'No Request Found';
	    	}
	    	return response(array('status' => "error", 'statuscode' => 400, 'message' =>__($message)), 400);
    	}catch(Exception $ex){
    		return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
    	}
    } 

    /**
     * @SWG\Post(
     *     path="/start-chat",
     *     description="Start Chat",
     * tags={"Chat"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="request_id",
     *         in="query",
     *         type="number",
     *         description=" Request Id",
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
    public static function postStartChat(Request $request) {
        try{
            $user = Auth::user();
            $rules = ['request_id' => 'required'];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $service_id = \App\Model\Service::getServiceId('chat');
            $sr_request = \App\Model\Request::where(['id'=>$request->request_id,
                'service_id'=>$service_id])
            ->first();
            $message = 'Something went wrong';
            if($sr_request){
                if($sr_request->requesthistory->status=='in-progress'){
                    $re_history = \App\Model\RequestHistory::where('request_id',$sr_request->id)->first();
                    $re_history->save();
                    return response(['status' => "success", 'statuscode' => 200,'message' => __('Request Accepted '),'data'=>(object)[]], 200);
                }else{
                    $message = "can't chat start request status is ".$sr_request->requesthistory->status;
                }
            }else{
                $message = 'No Chat Request Found';
            }
            return response(array('status' => "error", 'statuscode' => 400, 'message' =>__($message)), 400);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }


     /**
     * @SWG\Post(
     *     path="/call-status",
     *     description="Call Status Change",
     * tags={"Chat"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="request_id",
     *         in="query",
     *         type="number",
     *         description=" Request Id",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="status",
     *         in="query",
     *         type="string",
     *         description=" Call Status CALL_RINGING,CALL_ACCEPTED,CALL_CANCELED,start,reached,start_service,cancel_service,completed",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="lat",
     *         in="query",
     *         type="string",
     *         description="Latitude",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="long",
     *         in="query",
     *         type="string",
     *         description="Longitude",
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
    public static function postCallStausChange(Request $request) {
        try{
            $user = Auth::user();
            $customer = false;
            if($user->hasrole('customer')){
                $customer = true;
            }
            $rules = [
                'request_id' => 'required|exists:requests,id',
                'status'=>["required" , "max:255", "in:CALL_RINGING,CALL_ACCEPTED,CALL_CANCELED,start,reached,start_service,cancel_service,completed"]
            ];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $dateznow = new DateTime("now", new DateTimeZone('UTC'));
            $datenow = $dateznow->format('Y-m-d H:i:s');

            $current_date = strtotime($dateznow->format('Y-m-d'));
            $sr_request = \App\Model\Request::where(['id'=>$request->request_id])->first();
            $d = new DateTime($sr_request->booking_date, new DateTimeZone('UTC'));
            $d = strtotime($d->format('Y-m-d'));
            if(\Config::get('client_connected') && \Config::get('client_data')->domain_name=='intely' && ($request->status == 'start' || $request->status == 'start_service')){
                if($current_date<$d){
                    return response(array('status' => "error", 'statuscode' => 400, 'message' =>'Service can not start before the appoitnment date'), 400);
                }
            }
            if($sr_request->requesthistory->status!='completed' && $sr_request->requesthistory->status!='failed' && $sr_request->requesthistory->status!='canceled' ){
                if($request->status == 'start' || $request->status == 'reached' || $request->status == 'start_service' || $request->status == 'cancel_service' || $request->status == 'completed'){

                    
                    // print_r($datenow);die;
                    if($request->status == 'completed'){
                        $action_ignore = false;
                        if(\Config::get('client_connected') && \Config::get('client_data')->domain_name=='intely'){
                            $action_ignore = true;
                        }
                        $request_date =  \App\Model\RequestDate::where(['request_id'=>$sr_request->id])->orderBy("id","DESC")->first();
                        if($request_date && !$action_ignore){
                            $end_date_time = strtotime($request_date->end_date_time);
                            $c_end_date_time = strtotime($datenow);
                            if($end_date_time>=$c_end_date_time){
                                $message = "you can't mark status complete beofre service end time";
                                return response(array('status' => "error", 'statuscode' => 400, 'message' =>__($message)), 400);
                            }
                        }
                    }
                    $sr_request->requesthistory->status = strtolower($request->status);
                    $sr_request->requesthistory->save();
                }
                if(isset($request->lat) && isset($request->long)){
                    $ls = new  \App\Model\LastLocation();
                    $ls->lat = $request->lat;
                    $ls->long = $request->long;
                    $ls->request_id = $request->request_id;
                    $ls->user_id = $sr_request->sr_info->id;
                    $ls->save();
                }
                $status = ucwords(strtolower(str_replace('_', ' ', $request->status)));
                $notification = new Notification();
                $notification->sender_id = $user->id;
                if($customer){
                    $notification->receiver_id = $sr_request->to_user;
                }else{
                    $notification->receiver_id = $sr_request->from_user;
                }
                $category_name = '';
                if($sr_request->sr_info->getCategoryData($sr_request->sr_info->id)){
                    $category_name = $sr_request->sr_info->getCategoryData($sr_request->sr_info->id)->name;
                }
                $input = $request->all();
                $custominfo = new \App\Model\CustomInfo();
                $custominfo->info_type = 'request_status';
                $custominfo->ref_table = 'request';
                $custominfo->ref_table_id = $sr_request->id;
                $custominfo->status = $request->status;
                $custominfo->save();

                $call_id = null;
                if(isset($input['call_id'])){
                    $call_id = $input['call_id'];
                }
                $notification->module_id = $sr_request->id;
                $notification->module ='request';
                $notification->notification_type = strtoupper($request->status);
                $notification->message =__('notification.call_req_text', ['user_name' => $user->name,'call_status'=>$status]);
                $notification->save();
                $notification->push_notification(
                    array($notification->receiver_id),
                    array('pushType'=>strtoupper($request->status),
                        'message'=>__('notification.call_req_text', ['user_name' => $user->name,'call_status'=>$status]),
                        'request_time'=>$sr_request->booking_date,
                        'service_type'=>$sr_request->servicetype->type,
                        'sender_name'=>$user->name,
                        'sender_image'=>$user->profile_image,
                        'vendor_category_name'=>$category_name,
                        'request_id'=>$sr_request->id,
                        'call_id'=>$call_id,
                    ));
                    if(!Helper::chargeFromSP()){
                        $deposit_to = array(
                            'user'=>$sr_request->sr_info,
                            'from_id'=>$sr_request->cus_info->id,
                            'request_id'=>$sr_request->id,
                            'status'=>'succeeded'
                        );
                        \App\Model\Transaction::updateDeposit($deposit_to);
                    }
                return response(['status' => "success",
                    'statuscode' => 200,
                    'message' => __('notification.call_req_text', ['user_name' => $user->name,'call_status'=>$status]),
                    'data'=>['call_id'=>$call_id,'status'=>$request->status]
                ], 200);
            }else{
                $message = "can't change status because status is ".$sr_request->requesthistory->status;
            }
            return response(array('status' => "error", 'statuscode' => 400, 'message' =>__($message)), 400);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }

     /**
     * @SWG\Get(
     *     path="/bank-accounts",
     *     description="Service Provider Bank Accounts",
     * tags={"Service Provider"},
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
    public static function getBankAccountsListing(Request $request) {
        try{
            $user = Auth::user();
            if(!$user->hasrole('service_provider')){
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>'Invalid Valid user role must be as service_provider'), 400);
            }
            $bank_accounts = [];
            $bank_accounts = $user->getAttachedBanks($user);
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('Bank Accounts Listing'), 'data' =>['bank_accounts'=>$bank_accounts]], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }

    /**
     * @SWG\Get(
     *     path="/revenue",
     *     description="Total Revenue",
     * tags={"Service Provider"},
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
    public static function getRevenue(Request $request) {
        try{
            $user = Auth::user();
            // if(!$user->hasrole('service_provider')){
            //     return response(array('status' => "error", 'statuscode' => 400, 'message' =>'Invalid Valid user role must be as service_provider'), 400);
            // }
            $start = isset($request->start_date)?$request->start_date:null;
            $end = isset($request->end_date)?$request->end_date:null;
            $requests_data = \App\Model\Request::getReqAnaliticsBySrPro($user->id);
            $revenu_res = \App\Model\Transaction::getRevenueBySrPro($user);
            $totalHours = User::getTotalRequestHours($user->id,$start,$end);
            $requests_data['totalRevenue'] = $revenu_res['totalRevenue'];
            $requests_data['monthlyRevenue'] = $revenu_res['monthlyRevenue'];
            $requests_data['totalHours'] = $totalHours;
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('revenue'), 'data'=>$requests_data], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }

         /**
     * @SWG\Post(
     *     path="/pre_screptions",
     *     description="pre_screptions",
     * tags={"Chat"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="request_id",
     *         in="query",
     *         type="number",
     *         description=" Request Id",
     *         required=true,
     *     ),     
     *  @SWG\Parameter(
     *         name="type",
     *         in="query",
     *         type="string",
     *         description=" pre_screption type manual,digital",
     *         required=true,
     *     ),     
     *  @SWG\Parameter(
     *         name="pre_scription_notes",
     *         in="query",
     *         type="string",
     *         description=" pre_scription_notes title in case of digital",
     *         required=false,
     *     ),     
     *  @SWG\Parameter(
     *         name="title",
     *         in="query",
     *         type="string",
     *         description=" title in case of manual",
     *         required=false,
     *     ),     
     *  @SWG\Parameter(
     *         name="image",
     *         in="query",
     *         type="string",
     *         description=" image array type in case of manual 'image':['jsp.png','abc.jpg']",
     *         required=true,
     *     ),     
     *  @SWG\Parameter(
     *         name="pre_scriptions",
     *         in="query",
     *         type="string",
     *         description=" pre_scriptions type in case of digital e.g 'pre_scriptions':[{'medicine_name':'tetstts','duration':'1 day','dosage_type':'tablet','dosage_timing':[{'time':'morning','with':'with'}],'image':['jsp.png']}]",
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

    public function postAddPreScriptions(Request $request){
        $user = Auth::user();
        $customer = false;
        if($user->hasrole('customer')){
            $customer = true;
        }
        $rules = [];
        $rules["type"] = "required|in:digital,manual";
        $rules["request_id"] = "required|exists:requests,id";
        if(isset($request->type) && $request->type=="digital"){
            $rules["pre_scription_notes"] = "required|string";
            $rules["pre_scriptions"] = "required|array|min:1";
            $rules["pre_scriptions.*.medicine_name"] = "required|string";
            $rules["pre_scriptions.*.duration"] = "required|string";
            $rules["pre_scriptions.*.dosage_type"] = "required|string";
            $rules["pre_scriptions.*.dosage_timing"] = "required||array|min:1";
            // $rules["pre_scriptions.*.image"] = "required|array|min:1";
        }
        if(isset($request->type) && $request->type=="manual"){
            $rules["title"] = "required|string";
            $rules["image"] = "required|array|min:1";
        }
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                $validator->getMessageBag()->first()), 400);
        }
        $input = $request->all();
        $prescription = new PreScription();
        $prescription->type = $input['type'];
        $prescription->request_id = $input['request_id'];
        $prescription->save();
        if($input['type']=="digital"){
            $prescription->pre_scription_notes = $input["pre_scription_notes"];
            if($input["pre_scriptions"]){
                foreach ($input["pre_scriptions"] as $pre_scription) {
                    $prescriptionmedicine = new PreScriptionMedicine();
                    $prescriptionmedicine->medicine_name = $pre_scription['medicine_name'];
                    $prescriptionmedicine->duration = $pre_scription['duration'];
                    $prescriptionmedicine->dosage_type = $pre_scription['dosage_type'];
                    $prescriptionmedicine->dosage_timing = json_encode($pre_scription['dosage_timing']);
                    $prescriptionmedicine->pre_scription_id = $prescription->id;
                    $prescriptionmedicine->save();
                }
            }
        }else{
            $prescription->title = $input['title'];
            foreach ($input['image'] as $image) {
                $modelimage = new ModelImage();
                $modelimage->image_name = $image;
                $modelimage->module_table = 'pre_scriptions';
                $modelimage->module_table_id = $prescription->id;
                $modelimage->save();
            }
        }
        $prescription->save();
        $sr_request = RequestData::where('id',$input['request_id'])->first();
        $sender_id = $user->id;
        if($customer){
            $receiver_id = $sr_request->to_user;
        }else{
            $receiver_id = $sr_request->from_user;
        }
        $notification = new Notification();
        $notification->sender_id = $sender_id;
        $notification->receiver_id = $receiver_id;
        $notification->module_id = $sr_request->id;
        $notification->module ='request';
        $notification->notification_type ='PRESCRIPTION_ADDED';
        $notification->message =__('Prescription added for your appointment');
        $notification->save();
        $notification->push_notification(array($receiver_id),array(
            'pushType'=>'PRESCRIPTION_ADDED',
            'request_id'=>$sr_request->id,
            'message'=>__('Prescription added for your appointment')
        ));
        return response(['status' => "success",
            'statuscode' => 200,
            'message' => __('prescription saved'),
            'data'=>(object)[]], 
            200);
    }
}
