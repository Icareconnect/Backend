<?php

namespace App\Http\Controllers\GodPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Model\Country;
use App\Model\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use App\Jobs\ProcessClientDataBase;
use App\Model\GodPanel\Subscription;
use App\Model\GodPanel\FeatureType;
use App\Model\GodPanel\Feature;
use App\Model\GodPanel\FeatureKey;
use App\Model\GodPanel\FeatureSubscription;
use App\Model\GodPanel\ClientFeature;
class ClientController extends Controller
{

	public function getClientFeatures($client_id){
		$features = Feature::orderBy('id','desc')->get();
		$client_features = ClientFeature::where('client_id',$client_id)->pluck('feature_id')->toArray();
		return view('godpanel.client.client_features',compact('features','client_features','client_id'));
	}

	public function postClientFeatureUpdate(Request $request,$client_id){
		if($request->assign=='true'){
			ClientFeature::firstOrCreate(['client_id'=>$client_id,'feature_id'=>$request->feature_id]);
		}else{
			ClientFeature::where(['feature_id'=>$request->feature_id,'client_id'=>$client_id])->delete();
		}
		$client = new \Predis\Client();
        $client->flushAll();
		return response()->json(['status'=>'success']);
	}

}
