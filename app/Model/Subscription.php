<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    //

    /**
     * Get the Request History From RequestHistory Model.
     */
    public function doctor_data()
    {
        return $this->hasOne('App\User','id','consultant_id');
    }
    /**
     * Get the Request History From RequestHistory Model.
     */
    public function service_data()
    {
        return $this->hasOne('App\Model\Service','id','service_id');
    }

    public function categoryServiceProvider()
    {
        return $this->hasOne('App\Model\CategoryServiceProvider','sp_id','consultant_id');
    }

    public function checkServiceSubscribe($consultant_id,$service_id){
        $check = self::where(['consultant_id'=>$consultant_id,'service_id'=>$service_id])->first();
        if($check){
            return true;
        }else{
            return false;
        }
    }
}
