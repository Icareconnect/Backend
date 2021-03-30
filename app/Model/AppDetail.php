<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AppDetail extends Model
{
    public function getBackGroundColorAttribute($value) {
        if($value){
            return '#'.$value;
        }else{
            return null;
        }
    }
}
