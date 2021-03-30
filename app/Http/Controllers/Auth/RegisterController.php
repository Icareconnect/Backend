<?php

namespace App\Http\Controllers\Auth;

use Auth;
use Config;
use App\User;
use App\Model\Role;
use App\Model\Plan;
use App\Model\Wallet;
use App\Model\Profile;
use App\Model\UserRole;
use App\Model\CustomInfo;
use DateTime, DateTimeZone;
use App\Model\UserInsurance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\Model\Group, App\Model\GroupVendor;
use App\Model\Transaction, App\Model\Payment;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Model\Category, App\Model\SubscribePlan;
use App\Model\CustomField, App\Model\CustomUserField;
use App\Model\State, App\Model\CategoryServiceProvider;

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use App\Helpers\Helper;
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('mypathRegister', 'showRegistrationForm', 'showMypathUserRegister', 'postMypathUserRegister','postUpgradePlan');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm(Request $request){
        if (Config::get("default")) {
            return view('auth.register');
        } else if (Config::get("client_data")->domain_name == "mp2r") {
            if (Auth::user() && Auth::user()->hasrole('service_provider')) {
                $user = Auth::user();
                if ($user && $user->account_step < 6) {
                    if($user->account_step==5 ){
                        $exist_group  = SubscribePlan::where(['user_id' => $user->id])
                            ->whereHas('plan', function ($query) {
                                $query->whereIn('plan_id', ["com.mp2r.additional.group"]);
                            })->first();
                        if(!$exist_group){
                            $user->account_step = 6;
                            $user->save();
                            return redirect('service_provider/Appointment');
                        }
                    }
                    $categories = [];
                    $insurances = \App\Model\Insurance::where('enable', '1')->orderBy('name', "ASC")->pluck('name', 'id');
                    $states = State::where('country_id', '=', 231)->whereNotIn('name', ["Byram", "Cokato", "District of Columbia", "Lowa", "Medfield", "New Jersy", "Ontario", "Ramey", "Sublimity", "Trimble"])->pluck('name', 'id');
                    // print_r($user->account_step);die;
                    $account_step = $user->account_step + 1;
                    /* Check If Subcategory Exist */
                    if (isset($request->step) && $request->step == 3 && isset($request->category)) {
                        $sub_category = Category::where(['enable' => '1', 'parent_id' => $request->category])
                            ->with('subcategory')
                            ->orderBy('id', "asc")
                            ->get();
                        foreach ($sub_category as $key => $category) {
                            $category->is_filters = false;
                            if ($category->filters->count() > 0) {
                                $category->is_filters = true;
                            }
                            $category->is_subcategory = false;
                            $subcategory = Category::where('parent_id', $category->id)->where('enable', '=', '1')->count();
                            if ($subcategory > 0) {
                                $category->is_subcategory = true;
                            }
                        }
                        return view('vendor.mp2r.signup' . $account_step, compact('sub_category'));
                    } else {
                        $categories = Category::where(['enable' => '1', 'parent_id' => NULL])
                            ->where('name', '!=', 'Find Local Resources')
                            ->with('subcategory')
                            ->orderBy('id', "asc")
                            ->get();
                        foreach ($categories as $key => $category) {
                            $category->is_filters = false;
                            if ($category->filters->count() > 0) {
                                $category->is_filters = true;
                            }
                            $category->is_subcategory = false;
                            $subcategory = Category::where('parent_id', $category->id)->where('enable', '=', '1')->count();
                            if ($subcategory > 0) {
                                $category->is_subcategory = true;
                            }
                        }
                    }
                    $groups = [];
                    $category = $user->getCategoryData($user->id);
                    if($category){
                        $groups = Group::where('category_id', $category->id)->orderBy('name', "ASC")->get();
                    }
                    $question1 = Helper::getSecurityQuestion('question1');
                    $question2 = Helper::getSecurityQuestion('question2');
                    $question3 = Helper::getSecurityQuestion('question3');
                    return view('vendor.mp2r.signup' . $account_step, compact('states', 'insurances', 'categories', 'groups','question1','question2','question3'));
                } else {
                    return redirect('service_provider/Appointment');
                }
                return view('vendor.mp2r.signup');
            } else if (Auth::user() && Auth::user()->hasrole('customer')) {
                $user = Auth::user();
                if ($user && $user->account_step < 2) {
                    $question1 = Helper::getSecurityQuestion('question1');
                    $question2 = Helper::getSecurityQuestion('question2');
                    $question3 = Helper::getSecurityQuestion('question3');
                    $insurances = \App\Model\Insurance::where('enable', '1')->orderBy('name', "ASC")->pluck('name', 'id');
                    $states = State::where('country_id', '=', 231)->whereNotIn('name', ["Byram", "Cokato", "District of Columbia", "Lowa", "Medfield", "New Jersy", "Ontario", "Ramey", "Sublimity", "Trimble"])->pluck('name', 'id');
                    $account_step = $user->account_step + 1;
                    return view('vendor.mp2r.user.signup' . $account_step, compact('states', 'insurances', 'categories', 'groups','question1','question2','question3'));
                }
            } else {
                return view('vendor.mp2r.signup');
            }
        } elseif(Config::get("client_data")->domain_name == "heal") {
            return view('vendor.heal.expert.signup');
        }else {
            return view('auth.register');
        }
    }

    public function showRegistrationForm2(Request $request){
        return view('vendor.heal.expert.signup2');
    }

    public function showRegistrationForm3(Request $request){
        return view('vendor.heal.expert.signup3');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showMypathUserRegister(Request $request)
    {
        if (Config::get("default")) {
            return view('auth.register');
        } else if (Config::get("client_data")->domain_name == "mp2r" || Config::get("client_data")->domain_name == "food") {
            // die('hehe');
            if (Auth::user() && Auth::user()->hasrole('customer')) {
                $user = Auth::user();
                if ($user && $user->account_step < 2) {
                    $question1 = Helper::getSecurityQuestion('question1');
                    $question2 = Helper::getSecurityQuestion('question2');
                    $question3 = Helper::getSecurityQuestion('question3');
                    $categories = [];
                    $insurances = \App\Model\Insurance::where('enable', '1')->orderBy('name', "ASC")->pluck('name', 'id');
                    $states = State::where('country_id', '=', 231)->whereNotIn('name', ["Byram", "Cokato", "District of Columbia", "Lowa", "Medfield", "New Jersy", "Ontario", "Ramey", "Sublimity", "Trimble"])->pluck('name', 'id');
                    $account_step = $user->account_step + 1;
                    return view('vendor.mp2r.user.signup' . $account_step, compact('states', 'insurances','question1','question2','question3'));
                } else {
                    return redirect('/');
                }
            }
            return view('vendor.mp2r.user.signup');
        } else {
            return view('auth.register');
        }
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = new User();
        $user->name =  $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        if ($user->save()) {
            $wallet = new Wallet();
            $wallet->balance = 0;
            $wallet->user_id = $user->id;
            $wallet->save();
        }
    }

    public function mypathRegister(Request $request)
    {
        $input = $request->all();
        if ($request->step == 1) {
            // print_r($input);die;
            $column_name2 = 'email';
            
            if (filter_var(request('email'), FILTER_VALIDATE_EMAIL)) {
                $column_name = 'email';
            } else {
                $column_name = 'user_name';
            }
            $rules = [
                'first_name' => 'required|regex:/^[a-zA-Z]+$/u',
                'last_name' => 'required|regex:/^[a-zA-Z]+$/u',
                'phone' => 'required|numeric|unique:users,phone',
                
            ];

            if ($column_name == 'email') {
                $rules['email'] = 'required|email|unique:users,email';
                $customMessages['email.unique'] = "The email has already been taken.";
                $customMessages['email.required'] = 'The email name required';
            } else {
                $rules['email'] = 'required|unique:users,user_name';
                $customMessages['email.unique'] = "The user name has already been taken.";
                $customMessages['email.required'] = 'The email or username required.';
            }

            //if($request->password == 'password'){

                $rules['password'] = 'required|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|confirmed';


                $customMessages['password.regex'] = "Password  should contain at-least 1 Uppercase, 1 Lowercase, 1 Numeric.";

                $customMessages['first_name.regex'] = "The First Name may only contain word.";

                $customMessages['last_name.regex'] = "The Last Name may only contain  word.";
                

            //}
            $validator = Validator::make($request->all(), $rules, $customMessages);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'errors' =>
                $validator->errors()), 400);
            }
            if ($column_name == 'user_name') {
                $input["user_name"] =  $input["email"];
                unset($input["email"]);
            }
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);
            if ($user) {
                if ($request->hasfile('profile_image')) {
                    if ($image = $request->file('profile_image')) {
                        $extension = $image->getClientOriginalExtension();
                        $filename = str_replace(' ', '', md5(time()) . '_' . $image->getClientOriginalName());
                        $thumb = \Image::make($image)->resize(
                            100,
                            100,
                            function ($constraint) {
                                $constraint->aspectRatio();
                            }
                        )->encode($extension);
                        $normal = \Image::make($image)->resize(
                            400,
                            400,
                            function ($constraint) {
                                $constraint->aspectRatio();
                            }
                        )->encode($extension);
                        $big = \Image::make($image)->encode($extension);
                        $_800x800 = \Image::make($image)->resize(
                            800,
                            800,
                            function ($constraint) {
                                $constraint->aspectRatio();
                            }
                        )->encode($extension);
                        $_400x400 = \Image::make($image)->resize(
                            400,
                            400,
                            function ($constraint) {
                                $constraint->aspectRatio();
                            }
                        )->encode($extension);
                        \Storage::disk('spaces')->put('thumbs/' . $filename, (string)$thumb, 'public');
                        \Storage::disk('spaces')->put('uploads/' . $filename, (string)$normal, 'public');
                        \Storage::disk('spaces')->put('original/' . $filename, (string)$big, 'public');
                        \Storage::disk('spaces')->put('800x800/' . $filename, (string)$_800x800, 'public');
                        \Storage::disk('spaces')->put('400x400/' . $filename, (string)$_400x400, 'public');
                        \Storage::disk('spaces')->put('original/' . $filename, (string)$big, 'public');
                        $user->profile_image = $filename;
                    }
                }
                $datenow = new DateTime("now", new DateTimeZone('UTC'));
                $datenowone = $datenow->format('Y-m-d H:i:s');
                $user->account_verified = $datenowone;
                $user->name = $input['first_name'] . ' ' . $input['last_name'];
                $user->account_step = 1;

                // $user = $user->createStripeCustomer($user);
                if ($user) {
                    $user->provider_type = 'email';
                    $user->device_type = 'web';
                    $user->save();
                    $wallet = new Wallet();
                    $wallet->balance = 0;
                    $wallet->user_id = $user->id;
                    $wallet->save();
                    $role = Role::where('name', 'service_provider')->first();
                    if ($role) {
                        $user->roles()->attach($role);
                    }
                    $profile = new Profile();
                    $profile->dob = isset($input['dob']) ? $input['dob'] : '0000-00-00';
                    $profile->user_id = $user->id;
                    $profile->save();
                    auth()->login($user, true);
                    return response(['status' => 'success', 'statuscode' => 200, 'message' => __('You signed-up successfully !')], 200);

              }else{
                return response(['status' => 'error', 'statuscode' => 400, 'message' => __('Your inputs are not valid. !')], 400);

              }
            }
        } else if ($request->step == 2) {
            return $this->stepSecond($request);
        } else if ($request->step == 3) {
            return $this->stepThird($request);
        } else if ($request->step == 5) {
            return $this->stepFive($request);
        } else if ($request->step == 6) {
            return $this->stepSix($request);
        }
    }

    public function postMypathUserRegister(Request $request)
    {
        try {
            $input = $request->all();
            if ($request->step == 1) {
                // print_r($input);die;
                $column_name = 'email';
                if (filter_var(request('email'), FILTER_VALIDATE_EMAIL)) {
                    $column_name = 'email';
                } else {
                    $column_name = 'user_name';
                }
                $rules = [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'phone' => 'required|unique:users,phone',
                ];
                if ($column_name == 'email') {
                    $rules['email'] = 'required|email|unique:users,email';
                    $customMessages['email.unique'] = "The email has already been taken.";
                    $customMessages['email.required'] = 'The email name required';
                } else {
                    $rules['email'] = 'required|unique:users,user_name';
                    $customMessages['email.unique'] = "The user name has already been taken.";
                    $customMessages['email.required'] = 'The email or username required.';
                }

                $rules['password'] = 'required|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|confirmed';


                $customMessages['password.regex'] = "Password  should contain at-least 1 Uppercase, 1 Lowercase, 1 Numeric.";

                $validator = Validator::make($request->all(), $rules, $customMessages);
                if ($validator->fails()) {
                    return response(array('status' => "error", 'statuscode' => 400, 'errors' =>
                    $validator->errors()), 400);
                }
                if ($column_name == 'user_name') {
                    $input["user_name"] =  $input["email"];
                    unset($input["email"]);
                }
                $input['password'] = bcrypt($input['password']);
                $user = User::create($input);
                if ($user) {
                    if ($request->hasfile('profile_image')) {
                        if ($image = $request->file('profile_image')) {
                            $extension = $image->getClientOriginalExtension();
                            $filename = str_replace(' ', '', md5(time()) . '_' . $image->getClientOriginalName());
                            $thumb = \Image::make($image)->resize(
                                100,
                                100,
                                function ($constraint) {
                                    $constraint->aspectRatio();
                                }
                            )->encode($extension);
                            $normal = \Image::make($image)->resize(
                                400,
                                400,
                                function ($constraint) {
                                    $constraint->aspectRatio();
                                }
                            )->encode($extension);
                            $big = \Image::make($image)->encode($extension);
                            $_800x800 = \Image::make($image)->resize(
                                800,
                                800,
                                function ($constraint) {
                                    $constraint->aspectRatio();
                                }
                            )->encode($extension);
                            $_400x400 = \Image::make($image)->resize(
                                400,
                                400,
                                function ($constraint) {
                                    $constraint->aspectRatio();
                                }
                            )->encode($extension);
                            \Storage::disk('spaces')->put('thumbs/' . $filename, (string)$thumb, 'public');
                            \Storage::disk('spaces')->put('uploads/' . $filename, (string)$normal, 'public');
                            \Storage::disk('spaces')->put('original/' . $filename, (string)$big, 'public');
                            \Storage::disk('spaces')->put('800x800/' . $filename, (string)$_800x800, 'public');
                            \Storage::disk('spaces')->put('400x400/' . $filename, (string)$_400x400, 'public');
                            \Storage::disk('spaces')->put('original/' . $filename, (string)$big, 'public');
                            $user->profile_image = $filename;
                        }
                    }
                    $datenow = new DateTime("now", new DateTimeZone('UTC'));
                    $datenowone = $datenow->format('Y-m-d H:i:s');
                    $user->account_verified = $datenowone;
                    $user->name = $input['first_name'] . ' ' . $input['last_name'];
                    $user->account_step = 1;
                    // $user = $user->createStripeCustomer($user);
                    $user->provider_type = 'email';
                    $user->device_type = 'web';
                    $user->save();
                    $wallet = new Wallet();
                    $wallet->balance = 0;
                    $wallet->user_id = $user->id;
                    $wallet->save();
                    $role = Role::where('name', 'customer')->first();
                    if ($role) {
                        $user->roles()->attach($role);
                    }
                    $profile = new Profile();
                    $profile->dob = isset($input['dob']) ? $input['dob'] : '0000-00-00';
                    $profile->user_id = $user->id;
                    $profile->save();
                    auth()->login($user, true);
                    return response(['status' => 'success', 'statuscode' => 200, 'message' => __('You signed-up successfully !')], 200);
                }
            } else if ($request->step == 2) {
                return $this->stepSecondUser($request);
            }
        } catch (Exception $e) {
            return response(array('status' => "error", 'statuscode' => 400, 'message' => $e->getMessage()), 400);
        }
    }

    public function stepSecondUser($request)
    {
        
        $user = Auth::user();
        $input = $request->all();
        $rules = [
            'address' => 'required',
            'state' => 'required',
            'city' => 'required',
            'zip_code' => "required",
            'question1' => "required",
            'question2' => "required",
            'question3' => "required",
            'answer1' => "required",
            'answer2' => "required",
            'answer3' => "required",
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response(array('status' => "error", 'statuscode' => 400, 'errors' =>
            $validator->errors()), 400);
        }
        $user->profile->address = $input['address'];
        $user->profile->state = $input['state'];
        $user->profile->city = $input['city'];
        $user->profile->country = 231;
        $user->profile->save();
        $zip_code = CustomField::where(['field_name' => 'Zip Code', 'user_type' => 2])->first();
        if ($zip_code) {
            CustomUserField::where('user_id', $user->id)->where('custom_field_id', $zip_code->id)->delete();
            $CustomUserField = new CustomUserField();
            $CustomUserField->field_value = $input['zip_code'];
            $CustomUserField->user_id = $user->id;
            $CustomUserField->custom_field_id = $zip_code->id;
            $CustomUserField->save();
        }
        if (isset($input['insurance']) && $input['insurance']) {

            UserInsurance::where('user_id', $user->id)->delete();
            $userinsurance = new UserInsurance();
            $userinsurance->insurance_id = $input['insurance'];
            $userinsurance->user_id = $user->id;
            $userinsurance->save();
        }
        \App\Model\UserSecurityAnswer::where('user_id',$user->id)->delete();
        $UserSecurityAnswer1 = new \App\Model\UserSecurityAnswer();
        $UserSecurityAnswer1->security_question_id = $input['question1'];
        $UserSecurityAnswer1->user_id = $user->id;
        $UserSecurityAnswer1->answer = $input['answer1'];
        $UserSecurityAnswer1->save();

        $UserSecurityAnswer2 = new \App\Model\UserSecurityAnswer();
        $UserSecurityAnswer2->security_question_id = $input['question2'];
        $UserSecurityAnswer2->user_id = $user->id;
        $UserSecurityAnswer2->answer = $input['answer2'];
        $UserSecurityAnswer2->save();

        $UserSecurityAnswer3 = new \App\Model\UserSecurityAnswer();
        $UserSecurityAnswer3->security_question_id = $input['question3'];
        $UserSecurityAnswer3->user_id = $user->id;
        $UserSecurityAnswer3->answer = $input['answer3'];
        $UserSecurityAnswer3->save();
        
        $user->account_step = 2;
        $user->save();
        return response(['status' => 'success', 'statuscode' => 200, 'message' => __('You signed-up successfully !')], 200);
    }
    public function stepSecond($request)
    {
        $user = Auth::user();
        $input = $request->all();
        $rules = [
            'address' => 'required',
            'state' => 'required',
            'city' => 'required',
            'zip_code' => "required",
            'education' => "required",
            'insurances' => "required",
            'question1' => "required",
            'question2' => "required",
            'question3' => "required",
            'answer1' => "required",
            'answer2' => "required",
            'answer3' => "required",
            
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response(array('status' => "error", 'statuscode' => 400, 'errors' =>
            $validator->errors()), 400);
        }

        $user->profile->address = $input['address'];
        $user->profile->state = $input['state'];
        $user->profile->city = $input['city'];
        $user->profile->country = 231;
        $user->profile->save();
        $user->npi_id=$input['npi_id'];
        $user->save();
        $zip_code = CustomField::where(['field_name' => 'Zip Code', 'user_type' => 3])->first();
        $edu = CustomField::where(['field_name' => 'Education', 'user_type' => 3])->first();
        if ($zip_code) {
            CustomUserField::where('user_id', $user->id)->where('custom_field_id', $zip_code->id)->delete();
            $CustomUserField = new CustomUserField();
            $CustomUserField->field_value = $input['zip_code'];
            $CustomUserField->user_id = $user->id;
            $CustomUserField->custom_field_id = $zip_code->id;
            $CustomUserField->save();
        }
        if ($edu) {
            CustomUserField::where('user_id', $user->id)->where('custom_field_id', $edu->id)->delete();
            $CustomUserField = new CustomUserField();
            $CustomUserField->field_value = $input['education'];
            $CustomUserField->user_id = $user->id;
            $CustomUserField->custom_field_id = $edu->id;
            $CustomUserField->save();
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
        \App\Model\UserSecurityAnswer::where('user_id',$user->id)->delete();
        $UserSecurityAnswer1 = new \App\Model\UserSecurityAnswer();
        $UserSecurityAnswer1->security_question_id = $input['question1'];
        $UserSecurityAnswer1->user_id = $user->id;
        $UserSecurityAnswer1->answer = $input['answer1'];
        $UserSecurityAnswer1->save();

        $UserSecurityAnswer2 = new \App\Model\UserSecurityAnswer();
        $UserSecurityAnswer2->security_question_id = $input['question2'];
        $UserSecurityAnswer2->user_id = $user->id;
        $UserSecurityAnswer2->answer = $input['answer2'];
        $UserSecurityAnswer2->save();

        $UserSecurityAnswer3 = new \App\Model\UserSecurityAnswer();
        $UserSecurityAnswer3->security_question_id = $input['question3'];
        $UserSecurityAnswer3->user_id = $user->id;
        $UserSecurityAnswer3->answer = $input['answer3'];
        $UserSecurityAnswer3->save();
        $user->account_step = 2;
        $user->save();
        return response(['status' => 'success', 'statuscode' => 200, 'message' => __('You signed-up successfully !')], 200);
    }

    public function stepThird($request)
    {
        $user = Auth::user();
        $input = $request->all();
        $rules = [
            'category' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response(array('status' => "error", 'statuscode' => 400, 'errors' =>
            $validator->errors()), 400);
        }
        $category = Category::where('id', $input['category'])->first();
        if ($category) {
            $subcategory = Category::where('parent_id', $category->id)->where('enable', '=', '1')->count();
            if ($subcategory > 0) {
                return response(['status' => 'success', 'category' => $category->id, 'statuscode' => 200, 'message' => __('You signed-up successfully !')], 200);
            }
        }
        $category_service = CategoryServiceProvider::where(['sp_id' => $user->id])->first();
        if (!$category_service) {
            $category_service =  new CategoryServiceProvider();
            $category_service->sp_id = $user->id;
        }
        $category_service->category_id = $input['category'];
        $category_service->save();
        $user->account_step = 3;
        $user->save();
        return response(['status' => 'success', 'statuscode' => 200, 'message' => __('You signed-up successfully !')], 200);
    }

    public function stepSix($request)
    {
        $user = Auth::user();
        $input = $request->all();
        $rules = [
            'group_type' => 'required',
        ];
        $group = null;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response(array('status' => "error", 'statuscode' => 400, 'errors' =>
            $validator->errors()), 400);
        }
        if ($request->group_type == 'id') {
            if(isset($request->group_id))
                $group =  Group::where(['id' => $request->group_id])->first();
        } else {
            if(isset($request->group_name)){
                $category = $user->getCategoryData($user->id);
                $group =  Group::firstOrCreate(['name' => $request->group_name, 'category_id' => $category->id]);
            }
        }
        if($group){
            $groupvendor = new GroupVendor();
            $groupvendor->user_id = $user->id;
            $groupvendor->group_id = $group->id;
            $groupvendor->save();
        }
        $user->account_step = 6;
        $user->save();
        return response(['status' => 'success', 'statuscode' => 200, 'message' => __('You signed-up successfully !')], 200);
    }

    public function stepFive($request)
    {

        // $rules = [
        //     'address' => 'required',
            
        // ];
        // $validator = Validator::make($request->all(), $rules);
        // if ($validator->fails()) {
        //     return response(array('status' => "error", 'statuscode' => 400, 'errors' =>
        //     $validator->errors()), 400);
        // }


        try {
            $user = Auth::user();
            $rules = ['plan_id' => 'required'];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                $validator->getMessageBag()->first()), 400);
            }
            $datenow = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
            $input = $request->all();
            $res = [];
            if ($input['total_price']) {
                $res = $this->chargeCreditCard($request);
                if ($res['status'] == 'error') {
                    $res['statuscode'] = 400;
                    return response($res, 400);
                }
            }
            $bs_plans = ["com.mp2r.basic", "com.mp2r.premium", "com.mp2r.executive"];
            $plan_ids = explode(',', $input['plan_id']);
            $exist_plan  = SubscribePlan::where(['user_id' => $user->id])
                ->where('expired_on', '>', $datenow)
                ->whereHas('plan', function ($query) {
                    $query->whereIn('plan_id', ["com.mp2r.basic", "com.mp2r.premium", "com.mp2r.executive"]);
                })
                ->first();
            $basics_plan = null;
            if ($exist_plan && $exist_plan->plan) {
                $basics_plan = $exist_plan->plan->plan_id;
            }
            foreach ($plan_ids as $key => $plan_id) {
                $plan = Plan::where('plan_id', $plan_id)->first();
                if ($plan) {
                    $subscribeplan  = SubscribePlan::where(
                        [
                            'user_id' => $user->id,
                            'plan_id' => $plan->id
                        ]
                    )->where('expired_on', '>', $datenow)->first();
                    if (!$subscribeplan) {
                        $expired_on = \Carbon\Carbon::now()->addMonth(1)->format('Y-m-d H:i:s');
                        if (in_array($plan_id, $bs_plans) && $basics_plan !== $plan_id) {
                            if ($exist_plan) {
                                $exist_plan->delete();
                            }
                        }
                    } else {
                        $expired_on = \Carbon\Carbon::parse($subscribeplan->expired_on)->addMonth(1)->format('Y-m-d H:i:s');
                    }
                    // dd('hh');
                    $new_subscribe = SubscribePlan::firstOrCreate([
                        'plan_id' => $plan->id,
                        'user_id' => $user->id
                    ]);
                    $new_subscribe->expired_on = $expired_on;
                    $new_subscribe->save();
                    $transaction = Transaction::create(array(
                        'amount' => $plan->price,
                        'transaction_type' => 'subscribe_plan',
                        'status' => 'success',
                        'wallet_id' => $user->wallet->id,
                        'closing_balance' => $user->wallet->balance,
                        'transaction_id' => (isset($res['transaction_id'])) ? $res['transaction_id'] : null,
                        'payment_gateway' => (isset($res['transaction_id'])) ? 'authorize' : null,
                    ));
                    if ($transaction) {
                        $payment = Payment::create(array(
                            'from' => $user->id,
                            'to' => $user->id,
                            'transaction_id' => $transaction->id
                        ));
                        $transaction->module_table  = 'subscribe_plans';
                        $transaction->module_id  = $new_subscribe->id;
                        $transaction->save();
                    }
                }
            }
            $user->account_step = 5;
            $user->save();
            return response([
                'status' => "success",
                'statuscode' => 200,
                'message' => __('Subscribed Plan')
            ], 200);
        } catch (Exception $ex) {
            return response(['status' => "error", 'statuscode' => 400, 'message' => $ex->getMessage()], 400);
        }
    }


    public function postUpgradePlan(Request $request)
    {
        try {
            $user = Auth::user();
            $rules = ['plan_id' => 'required'];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                $validator->getMessageBag()->first()), 400);
            }
            $datenow = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
            $input = $request->all();
            $res = [];
            if ($input['total_price']) {
                $res = $this->chargeCreditCard($request);
                if ($res['status'] == 'error') {
                    $res['statuscode'] = 400;
                    return response($res, 400);
                }
            }
            $bs_plans = ["com.mp2r.basic", "com.mp2r.premium", "com.mp2r.executive"];
            $plan_ids = explode(',', $input['plan_id']);
            $exist_plan  = SubscribePlan::where(['user_id' => $user->id])
                ->where('expired_on', '>', $datenow)
                ->whereHas('plan', function ($query) {
                    $query->whereIn('plan_id', ["com.mp2r.basic", "com.mp2r.premium", "com.mp2r.executive"]);
                })
                ->first();
            $basics_plan = null;
            if ($exist_plan && $exist_plan->plan) {
                $basics_plan = $exist_plan->plan->plan_id;
            }
            foreach ($plan_ids as $key => $plan_id) {
                $plan = Plan::where('plan_id', $plan_id)->first();
                if ($plan) {
                    $subscribeplan  = SubscribePlan::where(
                        [
                            'user_id' => $user->id,
                            'plan_id' => $plan->id
                        ]
                    )->where('expired_on', '>', $datenow)->first();
                    if (!$subscribeplan) {
                        $expired_on = \Carbon\Carbon::now()->addMonth(1)->format('Y-m-d H:i:s');
                        if (in_array($plan_id, $bs_plans) && $basics_plan !== $plan_id) {
                            if ($exist_plan) {
                                $exist_plan->delete();
                            }
                        }
                    } else {
                        $expired_on = \Carbon\Carbon::parse($subscribeplan->expired_on)->addMonth(1)->format('Y-m-d H:i:s');
                    }
                    // dd('hh');
                    $new_subscribe = SubscribePlan::firstOrCreate([
                        'plan_id' => $plan->id,
                        'user_id' => $user->id
                    ]);
                    $new_subscribe->expired_on = $expired_on;
                    $new_subscribe->save();
                    $transaction = Transaction::create(array(
                        'amount' => $plan->price,
                        'transaction_type' => 'subscribe_plan',
                        'status' => 'success',
                        'wallet_id' => $user->wallet->id,
                        'closing_balance' => $user->wallet->balance,
                        'transaction_id' => (isset($res['transaction_id'])) ? $res['transaction_id'] : null,
                        'payment_gateway' => (isset($res['transaction_id'])) ? 'authorize' : null,
                    ));
                    if ($transaction) {
                        $payment = Payment::create(array(
                            'from' => $user->id,
                            'to' => $user->id,
                            'transaction_id' => $transaction->id
                        ));
                        $transaction->module_table  = 'subscribe_plans';
                        $transaction->module_id  = $new_subscribe->id;
                        $transaction->save();
                    }
                }
            }
            return response([
                'status' => "success",
                'statuscode' => 200,
                'message' => __('Subscribed Plan')
            ], 200);
        } catch (Exception $ex) {
            return response(['status' => "error", 'statuscode' => 400, 'message' => $ex->getMessage()], 400);
        }
    }

    private function chargeCreditCard($request)
    {
        try {
            $input = $request->input();
            /* Create a merchantAuthenticationType object with authentication details
            retrieved from the constants file */
            $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
            $merchantAuthentication->setName(env('MERCHANT_LOGIN_ID'));
            $merchantAuthentication->setTransactionKey(env('MERCHANT_TRANSACTION_KEY'));
            // Set the transaction's refId
            $refId = 'ref' . time();
            $cardNumber = preg_replace('/\s+/', '', $input['cc_number']);
            // Create the payment data for a credit card
            $creditCard = new AnetAPI\CreditCardType();
            $creditCard->setCardNumber($cardNumber);
            $creditCard->setExpirationDate($input['expiration_year'] . "-" . $input['expiration_month']);
            $creditCard->setCardCode($input['cvv']);
            // Add the payment data to a paymentType object
            $paymentOne = new AnetAPI\PaymentType();
            $paymentOne->setCreditCard($creditCard);
            // Create a TransactionRequestType object and add the previous objects to it
            $transactionRequestType = new AnetAPI\TransactionRequestType();
            $transactionRequestType->setTransactionType("authCaptureTransaction");
            $transactionRequestType->setAmount($input['total_price']);
            $transactionRequestType->setPayment($paymentOne);
            // Assemble the complete transaction request
            $requests = new AnetAPI\CreateTransactionRequest();
            $requests->setMerchantAuthentication($merchantAuthentication);
            $requests->setRefId($refId);
            $requests->setTransactionRequest($transactionRequestType);
            // Create the controller and get the response
            $controller = new AnetController\CreateTransactionController($requests);
            $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
            $status = "error";
            $message_text = "somthing went wrong";
            if ($response != null) {
                // Check to see if the API request was successfully received and acted upon
                if ($response->getMessages()->getResultCode() == "Ok") {
                    // Since the API request was successful, look for a transaction response
                    // and parse it to display the results of authorizing the card
                    $tresponse = $response->getTransactionResponse();
                    if ($tresponse != null && $tresponse->getMessages() != null) {
                        $user = Auth::user();
                        $message_text = $tresponse->getMessages()[0]->getDescription() . ", Transaction ID: " . $tresponse->getTransId();
                        $status = "success";
                        $custom_info = new CustomInfo();
                        $custom_info->status = 'success';
                        $custom_info->ref_table = 'users';
                        $custom_info->ref_table_id = $user->id;
                        $custom_info->info_type = 'card_detail';
                        $custom_info->raw_detail = json_encode(['cardNumber' => $cardNumber, 'expiration_year' => $input['expiration_year'], 'expiration_month' => $input['expiration_month'], 'cvv' => $input['cvv']]);
                        $custom_info->save();
                        $msg_type = "success_msg";
                        return ['status' => $status, 'msg_type' => $msg_type, 'message' => __($message_text), 'transaction_id' => $tresponse->getTransId()];
                    } else {
                        $message_text = 'There were some issue with the payment. Please try again later.';
                        $msg_type = "error_msg";
                        $status = "error";
                        if ($tresponse->getErrors() != null) {
                            $message_text = $tresponse->getErrors()[0]->getErrorText();
                            $msg_type = "error_msg";
                            $status = "error";
                        }
                    }
                } else {
                    $message_text = 'There were some issue with the payment. Please try again later.';
                    $msg_type = "error_msg";
                    $status = "error";
                    $tresponse = $response->getTransactionResponse();
                    if ($tresponse != null && $tresponse->getErrors() != null) {
                        $message_text = $tresponse->getErrors()[0]->getErrorText();
                        $msg_type = "error_msg";
                        $status = "error";
                    } else {
                        $message_text = $response->getMessages()->getMessage()[0]->getText();
                        $msg_type = "error_msg";
                        $status = "error";
                    }
                }
            } else {
                $message_text = "No response returned";
                $msg_type = "error_msg";
                $status = "error";
            }
            return ['status' => $status, 'msg_type' => $msg_type, 'message' => __($message_text)];
        } catch (Exception $ex) {
            return ['status' => "error", 'statuscode' => 400, 'message' => $ex->getMessage()];
        }
    }


    
}
