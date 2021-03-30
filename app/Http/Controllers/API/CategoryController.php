<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;
use App\Model\Category;
use App\Model\ConsultClass;
use App\Model\Package,App\Model\UserPackage,App\Model\Transaction,App\Model\Payment;
use App\Model\AdditionalDetail;
use App\Model\SpAdditionalDetail;
use App\Notification;
use Illuminate\Support\Facades\Auth;
use Validator,Hash,Mail,DB;
use DateTime,DateTimeZone;
use Redirect,Response,File,Image;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use App\Model\EnableService;
use App\Model\CategoryServiceProvider;
class CategoryController extends Controller
{
    public function __construct() {
        $this->middleware('auth')->except(['getCategoriesViaServiceProvider','getServiceProvidersViaCategory','getCategories','getAdditionalFields','getPackages','getAdditionalDocuments']);
    }
    /**
     * @SWG\Get(
     *     path="/sp-categories",
     *     description="Get Class Categories",
     * tags={"Category"},
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

     public static function getCategoriesViaServiceProvider(Request $request) {
        try{
            $per_page = (isset($request->per_page)?$request->per_page:10);
            $categories = CategoryServiceProvider::select('category_id as id')->groupBy('category_id')->orderBy('id', 'desc')->cursorPaginate($per_page);
            foreach ($categories as $key => $category) {
                $category_data = $category->getCategoryData($category->id);
                $category->name = $category_data->name;
                $category->color_code = $category_data->color_code;
                $category->description = $category_data->description;
                $category->image = $category_data->image;
                $category->image_icon = $category_data->image_icon;
            }
            $after = null;
            if($categories->meta['next']){
                $after = $categories->meta['next']->target;
            }
            $before = null;
            if($categories->meta['previous']){
                $before = $categories->meta['previous']->target;
            }
            $per_page = $categories->perPage();
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('Categories '), 'data' =>['categories'=>$categories->items(),'after'=>$after,'before'=>$before,'per_page'=>$per_page]], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }


    /**
     * @SWG\Get(
     *     path="/categories",
     *     description="Get Category By Service Provider",
     * tags={"Classes"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="parent_id",
     *         in="query",
     *         type="number",
     *         description=" parent id for fetch subcategory",
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

     public  function getCategories(Request $request) {
        try{

            if(\Config('client_connected') && \Config::get("client_data")->domain_name=="iedu"){



                $per_page = (isset($request->per_page)?$request->per_page:10);
            $parent_id = (isset($request->parent_id)?$request->parent_id:NULL);
            $user_type = (isset($request->user_type)?$request->user_type:NULL);
            $local_resources = (isset($request->local_resources)?$request->local_resources:'0');
            $orderBy = 'desc';
            if(config('client_connected')){
                $orderBy = 'asc';
            }
            // print_r($local_resources);die;
            // $packages = \App\Helpers\Helper::checkFeatureExist([
            //                     'client_id'=>\Config::get('client_id'),
            //                     'feature_name'=>'Packages']);
            $Find_Local_Resource = Category::where('name','=','Find Local Resources')->first();
            if($Find_Local_Resource && $local_resources=='0'){
                $parentCategories = Category::where('parent_id',$parent_id)
                ->where('enable','=','1')
                ->where('id','!=',$Find_Local_Resource->id)
                ->with('subcategory')
                ->orderBy('id', $orderBy)
                ->get();
            }else{
                $parentCategories = Category::where('parent_id',$parent_id)
                ->where('enable','=','1')
                ->with('subcategory')
                ->orderBy('id', $orderBy)
                ->get();
            }
            $after = null;
            foreach ($parentCategories as $key => $category) {
                $category->packages = false;
                if(Package::where(['category_id'=>$category->id,'enable'=>'1'])->count()> 0){
                    $category->packages = true;
                }
                $category->is_filters = false;
                if($category->filters->count() > 0){
                    $category->is_filters = true;
                }
                $category->is_additionals = false;
                if($category->additionals->count() > 0){
                    $category->is_additionals = true;
                }
                $subcategory = Category::where('parent_id',$category->id)->where('enable','=','1')->count();
                if($category->parent_id==null){
                    $banner = \App\Model\Banner::where(['category_id'=>$category->id,'banner_type'=>'category'])->where('enable','=','1')->first();
                    if($banner)
                        $category->banner = $banner->image_web;
                }
                $category->doctor_detail = null;
                if(\Config('client_connected') && \Config::get("client_data")->domain_name=="physiotherapist"  && $category->parent_id==2){
                    $category->doctor_detail = \App\User::getSessionDoctorDetail($category->id);
                }
                if($subcategory > 0){
                    $category->is_subcategory = true;
                }else{
                    $category->is_subcategory = false;
                }
                unset($category->filters);
                // unset($category->subcategory);
                unset($category->additionals);
            }
            
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('Chat Listing'), 'data' =>['classes_category'=>$parentCategories]], 200);


            }else
            {
            $per_page = (isset($request->per_page)?$request->per_page:10);
            $parent_id = (isset($request->parent_id)?$request->parent_id:NULL);
            $user_type = (isset($request->user_type)?$request->user_type:NULL);
            $local_resources = (isset($request->local_resources)?$request->local_resources:'0');
            $orderBy = 'desc';
            if(config('client_connected')){
                $orderBy = 'asc';
            }
            // print_r($local_resources);die;
            // $packages = \App\Helpers\Helper::checkFeatureExist([
            //                     'client_id'=>\Config::get('client_id'),
            //                     'feature_name'=>'Packages']);
            $Find_Local_Resource = Category::where('name','=','Find Local Resources')->first();
            if($Find_Local_Resource && $local_resources=='0'){
                $parentCategories = Category::where('parent_id',$parent_id)
                ->where('enable','=','1')
                ->where('id','!=',$Find_Local_Resource->id)
                ->with('subcategory')
                ->orderBy('id', $orderBy)
                ->cursorPaginate($per_page);
            }else{
                $parentCategories = Category::where('parent_id',$parent_id)
                ->where('enable','=','1')
                ->with('subcategory')
                ->orderBy('id', $orderBy)
                ->cursorPaginate($per_page);
            }
            $after = null;
            foreach ($parentCategories as $key => $category) {
                $category->packages = false;
                if(Package::where(['category_id'=>$category->id,'enable'=>'1'])->count()> 0){
                    $category->packages = true;
                }
                $category->is_filters = false;
                if($category->filters->count() > 0){
                    $category->is_filters = true;
                }
                $category->is_additionals = false;
                if($category->additionals->count() > 0){
                    $category->is_additionals = true;
                }
                $subcategory = Category::where('parent_id',$category->id)->where('enable','=','1')->count();
                if($category->parent_id==null){
                    $banner = \App\Model\Banner::where(['category_id'=>$category->id,'banner_type'=>'category'])->where('enable','=','1')->first();
                    if($banner)
                        $category->banner = $banner->image_web;
                }
                $category->doctor_detail = null;
                if(\Config('client_connected') && \Config::get("client_data")->domain_name=="physiotherapist"  && $category->parent_id==2){
                    $category->doctor_detail = \App\User::getSessionDoctorDetail($category->id);
                }
                if($subcategory > 0){
                    $category->is_subcategory = true;
                }else{
                    $category->is_subcategory = false;
                }
                unset($category->filters);
                // unset($category->subcategory);
                unset($category->additionals);
            }
            if($parentCategories->meta['next']){
                $after = $parentCategories->meta['next']->target;
            }
            $before = null;
            if($parentCategories->meta['previous']){
                $before = $parentCategories->meta['previous']->target;
            }
            $per_page = $parentCategories->perPage();
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('Chat Listing'), 'data' =>['classes_category'=>$parentCategories->items(),'after'=>$after,'before'=>$before,'per_page'=>$per_page]], 200);
        }
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }

    /**
     * @SWG\Get(
     *     path="/pack-sub",
     *     description="Get Packages Detail By Category",
     * tags={"Subscription"},
     *  @SWG\Parameter(
     *         name="type",
     *         in="query",
     *         type="string",
     *         description=" type open,category",
     *         required=true,
     *     ),     
     *  @SWG\Parameter(
     *         name="category_id",
     *         in="query",
     *         type="number",
     *         description=" category_id id for fetch Packages Detail",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="filter_id",
     *         in="query",
     *         type="number",
     *         description=" filter_id id for fetch Packages Detail",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="doctor_id",
     *         in="query",
     *         type="number",
     *         description=" doctor_id id in purchased",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="list_by",
     *         in="query",
     *         type="number",
     *         description=" all,purchased,my_package,not_purchased",
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

     public  function getPackages(Request $request) {
        try{
            $user = Auth::guard('api')->user();
            $per_page = (isset($request->per_page)?$request->per_page:10);
            $rules = ['type' => 'required|in:open,category'];
            if(isset($request->category_id)){
                $rules['category_id'] = 'required|exists:categories,id';
            }
            if(isset($request->type) && $request->type=='category'){
                $rules['category_id'] = 'required|exists:categories,id';
            }
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            if(!isset($request->list_by)){
                $request->list_by = 'all';
            }
            $filter_id = isset($request->filter_id)?$request->filter_id:null;
            if($request->list_by=='purchased'){
                if(!$user){
                    return response(array('status' => "error", 'statuscode' => 500, 'message' =>'Your session has been expired, Please login again to continue.'), 500);
                }
                $packages = UserPackage::select('package_id','available_requests')->where('available_requests','>',0)->where(['user_id'=>$user->id])
                    ->whereHas('package', function($query) use ($request,$filter_id){
                        if(isset($request->doctor_id)){
                            $query->where('created_by', $request->doctor_id);
                            $query->orWhere('created_by', null);
                        }
                        return $query->where('filter_id', $request->filter_id)->where('package_type',$request->type);
                    })->orderBy('package_id',"DESC")->cursorPaginate();
                foreach ($packages as $package) {
                    $userpackage = Package::where(['id'=>$package->package_id])->first();
                    $package->subscribe = true;
                    $package->id = $userpackage->id;
                    $package->title = $userpackage->title;
                    $package->description = $userpackage->description;
                    $package->price = $userpackage->price;
                    $package->image = $userpackage->image;
                    $package->total_requests = $userpackage->total_requests;
                    $package->category_id = $userpackage->category_id;
                    $package->filter_id = $userpackage->filter_id;
                    $package->package_type = $userpackage->package_type;
                    $package->created_by = $userpackage->created_by;
                    $package->created_from =  null;
                    if($package->created_by){
                        $package->created_from = User::select(['id', 'name', 'email','phone','profile_image'])->where('id',$package->created_by)->first();
                    }
                }
            }elseif($request->list_by=='my_package'){
                $packages = Package::select('id','title','description','price','image','total_requests','category_id','package_type','created_by','filter_id')->where('category_id',$request->category_id)->where('filter_id',$filter_id)
                    ->where('package_type',$request->type)
                    ->where('created_by',$user->id)
                    ->orderBy('id',"DESC")
                    ->cursorPaginate($per_page);
                 if($user){
                    foreach ($packages as $package) {
                        $userpackage = UserPackage::where(['package_id'=>$package->id,'user_id'=>$user->id])->first();
                        $package->created_from =  null;
                        if($package->created_by){
                            $package->created_from = User::select(['id', 'name', 'email','phone','profile_image'])->where('id',$package->created_by)->first();
                        }
                        $package->subscribe = false;
                        if($userpackage && $userpackage->available_requests>0){
                            $package->subscribe = true;
                            $package->available_requests = $userpackage->available_requests;
                        }
                    }
                }
            }elseif($request->list_by=='not_purchased'){
                $packag_ids = UserPackage::where('available_requests','>',0)->where(['user_id'=>$user->id])->pluck('package_id')->toArray();
                $packages = Package::select('id','title','description','price','image','total_requests','category_id','package_type','created_by','filter_id')->where('category_id',$request->category_id)->where('filter_id',$filter_id)
                    ->where('package_type',$request->type)
                    ->whereNotIn('id',$packag_ids)
                    ->orderBy('id',"DESC")
                    ->cursorPaginate($per_page);
                 if($user){
                    foreach ($packages as $package) {
                        $userpackage = UserPackage::where(['package_id'=>$package->id,'user_id'=>$user->id])->first();
                        $package->created_from =  null;
                        if($package->created_by){
                            $package->created_from = User::select(['id', 'name', 'email','phone','profile_image'])->where('id',$package->created_by)->first();
                        }
                        $package->subscribe = false;
                        if($userpackage && $userpackage->available_requests>0){
                            $package->subscribe = true;
                            $package->available_requests = $userpackage->available_requests;
                        }
                    }
                }
            }else{
                $packages = Package::select('id','title','description','price','image','total_requests','category_id','package_type','created_by','filter_id')->where('category_id',$request->category_id)->where('filter_id',$filter_id)
                    ->where('package_type',$request->type)
                    ->where('enable','=','1')
                    ->orderBy('id',"DESC")
                    ->cursorPaginate($per_page);
                 if($user){
                    foreach ($packages as $package) {
                        $userpackage = UserPackage::where(['package_id'=>$package->id,'user_id'=>$user->id])->first();
                        $package->created_from =  null;
                        if($package->created_by){
                            $package->created_from = User::select(['id', 'name', 'email','phone','profile_image'])->where('id',$package->created_by)->first();
                        }
                        $package->subscribe = false;
                        if($userpackage && $userpackage->available_requests>0){
                            $package->subscribe = true;
                            $package->available_requests = $userpackage->available_requests;
                        }
                    }
                }
            }
            $after = null;
            if($packages->meta['next']){
                $after = $packages->meta['next']->target;
            }
            $before = null;
            if($packages->meta['previous']){
                $before = $packages->meta['previous']->target;
            }
            $per_page = $packages->perPage();
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('packages listing'), 'data' =>['packages'=>$packages->items(),'after'=>$after,'before'=>$before,'per_page'=>$per_page]], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }


/**
     * @SWG\Get(
     *     path="/pack-detail",
     *     description="Get Packages Detail",
     * tags={"Subscription"},  
     *  @SWG\Parameter(
     *         name="package_id",
     *         in="query",
     *         type="number",
     *         description="package_id for fetch Packages Detail",
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

     public  function getPackageDetail(Request $request) {
        try{
            $user = Auth::user();
            $per_page = (isset($request->per_page)?$request->per_page:10);
            $rules = ['package_id' => 'required|exists:packages,id'];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $package = Package::select('id','title','description','price','image','total_requests','category_id','created_by')->where('id',$request->package_id)->first();
            $package->created_from =  null;
            if($package->created_by){
                $package->created_from = User::select(['id', 'name', 'email','phone','profile_image'])->where('id',$package->created_by)->first();
            }
             if($user){
                $userpackage = UserPackage::where(['package_id'=>$package->id,'user_id'=>$user->id])->first();
                $package->subscribe = false;
                if($userpackage && $userpackage->available_requests>0){
                    $package->subscribe = true;
                    $package->available_requests = $userpackage->available_requests;
                }
            }
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('packages detail'), 'data' =>['detail'=>$package]], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }


        /**
     * @SWG\Post(
     *     path="/sub-pack",
     *     description="Post Subscribe Package Or Subscription",
     * tags={"Subscription"},
     *  @SWG\Parameter(
     *         name="plan_id",
     *         in="query",
     *         type="string",
     *         description=" Package or Subscribe Id",
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

     public  function postPackages(Request $request) {
        try{
            $user = Auth::user();
            $rules = ['plan_id' => 'required|exists:packages,id'];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $package = Package::where('id',$request->plan_id)->first();
            if($user->wallet->balance < $package->price){
                return response(['status' => "success", 'statuscode' => 200,'message' => __('insufficient balance'),'data'=>['amountNotSufficient'=>true]], 200);
            }
            $userpackage  = UserPackage::firstOrCreate(['user_id'=>$user->id,'package_id'=>$package->id]);
            if($userpackage){
                $userpackage->increment('available_requests',$package->total_requests);
            }
            $user->wallet->decrement('balance',$package->price);
            $transaction = Transaction::create(array(
                    'amount'=>$package->price,
                    'transaction_type'=>'add_package',
                    'status'=>'success',
                    'wallet_id'=>$user->wallet->id,
                    'closing_balance'=>$user->wallet->balance,
            ));
            if($transaction){
                $transaction->module_table = 'user_packages';
                $transaction->module_id = $userpackage->id;
                $transaction->save();
                $payment = Payment::create(array('from'=>1,'to'=>$user->id,'transaction_id'=>$transaction->id));
            }
            return response(array(
                'status' => "success",
                'statuscode' => 200,
                'data'=>(Object)[],
                'message' =>__('Subscribe Successfully')), 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }

       /**
     * @SWG\Get(
     *     path="/additional-details",
     *     description="Get Additional Detail",
     * tags={"additional"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="category_id",
     *         in="query",
     *         type="number",
     *         description=" category_id id for fetch additional-fields",
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

     public  function getAdditionalFields(Request $request) {
        try{
            $per_page = (isset($request->per_page)?$request->per_page:10);
            $rules = ['category_id' => 'required|exists:categories,id'];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $additionaldetail = AdditionalDetail::where('category_id',$request->category_id)
                ->where('is_enable','=','1')
                ->orderBy('name',"ASC")
                ->cursorPaginate($per_page);
            $after = null;
            if($additionaldetail->meta['next']){
                $after = $additionaldetail->meta['next']->target;
            }
            $before = null;
            if($additionaldetail->meta['previous']){
                $before = $additionaldetail->meta['previous']->target;
            }
            $per_page = $additionaldetail->perPage();
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('additionaldetail listing'), 'data' =>['additional_details'=>$additionaldetail->items(),'after'=>$after,'before'=>$before,'per_page'=>$per_page]], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }



     /**
     * @SWG\Get(
     *     path="/additional-documents",
     *     description="Get Additional documents",
     * tags={"additional"},
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

     public  function getAdditionalDocuments(Request $request) {
        
        try{
            $per_page = (isset($request->per_page)?$request->per_page:10);
            
            $additionaldetail = AdditionalDetail::where('is_enable','=','1')
                ->orderBy('name',"ASC")
                ->cursorPaginate($per_page);
            $after = null;
            if($additionaldetail->meta['next']){
                $after = $additionaldetail->meta['next']->target;
            }
            $before = null;
            if($additionaldetail->meta['previous']){
                $before = $additionaldetail->meta['previous']->target;
            }
            $per_page = $additionaldetail->perPage();
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('additionaldocument listing'), 'data' =>['additional_details'=>$additionaldetail->items(),'after'=>$after,'before'=>$before,'per_page'=>$per_page]], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }

/**
     * @SWG\Post(
     *     path="/additional-details",
     *     description="Get Additional Detail",
     * tags={"additional"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="additional_details",
     *         in="query",
     *         type="number",
     *         description=" array of additional_details",
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

     public  function postAdditionalFields(Request $request) {
        try{
            $user = Auth::user();
            $rules = [
                'fields' => 'required',
            ];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $rules = array();
            $input = $request->all();
            if(!is_array($input['fields'])){
                  $input['fields'] = json_decode($input['fields'],true);
            }
            if(is_array($input['fields'])){
                foreach ($input['fields'] as $key_sr =>  $additionaldetail) {
                    // print_r($input['fields'][0]["id"]);die;
                    $additionaldetail = (array) $additionaldetail;
                    $rules["fields.$key_sr.id"] ="required|exists:additional_details,id"; 
                    $rules["fields.$key_sr.documents"] ="required"; 
                    if(isset($additionaldetail['id'])){
                        if(isset($additionaldetail['documents'])){
                            foreach ($additionaldetail['documents'] as $key => $document) {
                                $AdditionalDetail = AdditionalDetail::where('id',$additionaldetail['id'])->first();
                                if($AdditionalDetail && $AdditionalDetail->type=='file'){
                                    $rules["fields.$key_sr.documents.$key.file_name"] ="required"; 
                                }else{
                                    $rules["fields.$key_sr.documents.$key.title"] ="required"; 
                                }
                            }
                        }
                    }
                }
            }
            // print_r($input);die;
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $input['additional_details'] = $input['fields'];
            if(is_array($input['additional_details'])){
                foreach ($input['additional_details'] as $key1 => $additionaldetail) {
                    $not_delete = [];
                    foreach ($additionaldetail['documents'] as $key2 => $document) {
                        $SpAdditionalDetail = null;
                        if(isset($document['id'])){
                            $SpAdditionalDetail = SpAdditionalDetail::where(['id'=>$document['id']])->first();
                        }
                        if($SpAdditionalDetail){
                            $not_delete[] = $SpAdditionalDetail->id;
                            if($SpAdditionalDetail->status!="approved" && isset($document['is_edit'])){
                                $SpAdditionalDetail->title = isset($document['title'])?$document['title']:$SpAdditionalDetail->title;
                                $SpAdditionalDetail->type = isset($document['type'])?$document['type']:$SpAdditionalDetail->type;
                                $SpAdditionalDetail->description = isset($document['description'])?$document['description']:$SpAdditionalDetail->description;
                                $SpAdditionalDetail->file_name = isset($document['file_name'])?$document['file_name']:$SpAdditionalDetail->file_name;
                                $SpAdditionalDetail->status = "in_progress";
                                $SpAdditionalDetail->save();
                            }
                        }else{
                            $add_tab = new SpAdditionalDetail();
                            $add_tab->additional_detail_id = $additionaldetail['id'];
                            $add_tab->sp_id = $user->id;
                            $add_tab->title = isset($document['title'])?$document['title']:$add_tab->title;
                            $add_tab->type = isset($document['type'])?$document['type']:'image';
                            $add_tab->description = isset($document['description'])?$document['description']:$add_tab->description;
                            $add_tab->file_name = isset($document['file_name'])?$document['file_name']:$add_tab->file_name;
                            $add_tab->save();
                            $not_delete[] = $add_tab->id;
                        }
                    }
                    SpAdditionalDetail::where([
                        'sp_id'=>$user->id,
                        'additional_detail_id'=>$additionaldetail['id']]
                    )->whereNotIn('id',$not_delete)->whereNotIn('status',['approved'])->delete();
                }
            }
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('additionaldetail added/updated'), 'data' =>['additionals'=>$input['fields']]], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }

     /**
     * @SWG\Post(
     *     path="/add-class",
     *     description="Create Class",
     * tags={"Classes"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="category_id",
     *         in="query",
     *         type="number",
     *         description=" Category  Id",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="price",
     *         in="query",
     *         type="number",
     *         description=" Call Price",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="date",
     *         in="query",
     *         type="string",
     *         description="date e.g YYYY-MM-DD=>2000-02-20",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="time",
     *         in="query",
     *         type="string",
     *         description="date e.g 22:10",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="name",
     *         in="query",
     *         type="string",
     *         description="service type call chat",
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
    public static function postCreateClass(Request $request){
    	try{
	    	$user = Auth::user();
	    	if(!$user->hasrole('service_provider')){
	    		return response(array('status' => "error", 'statuscode' => 400, 'message' =>'user role must be role as service_provider'), 400);
	    	}
	    	$rules = ['category_id' => 'required|exists:categories,id',
	    			 'price'=>'required',
	                 'date'=>'required|date|date_format:Y-m-d',
	                 'time'=>'required|date_format:H:i',
	                 'name'=>'required|unique:ct_classes,name'
	    	];
	        $validator = Validator::make($request->all(),$rules);
	        if ($validator->fails()) {
	            return response(array('status' => "error", 'statuscode' => 400, 'message' =>
	                $validator->getMessageBag()->first()), 400);
	        }
	        $input = $request->all();
            $category = $user->getCategoryData($user->id);
            if(!$category){
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>'You have not added any category please set your category'), 400);
            }
            if($input['category_id']!=$category->id){
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>"Can't create class into another category please select your category $category->name"), 400);
            }
	        $input['created_by'] = $user->id;
	        $timezone = $request->header('timezone');
            if(!$timezone){
                $$timezone = 'Asia/Kolkata';
            }
            $input['booking_date'] = Carbon::parse($request->date.' '.$request->time,$timezone)->setTimezone('UTC')->format('Y-m-d H:i:s');
	        ConsultClass::create($input);
	        return response(['status' => "success", 'statuscode' => 200,'message' => __('New Class Created ')], 200);
	    }catch(Exception $ex){
    		return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
    	}
    }

    /**
     * @SWG\Post(
     *     path="/class/status",
     *     description="Update Class Status",
     * tags={"Classes"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="class_id",
     *         in="query",
     *         type="number",
     *         description="Class  Id",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="status",
     *         in="query",
     *         type="string",
     *         description="status started,completed",
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
    public static function putClassStatusChange(Request $request){
    	try{
	    	$user = Auth::user();
	    	if(!$user->hasrole('service_provider')){
	    		return response(array('status' => "error", 'statuscode' => 400, 'message' =>'user role must be role as service_provider'), 400);
	    	}
	    	$rules = ['class_id' => 'required|exists:ct_classes,id',
	    			 'status'=>["required" , "max:255", "in:started,completed"]];
	        $validator = Validator::make($request->all(),$rules);
	        if ($validator->fails()) {
	            return response(array('status' => "error", 'statuscode' => 400, 'message' =>
	                $validator->getMessageBag()->first()), 400);
	        }
	        $consultclass = ConsultClass::where('id',$request->class_id)->first();
	        if($consultclass->status=='completed'){
	        	return response(['status' => "error", 'statuscode' => 500, 'message' => 'Class was already completed'], 500);
	        }
	        if($consultclass->status=='started' && $request->status=='started'){
	        	return response(['status' => "success", 'statuscode' => 200,'message' => __('Class Status Change'),'data'=>['CALLING_TYPE'=>$consultclass->calling_type]], 200);
	        }
	        $consultclass->status = $request->status;
	        if($request->status=='started'){
	        	$calling_type = EnableService::where('type','class_calling')->first();
	        	$consultclass->calling_type = $calling_type->value;
	        }
	        $consultclass->save();
	        if($consultclass->enroll_users->count()>0){
	        	$receiver_ids = [];
		        foreach ($consultclass->enroll_users as $key => $assinged_user) {
			        $notification = new Notification();
		            $notification->sender_id = $user->id;
		            $notification->receiver_id = $assinged_user->assinged_user;
		            $notification->module_id = $consultclass->id;
		            $notification->module ='class';
		            $notification->notification_type ='CLASS_'.strtoupper($request->status);
		            $notification->message = $consultclass->name." class ".$request->status;
		            $notification->save();
		            $receiver_ids[] = $assinged_user->assinged_user;
		        }
	            $notification->push_notification($receiver_ids,array('pushType'=>'CLASS_'.strtoupper($request->status),'message'=>__($consultclass->name." class ".$request->status)));
	        }
	        return response(['status' => "success", 'statuscode' => 200,'message' => __('Class Status Change'),'data'=>['CALLING_TYPE'=>$consultclass->calling_type]], 200);
	    }catch(Exception $ex){
    		return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
    	}
    } 

    /**
     * @SWG\Post(
     *     path="/class/join",
     *     description="Join Class",
     * tags={"Classes"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="class_id",
     *         in="query",
     *         type="number",
     *         description="Class  Id",
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
    public static function joinClassByUser(Request $request){
    	try{
	    	$user = Auth::user();
	    	$rules = ['class_id' => 'required|exists:ct_classes,id'];
	        $validator = Validator::make($request->all(),$rules);
	        if ($validator->fails()) {
	            return response(array('status' => "error", 'statuscode' => 400, 'message' =>
	                $validator->getMessageBag()->first()), 400);
	        }
	        $consultclass = ConsultClass::where('id',$request->class_id)->first();
	        if(!$user->hasrole('service_provider')){
	    		if(!$consultclass->isOccupied($consultclass->id,$user->id)){
		        	return response(['status' => "error", 'statuscode' => 500, 'message' => 'You are not enrolled into that class'], 500);
		        }
	    	}
	        if($consultclass->status=='added'){
	        	return response(['status' => "error", 'statuscode' => 500, 'message' => 'You will be able to join class once it get started.'], 500);
	        }else if($consultclass->status=='completed'){
	        	return response(['status' => "error", 'statuscode' => 500, 'message' => 'you will not be able to join class as it has been completed'], 500);
	        }
	        return response(['status' => "success", 'statuscode' => 200,'message' => __('Joining...'),'data'=>['CALLING_TYPE'=>$consultclass->calling_type]], 200);
	    }catch(Exception $ex){
    		return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
    	}
    }

    /**
     * @SWG\Get(
     *     path="/classes",
     *     description="Create Class",
     * tags={"Classes"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="type",
     *         in="query",
     *         type="string",
     *         description="Filter type e.g USER_COMPLETED,USER_OCCUPIED,VENDOR_ADDED,VENDOR_COMPLETED,USER_SIDE",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="CategoryId",
     *         in="query",
     *         type="string",
     *         description="CategoryId",
     *         required=false,
     *     ),     
     *  @SWG\Parameter(
     *         name="doctor_id",
     *         in="query",
     *         type="string",
     *         description="doctor_id",
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
    public static function getClasses(Request $request){
    	try{
	    	$user = Auth::user();
	    	$timezone = $request->header('timezone');
            if(!$timezone){
                $$timezone = 'Asia/Kolkata';
            }
            $rules = ['type' => ["required" , "max:255", "in:USER_COMPLETED,USER_OCCUPIED,VENDOR_ADDED,VENDOR_COMPLETED,USER_SIDE"]];
	        $validator = Validator::make($request->all(),$rules);
	        if ($validator->fails()) {
	            return response(array('status' => "error", 'statuscode' => 400, 'message' =>
	                $validator->getMessageBag()->first()), 400);
	        }
            $per_page = (isset($request->per_page)?$request->per_page:10);
            $classes = ConsultClass::select('id','name','status','calling_type','booking_date as class_date','created_at','booking_date as bookingDateUTC','price','category_id','created_by')
			->when(request('type'), function ($query) use($request,$user) {
				if($request->type=='USER_COMPLETED' || $request->type=='USER_OCCUPIED'){
				    $query->whereHas('enroll_users', function($query) use ($request,$user){
	                    return $query->where('assinged_user', $user->id);
	            	});
				}else if($request->type=='VENDOR_ADDED'){
            		$query->where('created_by',$user->id);
            	}else if($request->type=='VENDOR_COMPLETED'){
            		$query->where(['created_by'=>$user->id,'status'=>'completed']);
            	}else if($request->type=='USER_COMPLETED'){
            		$query->where(['status'=>'completed']);
            	} 
			})
			->when(request('CategoryId'), function ($query) use($user) {
            		$query->where('category_id',request('CategoryId'));
			})
            ->when(request('doctor_id'), function ($query) use($user) {
                    $query->where('created_by',request('doctor_id'));
            })
            ->orderBy('id', 'desc')->cursorPaginate($per_page);
			foreach ($classes as $key => $class) {
				$date = Carbon::parse($class->booking_date,'UTC')->setTimezone($timezone);
                $class->booking_date = $date->isoFormat('D MMMM YYYY, h:mm:ss a');
                $class->time = $date->isoFormat('h:mm a');
                $class->created_by = User::select('name','email','id','profile_image')->with('profile')->where('id',$class->created_by)->first();
                $class->category_data = Category::select('name','image','parent_id','id')->where('id',$class->category_id)->first();
                $class->totalAssignedUser = $class->enroll_users->count();
                $class->isOccupied = $class->isOccupied($class->id,$user->id);
                if($class->enroll_users){
                	$user_data = [];
                	foreach ($class->enroll_users as $user_ind => $user_en) {
                		$user_data[] = array('id'=>$user_en->user->id,
                			'name'=>$user_en->user->name,
                			'email'=>$user_en->user->email,
                			'phone'=>$user_en->user->phone,
                			'profile_image'=>$user_en->user->profile_image
                		);
                	}
                	$class->enroll_user_data = $user_data;
                }
                unset($class->enroll_users);
			}
	        $after = null;
            if($classes->meta['next']){
                $after = $classes->meta['next']->target;
            }
            $before = null;
            if($classes->meta['previous']){
                $before = $classes->meta['previous']->target;
            }
            $per_page = $classes->perPage();
	        return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('Class Listing'), 'data' =>['classes'=>$classes->items(),'after'=>$after,'before'=>$before,'per_page'=>$per_page]], 200);
	    }catch(Exception $ex){
    		return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
    	}
    }


    /**
     * @SWG\Get(
     *     path="/class/detail",
     *     description="Create Class",
     * tags={"Classes"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="class_id",
     *         in="query",
     *         type="string",
     *         description="class_id",
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
    public static function getClassDetail(Request $request){
        try{
            $user = Auth::user();
            $timezone = $request->header('timezone');
            if(!$timezone){
                $$timezone = 'Asia/Kolkata';
            }
            $rules = ['class_id' =>'required|exists:ct_classes,id'];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $per_page = (isset($request->per_page)?$request->per_page:10);
            $class = ConsultClass::select('id','name','status','calling_type','booking_date as class_date','created_at','booking_date as bookingDateUTC','price','category_id','created_by')
            ->where('id',$request->class_id)->first();
            $date = Carbon::parse($class->booking_date,'UTC')->setTimezone($timezone);
            $class->booking_date = $date->isoFormat('D MMMM YYYY, h:mm:ss a');
            $class->time = $date->isoFormat('h:mm a');
            $class->created_by = User::select('name','email','id','profile_image')->where('id',$class->created_by)->first();
            $class->category = Category::where('id',$class->category_id)->first();
            if($class->category->subcategory->count() > 0){
                $class->category->is_subcategory = true;
            }else{
                $class->category->is_subcategory = false;
            }
            $class->totalAssignedUser = $class->enroll_users->count();
            $class->isOccupied = $class->isOccupied($class->id,$user->id);
            if($class->enroll_users){
                $user_data = [];
                foreach ($class->enroll_users as $user_ind => $user_en) {
                    $user_data[] = array('id'=>$user_en->user->id,
                        'name'=>$user_en->user->name,
                        'email'=>$user_en->user->email,
                        'phone'=>$user_en->user->phone,
                        'profile_image'=>$user_en->user->profile_image
                    );
                }
                $class->enroll_user_data = $user_data;
            }
            unset($class->enroll_users);
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('Class Detail'), 'data' =>$class], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }
}
