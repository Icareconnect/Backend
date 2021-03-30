<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MasterPreferencesOption extends Model
{
     protected $fillable = ['preference_id','name'];

     public function masterpreference(){
        return $this->hasOne('App\Model\MasterPreference','id','preference_id');
    }

    public function filterDuty(){
        return $this->hasMany('App\Model\MasterPreOptionFilter','option_id');
    }
}
