<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Service extends Model
{
    use SoftDeletes;
    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['deleted_at'];
    //
	public function getColorCodeAttribute($value) {
        if($value){
            return '#'.$value;
        }else{
            return null;
        }
    }
    public static function getServiceId($type){
    	$service = self::where('type',strtolower($type))->first();
    	if($service){
    		return $service->id;
    	}
    }

    public static function getServiceIdByMainType($type){
        $service = self::where('service_type',strtolower($type))->first();
        if($service){
            return $service->id;
        }
    }
}
