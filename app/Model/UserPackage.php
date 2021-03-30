<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserPackage extends Model
{
    protected $fillable = [
        'package_id', 'user_id','available_requests'
    ];


    /**
    * User Cards
    * @param 
    */
    public function package()
    {
        return $this->hasOne('App\Model\Package','id','package_id');
    }
}
