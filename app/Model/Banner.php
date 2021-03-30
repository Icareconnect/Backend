<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
	/**
    * User Cards
    * @param 
    */
    public function sp_data()
    {
        return $this->hasOne('App\User','id','sp_id');
    }
    /**
    * User Cards
    * @param 
    */
    public function service_provider()
    {
        return $this->hasOne('App\User','id','sp_id');
    }
    /**
    * User Cards
    * @param 
    */
    public function category()
    {
        return $this->hasOne('App\Model\Category','id','category_id');
    }

    /**
    * User Cards
    * @param 
    */
    public function class()
    {
        return $this->hasOne('App\Model\ConsultClass','id','class_id');
    }

    // public function getPositionAttribute($value) {
    //     return (int)($value);
    // }
}
