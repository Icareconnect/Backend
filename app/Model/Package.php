<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    /**
    * User Cards
    * @param 
    */
    public function filter_option()
    {
        return $this->hasOne('App\Model\FilterTypeOption','id','filter_id');
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
    public function userpackage()
    {
        return $this->hasOne('App\Model\UserPackage','id','package_id');
    }
}
