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
use App\Model\Role,App\Model\FilterType;
use App\Model\Wallet,App\Model\ServiceProviderFilterOption;
use App\Model\Feedback,App\Model\Banner,App\Model\Cluster;
use App\Model\Profile,App\Model\Payment,App\Model\Service,App\Model\Coupon;
use App\Model\SocialAccount,App\Model\Subscription;
use Socialite,Exception;
use Intervention\Image\ImageManager;
use Carbon\Carbon;
use App\Notification;
class CouponController extends Controller
{

	public function __construct() {
        $this->middleware('auth')->except(['getCoupons']);
    }
	/**
     * @SWG\Get(
     *     path="/coupons",
     *     description="Get Coupon",
     * tags={"Coupon"},
     *  @SWG\Parameter(
     *         name="category_id",
     *         in="query",
     *         type="string",
     *         description="category_id",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="service_id",
     *         in="query",
     *         type="string",
     *         description="service_id",
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

     public static function getCoupons(Request $request) {
        try{
            $per_page = (isset($request->per_page)?$request->per_page:10);
            $parent_id = (isset($request->parent_id)?$request->parent_id:NULL);
            $dateznow = new DateTime("now", new DateTimeZone('UTC'));
            $datenow = $dateznow->format('Y-m-d');
            $coupons = Coupon::select('id','category_id','service_id','percent_off','value_off','minimum_value','start_date','end_date','limit','coupon_code','maximum_discount_amount')->orderBy('id','DESC')
                ->where(function($q) use($datenow) {
                  $q->where('end_date', '>=', $datenow)
                    ->orWhere('start_date', '>=', $datenow);
                })->where(function($query2) use ($request){
                    if(isset($request->category_id))
                        $query2->where('category_id','=',$request->category_id);
                    if(isset($request->service_id))
                        $query2->where('service_id','=',$request->service_id);
              })->cursorPaginate($per_page);
	         foreach ($coupons as $key => $coupon) {
                $used = Coupon::usedCoupon($coupon->id);
                $coupon->limit = $coupon->limit -  $used;
	            $coupon->discount_type = '';
	            $coupon->discount_value = '';
	            if($coupon->percent_off){
	                $coupon->discount_value = $coupon->percent_off;
	                $coupon->discount_type = 'percentage';
	            }else{
	                $coupon->discount_value = $coupon->value_off;
	                $coupon->discount_type = 'currency';
	            }
                if($coupon->service_id){
                    $coupon->service;
                    $coupon->service->name = $coupon->service->type;
                    unset($coupon->service->type);
                }
                if($coupon->category_id){
                    $coupon->category;
                    if($coupon->category->subcategory->count() > 0){
                        $coupon->category->is_subcategory = true;
                    }else{
                        $coupon->category->is_subcategory = false;
                    }
                }
	            unset($coupon->percent_off);
	            unset($coupon->value_off);
	        }
            $after = null;
            if($coupons->meta['next']){
                $after = $coupons->meta['next']->target;
            }
            $before = null;
            if($coupons->meta['previous']){
                $before = $coupons->meta['previous']->target;
            }
            $per_page = $coupons->perPage();
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('Coupon Listing'), 'data' =>['coupons'=>$coupons->items(),'after'=>$after,'before'=>$before,'per_page'=>$per_page]], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }
}
