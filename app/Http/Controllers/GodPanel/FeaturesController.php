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
class FeaturesController extends Controller
{
   public function getFeaturePage(){
   		$feature_types = FeatureType::orderBy('id','desc')->get();
   		$features = Feature::orderBy('id','desc')->get();
   		return view('godpanel.features.features',compact('feature_types','features'));
   }

   public function getSubscriptionPage(){
   		$subscriptions = Subscription::orderBy('id','desc')->get();
   		return view('godpanel.subscriptions',compact('subscriptions'));
   }

   public function createSubscriptionPage(){
   		return view('godpanel.add_subscription');
   }

   public function addFeatureTypePage(){
   		return view('godpanel.features.add_feature_type');
   }

   public function addNewFeaturePage(){
   		$subscriptions = Subscription::orderBy('id','desc')->get();
   		$feature_types = FeatureType::orderBy('id','desc')->get();
   		return view('godpanel.features.add_feature',compact('subscriptions','feature_types'));
   }

   public function editSubscriptionPage($subscription_id){
   		$subscription = Subscription::where('id',$subscription_id)->first();
   		return view('godpanel.edit_subscription',compact('subscription'));
   }

   public function getEditFeaturePage($feature_id){
   		$subscriptions = Subscription::orderBy('id','desc')->get();
   		$feature_types = FeatureType::orderBy('id','desc')->get();
   		$feature = Feature::where('id',$feature_id)->first();
   		return view('godpanel.features.edit_feature',compact('feature','subscriptions','feature_types'));
   }

   public function postEditFeature(Request $request,$feature_id){
   		$msg = [];
	    $rule = [
            'name' => 'required',
            'feature_type' => 'required',
            'subscription_plan' => 'required',
      	];
	    $validator = \Validator::make($request->all(),$rule,$msg);
	    if ($validator->fails()) {
	            return back()->withErrors($validator)->withInput();
	    }
	    $input = $request->all();
	    $feature = Feature::where('id',$feature_id)->first();
	    $feature->name = $input['name'];
	    $feature->feature_type_id = $input['feature_type'];
	    if($feature->save()){
	    	$deleted = array_diff($feature->subscriptions->pluck('subscription_id')->toArray(), $input['subscription_plan']);
            $new_subscriptions = array_diff($input['subscription_plan'],$feature->subscriptions->pluck('subscription_id')->toArray());
            if(count($deleted)>0)
                $deleted_subscriptions = FeatureSubscription::where('feature_id',$feature_id)->whereIn('subscription_id',$deleted)->delete();
            foreach ($new_subscriptions as $key => $subscription_plan) {
                if($subscription_plan){
                    $featuresubscription = new FeatureSubscription();
	    			$featuresubscription->feature_id = $feature->id;
	    			$featuresubscription->subscription_id = $subscription_plan;
	    			$featuresubscription->save();
                }
            }
	    	if(isset($input['feature_keys'])){
	    		foreach ($input['feature_keys'] as $key2 => $feature_key) {
	    			$featurekey = FeatureKey::where('id',$feature_key['id'])->first();
	    			$featurekey->key_name = $feature_key['name'];
	    			if(isset($feature_key['for_front_end']) && $feature_key['for_front_end']=='on'){
	    				$featurekey->for_fron_end = '1';
	    			}else{
	    				$featurekey->for_fron_end = '0';
	    			}
	    			$featurekey->save();
	    		}
	    	}
	    	if(isset($input['new_feature_keys'])){
	    		foreach ($input['new_feature_keys'] as $key2 => $feature_key) {
	    			$featurekey = new FeatureKey();
	    			$featurekey->key_name = $feature_key['name'];
	    			$featurekey->feature_id =  $feature->id;
	    			if(isset($feature_key['for_front_end']) && $feature_key['for_front_end']=='on'){
	    				$featurekey->for_fron_end = '1';
	    			}else{
	    				$featurekey->for_fron_end = '0';
	    			}
	    			$featurekey->save();
	    		}
	    	}
	    	if($input['deleted_keys']){
	    		$deleted_keys = explode(",",$input['deleted_keys']);
	    		FeatureKey::whereIn("id",$deleted_keys)->where('feature_id',$feature_id)->delete();
	    	}
	    }
	    $client = new \Predis\Client();
        $client->flushAll();
	    return redirect('features'); 
	}

   public function postFeatureType(Request $request){
   		$input = $request->all();
	    $featuretype = new FeatureType();
	    $featuretype->name = $input['name'];
	    $featuretype->save();
	    return redirect('features'); 
   }

   public function updateFeatureType(Request $request){
   		$input = $request->all();
   		$featuretype = FeatureType::where('id',$input['feature_type_id'])->first();
   		$featuretype->name = $input['name'];
   		$featuretype->save();
	    return redirect('features'); 
   }

   public function postSubscription(Request $request){
   		$msg = [];
	    $rule = [
            'name' => 'required',
            'type' => 'required',
            'price' => 'required',
            'global_subscription' => 'required',
      	];
	    $validator = \Validator::make($request->all(),$rule,$msg);
	    if ($validator->fails()) {
	            return back()->withErrors($validator)->withInput();
	    }
	    $input = $request->all();
	    $subscription = new Subscription();
	    $subscription->name = $input['name'];
	    $subscription->price = $input['price'];
	    $subscription->type = $input['type'];
	    $subscription->global_type = $input['global_subscription'];
	    $subscription->save();
	    return redirect('subscriptions'); 
	} 


	public function postNewFeature(Request $request){
   		$msg = [];
	    $rule = [
            'name' => 'required',
            'feature_type' => 'required',
            'subscription_plan' => 'required',
      	];
	    $validator = \Validator::make($request->all(),$rule,$msg);
	    if ($validator->fails()) {
	            return back()->withErrors($validator)->withInput();
	    }
	    $input = $request->all();
	    $feature = new Feature();
	    $feature->name = $input['name'];
	    $feature->feature_type_id = $input['feature_type'];
	    if($feature->save()){
	    	if($input['subscription_plan']){
	    		foreach ($input['subscription_plan'] as $key => $subscription_plan) {
	    			$featuresubscription = new FeatureSubscription();
	    			$featuresubscription->feature_id = $feature->id;
	    			$featuresubscription->subscription_id = $subscription_plan;
	    			$featuresubscription->save();
	    		}
	    	}
	    	if(isset($input['feature_keys'])){
	    		foreach ($input['feature_keys'] as $key2 => $feature_key) {
	    			$featurekey = new FeatureKey();
	    			$featurekey->key_name = $input['feature_keys'][$key2]['name'];
	    			$featurekey->feature_id =  $feature->id;
	    			if(isset($feature_key['for_front_end']) && $feature_key['for_front_end']=='on'){
	    				$featurekey->for_fron_end = '1';
	    			}else{
	    				$featurekey->for_fron_end = '0';
	    			}
	    			$featurekey->save();
	    		}
	    	}
	    }
	    return redirect('features'); 
	}

	public function updateSubscription(Request $request,$subscription_id){
   		$msg = [];
	    $rule = [
            'name' => 'required',
            'type' => 'required',
            'price' => 'required',
            'global_subscription' => 'required',
      	];
	    $validator = \Validator::make($request->all(),$rule,$msg);
	    if ($validator->fails()) {
	            return back()->withErrors($validator)->withInput();
	    }
	    $input = $request->all();
	    $subscription = Subscription::where('id',$subscription_id)->first();
	    $subscription->name = $input['name'];
	    $subscription->price = $input['price'];
	    $subscription->type = $input['type'];
	    $subscription->global_type = $input['global_subscription'];
	    $subscription->save();
	    return redirect('subscriptions'); 
	}
}
