<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RecentView extends Model
{
    /**
     * Get the Request History From RequestHistory Model.
     */
    public function doctor_data()
    {
        return $this->hasOne('App\User','id','whose_id');
    }
}
