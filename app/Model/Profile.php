<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Service;
use App\Model\Country;
use App\Model\State;
use App\Model\City;
use App\Model\Subscription;
class Profile extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'address', 'dob', 'avatar','qualification','city','state','country','experience','rating','about','user_id','speciality','call_price','chat_price'
    ];
    public function getLocationAttribute()
    {
       return ["name"=>$this->location_name,"lat"=>$this->lat,"long"=>$this->long];
    }
    public function getRatingAttribute($value) {
        return round($value, 2);
    }

    public function getDobAttribute($value) {
        if($value=='0000-00-00'){
            return null;
        }else{
            return $value;
        }
    }

    public function getAcceptSelfPayAttribute($value) {
        if($value){
            return true;
        }else{
            return false;
        }
    }

    public function getCountryAttribute($value) {
        $country =  Country::where('id',$value)->first();
        if($country)
            $value = $country->name;
        return $value;
    }
    public function getStateAttribute($value) {
        $state =  State::where('id',$value)->first();
        if($state)
            $value = $state->name;
        return $value;
    }
    public function getCityAttribute($value) {
        $city =  City::where('id',$value)->first();
        if($city)
            $value = $city->name;
        return $value;
    }
    public function setSubscription($profile){
    	if($profile->call_price){ 
    		$service_id = Service::getServiceId('call');
    		$subscription = Subscription::where([
    			'consultant_id'=>$profile->user_id,
    			'service_id'=>$service_id])
    		->first();
    		if($subscription){
    			$subscription->duration = 60;
    			$subscription->charges = $profile->call_price;
    			$subscription->save();
    		}
    	}
    	if($profile->chat_price){
    		$service_id = Service::getServiceId('chat');
    		$subscription = Subscription::where([
    			'consultant_id'=>$profile->user_id,
    			'service_id'=>$service_id])
    		->first();
    		if($subscription){
    			$subscription->duration = 60;
    			$subscription->charges = $profile->chat_price;
    			$subscription->save();
    		}
    	}
    }
}
