<?php

namespace App\Http\Controllers\GodPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Country;
use App\Model\Client;
use App\Model\GodPanel\Config as GodConfig;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use App\Jobs\ProcessClientDataBase;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
class GodPanelController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function getDashboard()
    {
        return view('godpanel.dashboard');
    }

    public function getClientForm(){
    	$countries = Country::select('sortname','name')->get();
        return view('godpanel.create_app',compact('countries'));
    }

    public function getVariables(){
        $godconfigs = GodConfig::orderBy('id','desc')->get();
        $cache = false;
        foreach ($godconfigs as $key => $value) {
            if(strtolower($value->key_name)=='cache enable' && $value->key_value=='1'){
                $cache = true;
            }
        }
        return view('godpanel.config',compact('godconfigs','cache'));
    }

    public function postUpdateVariables(Request $request){
        $input = $request->all();
        $godconfig = GodConfig::where('id',$request->godconfig_id)->first();
        if($godconfig){
            if(strtolower($godconfig->key_name)=='cache enable'){
                if($input['enable']=='false'){
                    $godconfig->key_value = '0';
                }else{
                    $godconfig->key_value = '1';
                }
                $godconfig->save();
            }elseif (strtolower($godconfig->key_name)=='cache clear') {
                   $client = new \Predis\Client();
                   $client->flushAll();
            }

        }
        return response()->json(['status'=>'success']);
    }

    public function getClients(){
    	$clients =  Client::get();
    	return view('godpanel.clients',compact('clients'));
    }

    public function checkDomain(Request $request){
        $domain = Str::slug($request->domain,'');
        $already_exist = ['dbaccess','node','godpanel'];
        if(in_array($domain, $already_exist)){
        	return response()->json(['status'=>'error']);
        }
        $exist = Client::where('domain_name',$domain)->first();
        if($exist){
            return response()->json(['status'=>'error']);
        }else{
            return response()->json(['status'=>'success','domain'=>$domain]);
        }
    }

    public function postClientCreate(Request $request){
    	$request->merge(['domain_name' => Str::slug($request->domain_name,'')]);
    	\Validator::extend('not_contains', function($attribute, $value, $parameters)
			{
			    $words = array('dbaccess','node','godpanel');
			    foreach ($words as $word)
			    {
			        if ($value==$word) return false;
			    }
			    return true;
			});
    	$rules = [
            'app_name' => 'required|string|min:3',
            'domain_name' => 'required|string|unique:clients,domain_name|min:3|not_contains',
            'email'=>'required|email|unique:clients,email',
            'first_name'=>'required',
            'last_name'=>'required',
            'password'=>'required|min:5|max:20',
            'country'=>'required',
        ];
		$messages = array(
		    'domain_name.not_contains' => 'The :attribute already taken',
		);
    	$validator = \Validator::make($request->all(), $rules,$messages);
        // $decryption=openssl_decrypt($encryption,$chiper,$encryption_key,$options,$encryption_iv);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $input = $request->all();
        $country = Country::select('sortname','name')->where('sortname',$input['country'])->first();
        $client = new Client();
        $client->name = $input['app_name'];
        $client->domain_name = $input['domain_name'];
        $client->db_id = $input['domain_name'];
        $client->first_name = $input['first_name'];
        $client->last_name = $input['last_name'];
        $client->email = $input['email'];
        $chiper = 'AES-256-CBC';
        $options = 0;
        $encryption_iv = '1234567891011121';
        $encryption_key = "consultappclient";
        $client->password = openssl_encrypt($input['password'],$chiper,$encryption_key,$options,$encryption_iv);
        $client->country_code = $country->sortname;
        $client->country_name = $country->name;
        $client->status = $input['status'];
        $client->save();
        // $this->dispatchNow(new ProcessClientDataBase($client->id));
        return redirect('clients');
    }
}
