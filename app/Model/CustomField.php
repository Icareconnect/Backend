<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    /**
    * User Profile
    * @param 
    */
    public function user_type_value()
    {
        return $this->hasOne('App\Model\Role','user_type');
    }
}
