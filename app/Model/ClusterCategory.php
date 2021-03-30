<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ClusterCategory extends Model
{
    //
    protected $table = 'cluster_category';


    public function category()
    {
        return $this->hasOne('App\Model\Category','id','category_id');
    }
}
