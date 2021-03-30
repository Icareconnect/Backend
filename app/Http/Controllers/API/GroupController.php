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
use App\Model\Group;
use App\Model\SubscribePlan;
use App\Model\GroupVendor;
use App\Model\Profile;
use Socialite,Exception;
use Intervention\Image\ImageManager;
use Carbon\Carbon;
use App\Notification;
class GroupController extends Controller
{

	/**
     * @SWG\Get(
     *     path="/groups",
     *     description="Group Listing From Category ",
     * tags={"Groups"},
     *  @SWG\Parameter(
     *         name="category_id",
     *         in="query",
     *         type="string",
     *         description="Category ID",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="search",
     *         in="query",
     *         type="string",
     *         description="Search By Group Name",
     *         required=false,
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function groupsListing(Request $request,Group $group)
    {

        $rules = [
                'category_id' => 'required|exists:categories,id',
        ];
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                $validator->getMessageBag()->first()), 400);
        }
        $per_page = (isset($request->per_page)?$request->per_page:10);
        $group = $group->newQuery();
        $group->where('category_id',$request->category_id);
        if($request->has('search')){
            if($request->search){
                $group->where('name','like','%'.$request->search.'%');
            }
        }
        $groups = $group->orderBy('id', 'desc')->cursorPaginate($per_page);
        $after = null;
        if($groups->meta['next']){
            $after = $groups->meta['next']->target;
        }
        $before = null;
        if($groups->meta['previous']){
            $before = $groups->meta['previous']->target;
        }
        $per_page = $groups->perPage();
        return response([
            'status' => "success",
            'statuscode' => 200,
            'message' => __('Group List '),
            'data' =>['groups'=>$groups->items(),'after'=>$after,'before'=>$before,'per_page'=>$per_page]],
            200);
    }
    

        /**
     * @SWG\Get(
     *     path="/group-doctors",
     *     description="Group Doctor Listing",
     * tags={"Groups"},
     *  @SWG\Parameter(
     *         name="group_id",
     *         in="query",
     *         type="string",
     *         description="Group ID",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="search",
     *         in="query",
     *         type="string",
     *         description="Search Doctor Name",
     *         required=false,
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function groupsDoctorListing(Request $request,GroupVendor $groupvendors)
    {

        $rules = [
                'group_id' => 'required|exists:groups,id',
        ];
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                $validator->getMessageBag()->first()), 400);
        }
        $per_page = (isset($request->per_page)?$request->per_page:10);
        $groupvendors = $groupvendors->newQuery();
        $groupvendors->where('group_id',$request->group_id);
        $datenow = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        $vendor_ids = SubscribePlan::where('expired_on','>',$datenow)->whereHas('plan', function ($query) {
                return $query->whereIn('plan_id',[
                    'com.mp2r.premium','com.mp2r.executive'
                ]);
        })->pluck('user_id')->toArray();
        $groupvendors->whereIn('user_id',$vendor_ids);
        if($request->has('search')){
            $groupvendors->whereHas('vendor', function ($query) use($request) {
                    $query->where('name','like','%'.$request->search.'%');
            });
        }
        $group_doctors = $groupvendors->orderBy('id', 'desc')->cursorPaginate($per_page);
        foreach ($group_doctors as $key => $group_doctor) {
            $group_doctor->doctor_data = User::getDoctorDetail($group_doctor->user_id);
        }
        $after = null;
        if($group_doctors->meta['next']){
            $after = $group_doctors->meta['next']->target;
        }
        $before = null;
        if($group_doctors->meta['previous']){
            $before = $group_doctors->meta['previous']->target;
        }
        $per_page = $group_doctors->perPage();
        return response([
            'status' => "success",
            'statuscode' => 200,
            'message' => __('Group Doctor '),
            'data' =>['group_doctors'=>$group_doctors->items(),'after'=>$after,'before'=>$before,'per_page'=>$per_page]],
            200);
    }

    /**
     * @SWG\Post(
     *     path="/group/create",
     *     description="Create Group",
     * tags={"Groups"},
     *  @SWG\Parameter(
     *         name="category_id",
     *         in="query",
     *         type="string",
     *         description="Category ID",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="name",
     *         in="query",
     *         type="string",
     *         description="Group Name",
     *         required=true,
     *     ),     
     *  @SWG\Parameter(
     *         name="image",
     *         in="query",
     *         type="string",
     *         description="Image Name",
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
    public function createGroup(Request $request)
    {

        $rules = [
                'category_id' => 'required|exists:categories,id',
                'name' => 'required',
        ];
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                $validator->getMessageBag()->first()), 400);
        }
        $group =  Group::firstOrCreate(['name'=>$request->name,'category_id'=>$request->category_id]);
        if(isset($request->image)){
        	$group->image = $request->image;
        }
        if(Auth::guard('api')->check()){
            
        }
        $group->save();
        return response([
            'status' => "success",
            'statuscode' => 200,
            'message' => __('Group Created'),
            'data' =>['group'=>$group]],
            200);
    }    



    /**
     * @SWG\Post(
     *     path="/group/assign",
     *     description="Assign Vendor to Group",
     * tags={"Groups"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="group_id",
     *         in="query",
     *         type="string",
     *         description="Group Id",
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function assignVendorToGroup(Request $request)
    {
    	$user = Auth::user();
        $rules = [
                'group_id' => 'required|exists:groups,id',
        ];
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                $validator->getMessageBag()->first()), 400);
        }
        $group = GroupVendor::where(['user_id'=>$user->id])->first();
        if($group){
        	$message = 'You are already assigned to another Group';
        	if($group->group_id==$request->group_id){
        		$message = 'You are already assigned to this Group';
        	}
        	return response([
	            'status' => "error",
	            'statuscode' => 400,
	            'message' => __($message),
	            'data' =>['group'=>$group]],
            400);
        }else{
        	$group = new GroupVendor();
        	$group->user_id = $user->id;
        	$group->group_id = $request->group_id;
        	$group->save();
	        return response([
	            'status' => "success",
	            'statuscode' => 200,
	            'message' => __('Group Assigned'),
	            'data' =>['group'=>$group]],
	            200);
        }
    }

}
