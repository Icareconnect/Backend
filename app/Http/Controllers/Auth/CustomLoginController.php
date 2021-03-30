<?php
  
namespace App\Http\Controllers\Auth;
   
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\User;
use App\Model\SecurityQuestion;
use App\Model\UserSecurityAnswer;
use Illuminate\Support\Facades\Auth;
use Hash;
use Illuminate\Validation\ValidationException;
class CustomLoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
  
    use AuthenticatesUsers;
  
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';
   
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout','customLogin','profileUpdate','checkEmailUserNameExist','checkVerifyAnswer');
    }

    public function checkEmailUserNameExist(Request $request){
        $input = $request->all();
        $column_name = 'email';
        $domain = "mp2r";
        if($domain=="mp2r"){
            if(filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                $column_name = 'email';
            }else {
                $column_name = 'user_name';
                unset($request['email']);
            }
        }
        $exist = User::whereHas('roles', function ($query) {
           $query->whereIn('name',['service_provider','customer']);
        })->where($column_name,$input['email'])->first();
        if(!$exist){
            return response(['status' => "error", 'statuscode' => 400, 'message' =>" The $column_name account that you tried to reach does not exist."], 400); 
        }
        $answers_raw = UserSecurityAnswer::where(['user_id'=>$exist->id])->get();
        if($answers_raw->count()>0){
            $answers = [];
            foreach ($answers_raw as $key => $answer) {
                $ss_q = SecurityQuestion::where(['id'=>$answer->security_question_id])->first();
                $answer->question = $ss_q->question;
                $answer->user_answer = "";
                $answers[$ss_q->type] = $answer;
            }
            return response(['status' => "success", 'statuscode' => 200, 'message' =>"Listing",'data'=>['questions'=>$answers]],200); 
        }else{
            return response(['status' => "error", 'statuscode' => 400, 'message' =>"You have not added any security question/answer. Please contact to Admin for password reset"], 400);
        }

    }

    public function postResetPassword(Request $request){
        $input = $request->all();
        $exist = User::where('id',$input['user_id'])->first();
        if(!$exist){
            return response(['status' => "error", 'statuscode' => 400, 'message' =>" The account that you tried to reach does not exist."], 400); 
        }
        $rules = [
                'new_password' => 'required|string|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/',
                'confirm_password' => 'required|same:new_password',
            ];
        $customMessages = [
            'new_password.regex' => 'New Password  should contain at-least 1 Uppercase, 1 Lowercase, 1 Numeric.'
        ];
        $this->validate($request, $rules, $customMessages);
        $exist->update(['password'=> Hash::make($request->new_password)]);
        return response(['status' => "success", 'statuscode' => 200, 'message' =>"You Password has been updated successfully "],200); 

    }

    public function checkVerifyAnswer(Request $request){
        $input = $request->all();
        $column_name = 'email';
        $domain = "mp2r";
        if($domain=="mp2r"){
            if(filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                $column_name = 'email';
            }else {
                $column_name = 'user_name';
                unset($request['email']);
            }
        }
        $exist = User::whereHas('roles', function ($query) {
           $query->whereIn('name',['service_provider','customer']);
        })->where($column_name,$input['email'])->first();
        if(!$exist){
            return response(['status' => "error", 'statuscode' => 400, 'message' =>" The $column_name account that you tried to reach does not exist."], 400); 
        }
        $questions = $input['questions'];
        $answers_raw1 = UserSecurityAnswer::where([
            'user_id'=>$exist->id,
            'id'=>$questions['question1']['id'],
            'answer'=>$questions['question1']['user_answer']
        ])->first();
        if(!$answers_raw1){
            return response(['status' => "error", 'statuscode' => 400,'errors'=>['question1' =>"Not a valid Answer"]], 400);
        }
        $answers_raw2 = UserSecurityAnswer::where([
            'user_id'=>$exist->id,
            'id'=>$questions['question2']['id'],
            'answer'=>$questions['question2']['user_answer']
        ])->first();
        if(!$answers_raw2){
            return response(['status' => "error", 'statuscode' => 400, 'errors'=>['question2' =>"Not a valid Answer"]], 400);
        }
        $answers_raw3 = UserSecurityAnswer::where([
            'user_id'=>$exist->id,
            'id'=>$questions['question3']['id'],
            'answer'=>$questions['question3']['user_answer']
        ])->first();
        if(!$answers_raw3){
            return response(['status' => "error", 'statuscode' => 400, 'errors'=>['question3' =>"Not a valid Answer"]], 400);
        }
        return response([
            'status' => "success",
            'statuscode' => 200,
            'message' =>"Listing",
            'data'=>['user_id'=>$exist->id]
        ],200);

    }

    public function profileUpdate(Request $request){
        $user = Auth::user();
        $input = $request->all();
        $rules = [
            'email' => "required|email|unique:users,email," .$user->id,
            'name' => 'required',
            'number' => "required|unique:users,number," .$user->id,
        ];
        $validator = \Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return response(['status' => "error", 'statuscode' => 400, 'errors' =>$validator->getMessageBag()], 400); 
        }

        $user->number = $input['number'];
        $user->email = $input['email'];
        $user->name = $input['name'];
        $user->save();
         return response(['status' => "success", 'statuscode' => 200, 'message' =>'Profile Updated'], 200); 
    }
   
    public function customLogin(Request $request){   
        $input = $request->all();
        $column_name = 'email';
        $domain = "mp2r";
        if(isset($input['domain'])){
            $domain = $input['domain'];
        }
        if($domain=="mp2r"){
	        if(filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
	            $column_name = 'email';
	        }else {
	            $request['user_name'] = $request->email;
	            $column_name = 'user_name';
	            unset($request['email']);
	        }
        }
        // $request->column_name = $column_name;
        // $request->remember = true;
        $this->validateLogin($request);
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }
        $user = User::where('email', $input['email'])->first();
        if(!$user){
            $errors['email'] = ['The email account that you tried to reach does not exist.'];
                return response(['status' => "error", 'statuscode' => 400, 'errors' =>$errors], 400); 
        }
        if($user && (!$user->hasrole('customer') && !$user->hasrole('service_provider'))){
            $errors['email'] = ['The email account that you tried to reach does not exist.'];
            return response(['status' => "error", 'statuscode' => 400, 'errors' =>$errors], 400); 
        }
        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }
        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'email'=> 'required',
            'password' => 'required|string',
        ]);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only('email', 'password');
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'password' => ['Incorrect password'],
        ]);
    }
    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        
        $request->session()->regenerate();
        $this->clearLoginAttempts($request);
        return $this->authenticated($request, $this->guard()->user())
                ?: response(['status' => "success", 'statuscode' => 200],200);
    }
}