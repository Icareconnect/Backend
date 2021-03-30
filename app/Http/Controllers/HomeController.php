<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Config;
use App\Model\Category;
use App\Helpers\Helper;
use App\Model\Country;
use App\Model\State;
use Auth;
use App\Model\Banner;
use Storage;
use Exception;
class HomeController extends Controller
{

    public function queryPost(Request $request){
        try{
            $to_email = isset($request->to_email)?$request->to_email:'adesh.codebrewlab@gmail.com';
            $number = isset($request->phone_number)?$request->phone_number:'NA';
            $subject = 'New Query From '.$request->email;
            if(isset($request->subject)){
                $subject = $request->subject;
            }
            $name = 'NA';
            if(isset($request->first_name) && isset($request->last_name)){
                $name = $request->first_name.' '.$request->last_name;
            }
            $from_name = Config::get("default")?'no-reply':Config::get("client_data")->domain_name;
            \Mail::raw($request->query_data."\nName:$name \nFrom email: $request->email \nMobile Number:$number", function ($message) use($request,$subject,$to_email,$from_name) {
              $message->from($to_email,$from_name)->to($to_email)->subject($subject);
            });
             return response(['status' => "success", 'statuscode' => 200], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }

    public function postRequestDemo(Request $request){
        try{
            $to_email = isset($request->to_email)?$request->to_email:'adesh.codebrewlab@gmail.com';
            $number = isset($request->phone_number)?$request->phone_number:'NA';
            $subject = 'Request for Demo';
            if(isset($request->subject)){
                $subject = $request->subject;
            }
            $name = 'NA';
            if(isset($request->first_name) && isset($request->last_name)){
                $name = $request->first_name.' '.$request->last_name;
            }
            $from_name = "iCareConnect";
            \Mail::raw($request->query_data."\nName:$name \nFrom email: $request->email \nMobile Number:$number \nFacility Name:$request->facility_name \nJob Title:$request->job_title \nCity:$request->city \nProvince:$request->province \nComment:$request->comment", function ($message) use($request,$subject,$to_email,$from_name) {
              $message->from($to_email,$from_name)->cc(['adesh.codebrewlab@gmail.com','rajni.codebrewlabs@gmail.com'])->to($to_email)->subject($subject);
            });
             return response(['status' => "success", 'statuscode' => 200], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }

    public function download($file_name){
        $tempImage = tempnam(sys_get_temp_dir(), $file_name);
        copy(Storage::disk('spaces')->url('uploads/'.$file_name), $tempImage);
        return response()->download($tempImage, $file_name);
    }

    public function getAboutUs(){
        if(Config::get("default")){
            return view('vendor.default.about-us');
        }else{
            return view('vendor.'.Config::get("client_data")->domain_name.'.about-us');
        }
    }

    public function getSupportPage(){
        if(Config::get("default")){
            return view('vendor.default.support');
        }else{
            return view('vendor.'.Config::get("client_data")->domain_name.'.support');
        }
    }

    public function getWebSupportPage(){
        if(Config::get("default")){
            return view('vendor.default.support');
        }else{
            return view('vendor.'.Config::get("client_data")->domain_name.'.web-support');
        }
    }
    public function getBlogView($blog_id){
        $blog = \App\Feed::select('id','title','image','description','like','user_id','created_at','views','favorite')->where('type','blog')->where('id',$blog_id)->first();
        if(Config::get("default")){
            return view('vendor.default.support',compact('blog'));
        }else{
            return view('vendor.'.Config::get("client_data")->domain_name.'.blog-view',compact('blog'));
        }
    }

    public function getContactUs(){
        if(Config::get("default")){
            return view('vendor.default.contact-us');
        }else{
            return view('vendor.'.Config::get("client_data")->domain_name.'.contact-us');
        }
    }

    public function getCovid19(){
        if(Config::get("default")){
            return view('vendor.default.covid-19');
        }else{
            return view('vendor.'.Config::get("client_data")->domain_name.'.covid-19');
        }
    }

    public function getNurseProfessionals(){
        if(Config::get("default")){
            return view('vendor.default.homepage-nurse');
        }else{
            return view('vendor.'.Config::get("client_data")->domain_name.'.homepage-nurse');
        }
    }

    public function getHomepageHomecare(){
        if(Config::get("default")){
            return view('vendor.default.homepage-homecare');
        }else{
            return view('vendor.'.Config::get("client_data")->domain_name.'.homepage-homecare');
        }
    }

    public function getWebDasboard(){
        if(Config::get("default")){
            return view('vendor.default.web-dashboard');
        }else{
            return view('vendor.'.Config::get("client_data")->domain_name.'.web-dashboard');
        }
    }

    public function getWebDasboardFacility(){
        if(Config::get("default")){
            return view('vendor.default.web-dashboard');
        }else{
            return view('vendor.'.Config::get("client_data")->domain_name.'.facility');
        }
    }

    public function getWebFacilityForm(){
        if(Config::get("default")){
            return view('vendor.default.web-dashboard');
        }else{
            return view('vendor.'.Config::get("client_data")->domain_name.'.fill_form_facility');
        }
    }

    public function getWebDasboardJob(){
        if(Config::get("default")){
            return view('vendor.default.web-jobs');
        }else{
            return view('vendor.'.Config::get("client_data")->domain_name.'.web-jobs');
        }
    }

    public function getWebDasboardNurses(){
        if(Config::get("default")){
            return view('vendor.default.web-nurses');
        }else{
            return view('vendor.'.Config::get("client_data")->domain_name.'.web-nurses');
        }
    }
    
    public function homePage()
    {

    	$categories = Category::where(['enable'=>'1','parent_id'=>null])
        ->orderBy('id',"ASC")
        ->get();
        $banners = Banner::orderBy('id','DESC')->get();

        $countries = Country::where('phonecode','!=',0)->pluck('sortname','phonecode');
    	if(Config::get("default")){
        	return view('vendor.default.home',compact('categories','countries','banners'));
    	}else if(Config::get("client_data")->domain_name=="mp2r" || Config::get("client_data")->domain_name=="food"){
             if(Auth::user() && Auth::user()->hasrole('service_provider')){
                 $user = Auth::user();
                 if($user && $user->account_step<6){
                    $account_step = $user->account_step + 1;
                    return redirect('register/service_provider?step='.$account_step);
                 }else
                 {
                    return redirect('service_provider/Appointment');
                 }
             }
             else if(Auth::user() && Auth::user()->hasrole('customer')){
                $user = Auth::user();
                 if($user && $user->account_step<2){
                    $account_step = $user->account_step + 1;
                    return redirect('register/user?step='.$account_step);
                 }else{
                    return redirect('user/home');
                 }
             }
             $us_states = State::where('country_id','=',231)->whereNotIn('name',["Byram","Cokato","District of Columbia","Lowa","Medfield","New Jersy","Ontario","Ramey","Sublimity","Trimble"])->pluck('name','id');
            return view('vendor.mp2r.home',compact('categories','countries','us_states','banners'));
        }else if(Config::get("client_data")->domain_name=="intely"){
            return view('vendor.intely.home',compact('categories'));
        }else if(Config::get("client_data")->domain_name=="heal" ){
            return view('vendor.heal.home',compact('categories'));
        }else if(Config::get("client_data")->domain_name=="iedu" ){
            return view('vendor.iedu.home',compact('categories'));
        }else if(Config::get("client_data")->domain_name=="healtcaremydoctor" ){
            $data = Helper::getBanners();
            $banners = $data['banners'];
            $blogs = $data['blogs'];
            return view('vendor.'.Config::get("client_data")->domain_name.'.home',compact('categories','banners','blogs'));
        }else{
        	return view('index');
    	}
    }

    public function getCities(Request $request){
        $state=State::select('id')->where('name',$request->state_id)->first();
        $state_id=($state->id);
        $data = \DB::table('cities')
        ->select('id','name')
        ->where('state_id',$state_id)
        ->orderBy('name','ASC')
        ->get();
        return response($data); 
    }
    public function getCityDetails(Request $request){
        $state=State::select('id')->where('name',$request->state_name)->first();
        $data = \DB::table('cities')
        ->select('id','name')
        ->where('state_id', $state->id)
        ->orderBy('name','ASC')
        ->get();
        return response($data); 
    }
}
