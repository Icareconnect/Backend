<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SubscribePlan extends Model
{
    protected $fillable = [
        'plan_id', 'user_id'
    ];

    public function plan()
    {
        return $this->hasOne('App\Model\Plan','id','plan_id');
    }
}
