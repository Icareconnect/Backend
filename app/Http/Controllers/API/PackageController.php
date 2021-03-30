<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Package;
use App\Model\MasterPackage;
use App\User;
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
use App\Model\Request as RequestData;
class PackageController extends Controller
{
      /**
     * @SWG\Post(
     *     path="/create-package",
     *     description="Create Package From Service Provider",
     * tags={"Payment"},
     *  @SWG\Parameter(
     *         name="title",
     *         in="query",
     *         type="string",
     *         description="title",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="description",
     *         in="query",
     *         type="string",
     *         description="description",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="price",
     *         in="query",
     *         type="string",
     *         description="price",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         name="total_requests",
     *         in="query",
     *         type="string",
     *         description="total_requests",
     *         required=true,
     *     ),
     *    @SWG\Parameter(
     *      name="image",
     *      in="formData",
     *      description="image",
     *      required=true,
     *      type="file"
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
     */
    public function createPackage(Request $request)
    {
    	try{
	    	$user = Auth::user();
	        $msg = [];
	        $rules = [
	                'title' => 'required',
	                'description' => 'required|string',
	                'price'      => 'required|integer|min:1',
	                'image' => 'required',
	                'total_requests' => 'required|integer|min:1',
	          ];
	          $input = $request->all();
	          if(isset($request->image) && $request->hasfile('image')){
	             $rules['image']='required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=480,min_height=400';
	             $msg['image.dimensions'] = "image should be min_width=480,min_height=400";
	         }
	         $validator = \Validator::make($request->all(),$rules,$msg);
	          if ($validator->fails()) {
		            return response(array('status' => "error", 'statuscode' => 400, 'message' =>
		                $validator->getMessageBag()->first()), 400);
		        }
	          $category_id = null;
	          $enable = '1';
	          if(isset($input['category']) && $input['category']){
	            $category_id = $input['category'];
	          }
	          $package = new Package();
	          if($request->hasfile('image')) {
	            if ($image = $request->file('image')) {
	                $extension = $image->getClientOriginalExtension();
	                $filename = str_replace(' ','', md5(time()).'_'.$image->getClientOriginalName());
	                $thumb = \Image::make($image)->resize(100, 100,
	                  function ($constraint) {
	                      $constraint->aspectRatio();
	                  })->encode($extension);
	                $normal = \Image::make($image)->resize(400, 480,
	                  function ($constraint) {
	                      $constraint->aspectRatio();
	                  })->encode($extension);
	                $big = \Image::make($image)->encode($extension);
	                $_800x800 = \Image::make($image)->resize(800, 800,
	                  function ($constraint) {
	                      $constraint->aspectRatio();
	                  })->encode($extension);
	                $_400x400 = \Image::make($image)->resize(400, 400,
	                  function ($constraint) {
	                      $constraint->aspectRatio();
	                  })->encode($extension);
	                \Storage::disk('spaces')->put('thumbs/'.$filename, (string)$thumb, 'public');
	                \Storage::disk('spaces')->put('uploads/'.$filename, (string)$normal, 'public');
	                \Storage::disk('spaces')->put('original/'.$filename, (string)$big, 'public');
	                \Storage::disk('spaces')->put('800x800/'.$filename, (string)$_800x800, 'public');
	                \Storage::disk('spaces')->put('400x400/'.$filename, (string)$_400x400, 'public');
	                \Storage::disk('spaces')->put('original/'.$filename, (string)$big, 'public');
	                $package->image = $filename;
	            }
	          }else{
	          	$package->image = $input['image'];
	          }
	          $package->title = $input['title'];
	          $package->description = $input['description'];
	          $package->price = $input['price'];
	          $package->total_requests = $input['total_requests'];
	          $package->package_type = 'open';
	          $package->enable = $enable;
	          $package->created_by = $user->id;
	          $package->save();
	          $package->created_from =  null;
	          if($package->created_by){
	             $package->created_from = User::select(['id', 'name', 'email','phone','profile_image'])->where('id',$package->created_by)->first();
	           }
	          return response(['status' => 'success', 'statuscode' => 200, 'message' => __('Package Created Successfully !'),'data'=>['package'=>$package]], 200);
          }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }


    /**
     * @SWG\Get(
     *     path="/support-packages",
     *     description="Support Question Packages",
     * tags={"Support"},
     *     security={
     *     {"Bearer": {}},
     *   },
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getSupportPackage(Request $request){
    	$per_page = (isset($request->per_page)?$request->per_page:10);
        $support_packages = MasterPackage::select('id','title','image_icon','description','color_code','price')->where('type','support_package')
            ->orderBy('id', 'desc')
            ->cursorPaginate($per_page);
        $after = null;
        if($support_packages->meta['next']){
            $after = $support_packages->meta['next']->target;
        }
        $before = null;
        if($support_packages->meta['previous']){
            $before = $support_packages->meta['previous']->target;
        }
        $per_page = $support_packages->perPage();
        return response([
            'status' => "success",
            'statuscode' => 200,
            'message' => __("$request->type listing"),
            'data' =>['support_packages'=>$support_packages->items(),'after'=>$after,'before'=>$before,'per_page'=>$per_page]
        	],200);
    }
}
