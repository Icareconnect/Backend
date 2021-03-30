<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DailyWaterDate extends Model
{
    public function dailyglass(){
        return $this->hasOne('App\Model\DailyGlass', 'user_id', 'user_id')->latest();
    }

    public function user(){
        return $this->hasOne('App\User','id', 'user_id');
    }
}
