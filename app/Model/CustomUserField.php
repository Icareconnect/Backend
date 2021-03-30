<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CustomUserField extends Model
{
	/**
     * Get the Request History From RequestHistory Model.
     */
    public function customfield()
    {
        return $this->hasOne('App\Model\CustomField','id','custom_field_id');
    }

    public function user(){

    	return $this->belongsTo('App\user','user_id','id');
    }
}
