<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Config,DB,Carbon\Carbon;
use Illuminate\Support\Str;

use App\User,App\Notification;
use Illuminate\Support\Facades\Auth;
use Validator;
use Hash;
use Mail;
use DateTime,DateTimeZone;
use Redirect,Response,File;
use Image;
use Illuminate\Support\Facades\URL;
use App\Helpers\Helper;
use App\Model\Role,App\Model\UserMasterPreference;
use App\Model\Profile;
use App\Model\CustomUserField;
use App\Model\Wallet;
use App\Model\SocialAccount;
class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = User::whereHas('roles', function ($query) {
                           $query->where('name','customer');
                        })->orderBy('id','DESC')->get();
        return view('admin.customers')->with(['customers'=>$customers]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function patientList()
    {
        $user = Auth::user();
        $customers = User::whereHas('roles', function ($query) {
                           $query->where('name','customer');
                        })->where('created_by',$user->id)->orderBy('id','DESC')->get();
        return view('admin.customers.patient_list')->with(['customers'=>$customers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPatientCreate()
    {
        $user = Auth::user();
        return view('admin.customers.patient_add');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function postPatientCreate(Request $request)
    {
        $rules = [
            'email' => 'required|email|unique:users,email',
            'name'=>'required',
            'phone'=>'required|unique:users,phone',
            'dob'=>'required',
            'source'=>'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $admin = Auth::user();
        $datenow = new DateTime("now", new DateTimeZone('UTC'));
        $datenowone = $datenow->format('Y-m-d H:i:s');
        $input = $request->all();
        $input['country_code'] = '+91';
        $input['password'] = bcrypt('password');
        $user = User::create($input);
        $user->account_step = 5;
        $user->account_verified = $datenowone;
        $user->provider_type = 'email';
        $user->device_type = 'WEB';
        $user->created_by = $admin->id;
        $user->reference_code = Str::random(10).$user->id;
        $user->save();

        $wallet = new Wallet();
        $wallet->balance = 0;
        $wallet->user_id = $user->id;
        $wallet->save();
        $role = Role::where('name','customer')->first();
        if($role){
            $user->roles()->attach($role);
        }
        $profile = New Profile();
        $profile->dob = isset($input['dob'])?$input['dob']:'0000-00-00';
        $profile->user_id = $user->id;
        $profile->about = isset($input['about'])?$input['about']:'';
        $profile->save();
        return redirect('admin/patients');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        if(!$user->hasrole('customer')){
           abort(404);
        }
        if($user->profile_image){
            $user->profile_image = url('/').'/media/'.$user->profile_image;
        }else{
            $user->profile_image = url('/').'/default/user.jpg';
        }
        return view('admin.customers.view')->with(['customer'=>$user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('admin.customer_update')->with(['customer'=>$user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function getEditPatient(Request $request,$user_id)
    {
        $user = User::where('id',$user_id)->first();
        return view('admin.customers.patient_update')->with(['customer'=>$user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        if(!isset($request->account_verify_ajax)){
            $rules = [
                'name' => 'required',
            ];
            if(isset($request->phone)){
                $rules['phone'] = 'unique:users,phone,' . $user->id;
            }
            if(isset($request->email)){
                $rules['email'] = 'email|unique:users,email,' . $user->id;
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            if(!$user->profile){
                $profile = New Profile();
                $profile->dob = '0000-00-00';
                $profile->user_id = $user->id;
                $profile->save();
            }
            $profile = Profile::where('user_id',$user->id)->first();  
            if(isset($request->dob)){
                $orgDate = $request->dob;  
                $profile->dob = date("Y-m-d", strtotime($orgDate));
            }
            if(isset($request->about)){
                $profile->about = $request->about;  
            }
            $profile->save();
            if(isset($request->phone)){
                $user->phone = $request->input('phone');
            }
            if(isset($request->source)){
                $user->source = $request->input('source');
            }
            if(isset($request->email)){
                $user->email = $request->input('email');
            }
            $user->name = $request->input('name');
            $user->save();
            return back()->with("status", "User updated");
        }else{
            if(!$user->account_verified){
                $admin = Auth::user();
                $dateznow = new DateTime("now", new DateTimeZone('UTC'));
                $datenow = $dateznow->format('Y-m-d H:i:s');
                $user->account_verified = $datenow;

                $notification = new Notification();
                $notification->sender_id = $admin->id;
                $notification->receiver_id = $user->id;
                $notification->module_id = $user->id;
                $notification->module ='users';
                $notification->notification_type ='PROFILE_APPROVED';
                $notification->message =__('Your Account has been approved');;
                $notification->save();
                $notification->push_notification(array($user->id),array('pushType'=>'PROFILE_APPROVED','message'=>__('Your Account has been approved')));
            }
            $user->save();
            return response()->json(['status'=>'success']);
        }
    }

    public function deleteCustomer(Request $request){
        $user_id = $request->user_id;
        if(is_array($request->user_id)){
            foreach ($request->user_id as $key => $user_id) {
                $user = User::where('id',$user_id)->first();
                if($user){
                    SocialAccount::where("user_id",$user->id)->delete();
                    $userTokens = $user->tokens;
                    if($userTokens){
                        foreach($userTokens as $token) {
                            $token->revoke();   
                        }
                    }
                    $user->email = null;
                    $user->phone = null;
                    $user->fcm_id = null;
                    $user->apn_token = null;
                    $user->socket_id = null;
                    if($user->save()){
                        $role = \App\Model\Role::where('name','customer')->first();
                        if($user->hasRole('customer')){
                            $user->roles()->detach($role);
                            $archived_user = \App\Model\Role::firstOrCreate(['name'=>'archived_user']);
                            $user->roles()->attach($archived_user);
                        }
                    }
                }
            }
        }else{
            $user = User::where('id',$user_id)->first();
            if($user){
                SocialAccount::where("user_id",$user->id)->delete();
                $userTokens = $user->tokens;
                if($userTokens){
                    foreach($userTokens as $token) {
                        $token->revoke();   
                    }
                }
                $user->email = null;
                $user->phone = null;
                $user->fcm_id = null;
                $user->apn_token = null;
                $user->socket_id = null;
                if($user->save()){
                    $role = \App\Model\Role::where('name','customer')->first();
                    if($user->hasRole('customer')){
                        $user->roles()->detach($role);
                        $archived_user = \App\Model\Role::firstOrCreate(['name'=>'archived_user']);
                        $user->roles()->attach($archived_user);
                    }
                }
            }
        }
        return response()->json(['status'=>'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
