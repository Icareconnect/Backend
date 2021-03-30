<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AdditionalDetail extends Model
{
    public function category()
    {
        return $this->hasOne('App\Model\Category','id','category_id');
    }
}
