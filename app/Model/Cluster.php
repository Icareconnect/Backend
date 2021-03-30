<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Cluster extends Model
{
	/**
    * User Cards
    * @param 
    */
    public function cluster_category()
    {
        return $this->hasMany('App\Model\ClusterCategory','cluster_id');
    }
}
