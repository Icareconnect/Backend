<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{ 
	public function getPermissionAttribute($value) {
		if($value==null){
			return [];
		}else{
	    	return json_decode($value, true);
		}
	}
}
