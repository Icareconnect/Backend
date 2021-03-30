<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Model\Role;
use App\Model\State;
use App\Notification;
use App\Helpers\Helper;
use App\Model\Wallet;
use App\Model\Profile;
use App\Model\Feedback;
use App\Model\Category;
use App\Model\Insurance;
use App\Model\CustomField;
use DateTime,DateTimeZone;
use App\Model\UserInsurance;
use App\Model\MasterPreference;
use App\Model\SpAdditionalDetail;
use App\Model\UserMasterPreference;
use Illuminate\Http\Request;
use App\Imports\UsersImport;
use App\Model\SocialAccount;
use App\Model\CustomUserField;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\CategoriesTrait;
use App\Model\CategoryServiceProvider;
use Config;
use Ixudra\Curl\Facades\Curl;
class ConsultantController extends Controller{

    use CategoriesTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
         $consultants = User::with('userInsurances.insurance')->whereHas('roles', function ($query) {
                           $query->where('name','service_provider');
                        })->where('permission',null)->orderBy('id','DESC')->get();
         foreach($consultants as $consultant){
            $names = [];
            foreach ($consultant->userinsurances as $user_insurance) {
                $names[] = $user_insurance->insurance->name;
            }
            $consultant->filter = false;
            $consultant->categoryData = $consultant->getCategoryData($consultant->id);
            if($consultant->categoryData && $consultant->categoryData->is_filters){
                $filters_name = \App\Model\FilterType::getUserFiltersNameByCategory($consultant->categoryData->id,$consultant->id);
                if(count($filters_name)>0){
                    $consultant->filter = true;
                    $consultant->filters_name = implode(',', $filters_name);
                }
            }
            $consultant->patientCount = User::getTotalRequestDone($consultant->id);
            $consultant->insurance_names = implode(', ', $names);
            $consultant->date = \Carbon\Carbon::parse($consultant->created_at,'UTC')->setTimezone('Asia/Kolkata')->format('d');
            $consultant->month = \Carbon\Carbon::parse($consultant->created_at,'UTC')->setTimezone('Asia/Kolkata')->format('F');
            $consultant->year = \Carbon\Carbon::parse($consultant->created_at,'UTC')->setTimezone('Asia/Kolkata')->format('Y');

            // print_r($consultant);die;
            $consultant->insurance_names = implode(', ', $names);
            if(Config('client_connected') && (Config::get("client_data")->domain_name=="mp2r" || Config::get("client_data")->domain_name=="food")){
                 $plan_names=[];
                 $datenow = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                 $subscribeplans = \App\Model\SubscribePlan::where(['user_id'=>$consultant->id])
                     ->where('expired_on','>',$datenow)->get();
                    if($subscribeplans->count()>0){
                        foreach ($subscribeplans as $key => $subscribeplan) {
                            $plan_names[] = $subscribeplan->plan->name;
                        }
                    }
                    $consultant->plan_names = $plan_names;
            }
         }
        return view('admin.consultants')->with(['consultants' => $consultants]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        $parentCategories = $this->parentCategories();
        return view('admin.consultants.add')->with(['parentCategories'=>$parentCategories]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $input = $request->all();
        $rules = [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
                'category' => 'required',
                'experience' => 'required',
        ];
        if(isset($input['phone'])){
            $rules['phone'] = 'required|unique:users,phone';
        }else{
            $input['phone'] = null;
        }
        $validator = \Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $request["status"] = 'verified';
        if ($request->hasfile('profile_image')) {
            if ($image = $request->file('profile_image')) {
                $extension = $image->getClientOriginalExtension();
                \Illuminate\Support\Facades\Storage::disk('media')->put(time() . $image->getFilename() . '.' . $extension, \Illuminate\Support\Facades\File::get($image));
                $input['profile_image'] = time() . $image->getFilename() . '.' . $extension;
            }
        }
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        if($user){
            $user = $user->createStripeCustomer($user);
            $user->provider_type = 'email';
            $user->save();
            $wallet = new Wallet();
            $wallet->balance = 0;
            $wallet->user_id = $user->id;
            $wallet->save();
            $role = Role::where('name','service_provider')->first();
            if($role){
                $user->roles()->attach($role);
            }
            $profile = new Profile();
            $profile->user_id = $user->id;
            if(isset($input['speciality'])){
                $profile->speciality = $input['speciality'];
            }
            if(isset($input['call_price'])){
                $profile->call_price = $input['call_price'];
            }
            if(isset($input['chat_price'])){
                $profile->chat_price = $input['chat_price'];
            }
            if(isset($input['experience'])){
                $profile->experience = $input['experience'];
            }
            if(isset($input['category'])){
                if($user->hasrole('service_provider')){
                    $category_service = CategoryServiceProvider::where(['sp_id'=>$user->id])->first();
                    if(!$category_service){
                        $category_service =  new CategoryServiceProvider();
                        $category_service->sp_id = $user->id;
                    }
                    $category_service->category_id = $input['category'];
                    $category_service->save();
                }
            }
            $category_service->save();
            return redirect('admin/consultants');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user){
        if(!$user->hasrole('service_provider')){
           abort(404);
        }
        $user->reviewCount = Feedback::reviewCountByConsulatant($user->id);
        $user->recentReviews = Feedback::recentReviewByConsulatant($user->id);
        $user->additionals = $user->getAdditionals($user->id);
        $user->master_preferences = \App\Model\MasterPreference::getMasterPreferences($user->id);
        // print_r($user);die;
        return view('admin.consultants.view')->with(['consultant'=>$user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user){
        $education = null;
        $qualification = null;
        $zip_code = null;
        $parentCategories = $this->parentCategories();
        $states = State::where('country_id', '=', 231)->whereNotIn('name', ["Byram", "Cokato", "District of Columbia", "Lowa", "Medfield", "New Jersy", "Ontario", "Ramey", "Sublimity", "Trimble"])->pluck('name', 'id');
        $insurances = Insurance::where('enable', '1')->orderBy('name', "ASC")->pluck('name', 'id');
        $custom_field = CustomField::where(['field_name' => 'Zip Code', 'user_type' => 3])->first();
        if($custom_field){
            $zip_code = CustomUserField::where('user_id', $user->id)->where('custom_field_id', $custom_field->id)->first();
        }
        $edu_field = CustomField::where(['field_name' => 'Education', 'user_type' => 3])->first();
        $qualification_f = CustomField::where(['field_name' => 'Qualification', 'user_type' => 3])->first();
        if($edu_field){
            $education = CustomUserField::where('user_id', $user->id)->where('custom_field_id', $edu_field->id)->first();
        }
        if($qualification_f){
            $qualification = CustomUserField::where('user_id', $user->id)->where('custom_field_id', $qualification_f->id)->first();
        }
        $master_preferences = MasterPreference::getMasterPreferences($user->id);
        $user_insurances = UserInsurance::where('user_id', $user->id)->get();
        $user->category = $user->getCategoryData($user->id);
        $user_insurances_ids = [];
        foreach ($user_insurances as $user_insurance) {
            $user_insurances_ids[] = $user_insurance->insurance_id;
        }
        return view('admin.consultant_update')->with([
            'consultant'=>$user,
            'states' => $states, 
            'zip_code' => $zip_code, 
            'education' => $education, 
            'qualification' => $qualification, 
            'master_preferences' => $master_preferences, 
            'insurances' => $insurances,
            'parentCategories'=>$parentCategories, 
            'user_insurances_ids' => $user_insurances_ids,
        ]);
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
        if(isset($request->account_active_ajax)){
            $admin = Auth::user();
            $text = "Active";
            if($user->account_active){
                $text = "InActive";
                $user->account_active = null;
            }else{
                $dateznow = new DateTime("now", new DateTimeZone('UTC'));
                $datenow = $dateznow->format('Y-m-d H:i:s');
                $user->account_active = $datenow;
            }
            $message = "Your Account has been mark $text";
            if(Config('client_connected') && (Config::get("client_data")->domain_name=="intely")){
                $message = "All Documents have been approved";
            }
            $user->account_covid_rejected = null;
            $user->custom_message = null;
            $notification = new Notification();
            $notification->module ='users';
            $notification->module_id = $user->id;
            $notification->sender_id = $admin->id;
            $notification->receiver_id = $user->id;
            $notification->notification_type ='PROFILE_ACTIVE';
            $notification->message =__($message);
            $notification->save();
            $notification->push_notification(array($user->id),array('pushType'=>'PROFILE_ACTIVE','message'=>__($message)));
            $user->save();
            return response()->json(['status'=>'success','account_active_ajax'=>true]);
        }else if(isset($request->account_document_verify)){
            $admin = Auth::user();
            $document = SpAdditionalDetail::select('id','status')->where([
                "id"=>$request->document_id,
                "sp_id"=>$user->id
            ])->first();
            if($document){
                $document->comment = null;
                $document->status = 'approved';
                $document->save();
            }
            $notification = new Notification();
            $notification->module ='spadditionaldetail';
            $notification->module_id = $document->id;
            $notification->sender_id = $admin->id;
            $notification->receiver_id = $user->id;
            $notification->notification_type ='DOCUMENT_STATUS';
            $notification->message =__("Your Document status updated!");
            $notification->save();
            $notification->push_notification(array($user->id),array('pushType'=>'DOCUMENT_STATUS','message'=>__("Your Document status updated!")));
            $user->save();
            return response()->json(['status'=>'success','account_active_ajax'=>true]);
        }else if(isset($request->account_document_decline)){
            $admin = Auth::user();
            $document = SpAdditionalDetail::select('id','status')->where([
                "id"=>$request->document_id,
                "sp_id"=>$user->id
            ])->first();
            if($document){
                $document->status = 'declined';
                $document->comment = __($request->comment);
                $document->save();
            }
            $notification = new Notification();
            $notification->module ='spadditionaldetail';
            $notification->module_id = $document->id;
            $notification->sender_id = $admin->id;
            $notification->receiver_id = $user->id;
            $notification->notification_type ='DOCUMENT_STATUS';
            $notification->message =__($request->comment);
            $notification->save();
            $notification->push_notification(array($user->id),array('pushType'=>'DOCUMENT_STATUS','message'=>__($request->comment)));
            $user->save();
            return response()->json(['status'=>'success','account_active_ajax'=>true]);
        }else if(isset($request->account_reject_ajax)){
            if(!$user->account_rejected){
                $user->account_covid_rejected = null;
                $admin = Auth::user();
                $dateznow = new DateTime("now", new DateTimeZone('UTC'));
                $datenow = $dateznow->format('Y-m-d H:i:s');
                $user->account_rejected = $datenow;
                $user->custom_message = __($request->comment);
                $notification = new Notification();
                $notification->module ='users';
                $notification->module_id = $user->id;
                $notification->sender_id = $admin->id;
                $notification->receiver_id = $user->id;
                $notification->notification_type ='PROFILE_REJECTED';
                $notification->message =__($request->comment);
                $notification->save();
                $notification->push_notification(array($user->id),array('pushType'=>'PROFILE_REJECTED','message'=>__($request->comment)));
            }
            $user->save();
            return response()->json(['status'=>'success','account_reject_ajax'=>true]);
        }else if(isset($request->account_covid_reject_ajax)){
            if(!$user->account_covid_rejected){
                $admin = Auth::user();
                $coviddateznow = new DateTime("now", new DateTimeZone('UTC'));
                $coviddateznow->modify('+13 day');
                $coviddateznow = $coviddateznow->format('Y-m-d H:i:s');
                $dateznow = new DateTime("now", new DateTimeZone('UTC'));
                $dateznow->modify('+13 day');
                $datenow = $dateznow->format('Y-m-d H:i:s');
                $user->account_rejected = $datenow;
                $user->account_covid_rejected = $coviddateznow;
                $user->custom_message = __($request->comment);
                $notification = new Notification();
                $notification->module ='users';
                $notification->module_id = $user->id;
                $notification->sender_id = $admin->id;
                $notification->receiver_id = $user->id;
                $notification->notification_type ='COVID_REJECTED';
                $notification->message =__($request->comment);
                $notification->save();
                $notification->push_notification(array($user->id),array('pushType'=>'COVID_REJECTED','message'=>__($request->comment)));
            }
            $user->save();
            return response()->json(['status'=>'success','account_reject_ajax'=>true]);
        }else if(isset($request->account_password_ajax)){
            $user->password = bcrypt($request->password);
            $user->save();
            return response()->json(['status'=>'success','account_password_ajax'=>true]);
        }else if(isset($request->account_points_ajax)){
            $user->wallet->points = $request->points;
            $user->wallet->save();
            return response()->json(['status'=>'success','account_points_ajax'=>true]);
        }else if(!isset($request->account_verify_ajax)){
            $rules = [
                'name' => 'required',
            ];
            if(isset($request->phone)){
                $rules['phone'] = 'unique:users,phone,' . $user->id;
            }
            if(isset($request->email)){
                $rules['email'] = 'email|unique:users,email,' . $user->id;
            }
            $validator = \Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $input = $request->all();
            $profile = Profile::where('user_id',$user->id)->first();  
            if(!$profile){
                $profile = New Profile();
                $profile->dob = '0000-00-00';
                $profile->user_id = $user->id;
                $profile->save();
            }
            if(isset($request->dob)){
                $orgDate = $request->dob;  
                $profile->dob = date("Y-m-d", strtotime($orgDate));
            }
            if(isset($request->working_since)){
                $orgDate = $request->working_since;  
                $profile->working_since = date("Y-m-d", strtotime($orgDate));
            }
            if(isset($request->bio)){
                $profile->about = $request->bio;  
            }
            $profile->save();
            if(isset($request->phone)){
                $user->phone = $request->input('phone');
            }
            if(isset($request->email)){
                $user->email = $request->input('email');
            }
            $user->name = $request->input('name');

            if(isset($request->npi_number)){
                $user->npi_id = $request->input('npi_number');
            }
            $user->save();
            if(isset($input['speciality'])){
                $profile->speciality = $input['speciality'];
            }
            if(isset($input['address'])){
                $profile->address = $input['address'];
            }
            if(isset($input['call_price'])){
                $profile->call_price = $input['call_price'];
            }
            if(isset($input['chat_price'])){
                $profile->chat_price = $input['chat_price'];
            }
            if(isset($input['experience'])){
                $profile->experience = $input['experience'];
            }
            if(isset($input['state'])){
                $profile->state = $input['state'];
            }
            if(isset($input['city'])){
                $profile->city = $input['city'];
            }

            if(isset($input['category'])){
                if($user->hasrole('service_provider')){
                    $category_service = CategoryServiceProvider::where(['sp_id'=>$user->id])->first();
                    if(!$category_service){
                        $category_service =  new CategoryServiceProvider();
                        $category_service->sp_id = $user->id;
                    }
                    $category_service->category_id = $input['category'];
                    $category_service->save();
                }
            }
            if(isset($input['zip_code'])){
                $zip_code = CustomField::where(['field_name' => 'Zip Code', 'user_type' => 3])->first();
                if ($zip_code) {
                    CustomUserField::where('user_id', $user->id)->where('custom_field_id', $zip_code->id)->delete();
                    $custom_user_field = new CustomUserField();
                    $custom_user_field->user_id = $user->id;
                    $custom_user_field->custom_field_id = $zip_code->id;
                    $custom_user_field->field_value = $input['zip_code'];
                    $custom_user_field->save();
                }
            }
            if (isset($input['insurances'])) {
                UserInsurance::where('user_id', $user->id)->delete();
                foreach ($input['insurances'] as $key => $insurance_id) {
                    if ($insurance_id) {
                        $userinsurance = new UserInsurance();
                        $userinsurance->insurance_id = $insurance_id;
                        $userinsurance->user_id = $user->id;
                        $userinsurance->save();
                    }
                }
            }
            if(isset($input['education'])){
                $edu = CustomField::where(['field_name' => 'Education', 'user_type' => 3])->first();
                if ($edu) {
                    CustomUserField::where('user_id', $user->id)->where('custom_field_id', $edu->id)->delete();
                    $CustomUserField = new CustomUserField();
                    $CustomUserField->user_id = $user->id;
                    $CustomUserField->custom_field_id = $edu->id;
                    $CustomUserField->field_value = $input['education'];
                    $CustomUserField->save();
                }
            }
            if(isset($input['master_preferences'])){
                foreach ($input['master_preferences'] as $preference_id => $master_preference) {
                    if($preference_id){
                        UserMasterPreference::where([
                            'user_id'=>$user->id,
                            'preference_id'=>$preference_id,
                        ])->delete();
                        foreach ($master_preference as $option) {
                            if($option){
                                UserMasterPreference::firstOrCreate([
                                    'user_id'=>$user->id,
                                    'preference_id'=>$preference_id,
                                    'preference_option_id'=>$option,
                                ]);
                            }
                        }
                    }
                }
            }
            $profile->save();
        }else{
            if(!$user->account_verified){
                $admin = Auth::user();
                $dateznow = new DateTime("now", new DateTimeZone('UTC'));
                $datenow = $dateznow->format('Y-m-d H:i:s');
                $user->account_verified = $datenow;
                $notification = new Notification();
                $notification->module ='users';
                $notification->module_id = $user->id;
                $notification->sender_id = $admin->id;
                $notification->receiver_id = $user->id;
                $notification->notification_type ='PROFILE_APPROVED';
                $notification->message =__('Your Account has been approved');;
                $notification->save();
                $notification->push_notification(array($user->id),array('pushType'=>'PROFILE_APPROVED','message'=>__('Your Account has been approved')));
            }
            $user->save();
            return response()->json(['status'=>'success','account_verified'=>true]);
        }
        return back()->with("status", "profile updated");
    }
    public function PostMakeOnline(Request $request){
        $user_id = $request->user_id;
        $manual_available = $request->manual_available;
        $user = User::where('id',$user_id)->first();
        if($user){
            if($manual_available == 'true'){
                $user->manual_available = 0;
            }else{
                $user->manual_available = 1;
            }
            $user->save();
        }
        return response()->json(['status'=>'success', 'user_data' => $user]);
    }

    public function PostMakePreOnline(Request $request){
        $user_id = $request->user_id;
        // $manual_available = $request->premium_enable;
        $user = User::where('id',$user_id)->first();
        if($user){
            if($user->premium_enable){
                $user->premium_enable = null;
            }else{
                $dateznow = new DateTime("now", new DateTimeZone('UTC'));
                $datenow = $dateznow->format('Y-m-d H:i:s');
                $user->premium_enable = $datenow;
            }
            $user->save();
        }
        return response()->json(['status'=>'success', 'user_data' => $user]);
    }

    public function PostPremiumMessage(Request $request){
        $admin = Auth::user();
        $users = User::where('premium_enable','!=',null)->whereHas('roles', function ($query) {
           $query->where('name','service_provider');
        })->where('permission',null)->pluck('id')->toArray();
        if(count($users)>0){
            foreach($users as $id) {
                $notification = new Notification();
                $notification->module ='premium_messages';
                $notification->module_id = $id;
                $notification->sender_id = $admin->id;
                $notification->receiver_id = $id;
                $notification->notification_type ='PREMIUM_MESSAGE';
                $notification->message =__($request->comment);
                $notification->save();
            }
            $notification->push_notification($users,array('pushType'=>'PREMIUM_MESSAGE','message'=>__($request->comment)));
        }
        return response()->json(['status'=>'success']);
    }
    public function PostUploadxls(Request $request){
        try {
            $image = $request->file('fileName');
            $extension = $image->getClientOriginalExtension();
            $name = $image->getFilename();
            \Illuminate\Support\Facades\Storage::disk('media')->put(time() . $image->getFilename() . '.' . $extension, \Illuminate\Support\Facades\File::get($image));
            Excel::import(new UsersImport, base_path('public/media/'.time() . $image->getFilename() . '.' . $extension));
            return response()->json(['status'=>'success']);
        } catch (\Exception $e) {
            return response()->json(['status'=>'error', 'message' => $e->getMessage()]);
        }
    }
    public function deleteServiceProvider(Request $request){
        $user_ids = $request->user_id;
        if(is_array($request->user_id)){
            foreach ($user_ids as $key => $user_id) {
                $user = User::where('id', $user_id)->first();
                if($user){
                    SocialAccount::where("user_id", $user->id)->delete();
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
                        if(Config('client_connected') && (Config::get("client_data")->domain_name=="mp2r")){
                                $response = Curl::to("https://api-B13514F8-4AB5-4ABA-87D6-C3904DA10C96.sendbird.com/v3/users/$user->id")
                            ->withHeader('Api-Token: ec1d0ce14b3a467eeb5f6665ab915c8f3f09ca59')
                            ->asJson()
                            ->delete();
                        }
                        $role = \App\Model\Role::where('name','service_provider')->first();
                        if($user->hasRole('service_provider')){
                            $user->roles()->detach($role);
                            $archived_user = Role::firstOrCreate(['name'=>'archived_user']);
                            $user->roles()->attach($archived_user);
                        }
                    }
                }
            }
        }else{
            $user = User::where('id', $user_ids)->first();
            if($user){
                SocialAccount::where("user_id", $user->id)->delete();
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
                    if(Config('client_connected') && (Config::get("client_data")->domain_name=="mp2r")){
                        $response = Curl::to("https://api-B13514F8-4AB5-4ABA-87D6-C3904DA10C96.sendbird.com/v3/users/$user->id")
                        ->withHeader('Api-Token: ec1d0ce14b3a467eeb5f6665ab915c8f3f09ca59')
                        ->asJson()
                        ->delete();
                    }
                    $role = \App\Model\Role::where('name','service_provider')->first();
                    if($user->hasRole('service_provider')){
                        $user->roles()->detach($role);
                        $archived_user = Role::firstOrCreate(['name'=>'archived_user']);
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
