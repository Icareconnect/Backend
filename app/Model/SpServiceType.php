<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SpServiceType extends Model
{
	 /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sp_id', 'category_service_id'
    ];


    /**
     * Get the Request History From RequestHistory Model.
     */
    public function doctor_data()
    {
        return $this->hasOne('App\User','id','sp_id');
    }
    public function profile()
    {
        return $this->hasOne('App\Model\Profile','user_id','sp_id');
    }
    /**
     * Get the Request History From RequestHistory Model.
     */
    public function category_service_type()
    {
        return $this->hasOne('App\Model\CategoryServiceType','id','category_service_id');
    }

    public function categoryServiceProvider()
    {
        return $this->hasOne('App\Model\CategoryServiceProvider','sp_id','sp_id');
    }

    public function checkServiceSubscribe($consultant_id,$service_id){
        $check = self::where(['sp_id'=>$consultant_id,'category_service_id'=>$service_id])->first();
        if($check){
            return true;
        }else{
            return false;
        }
    }
}
