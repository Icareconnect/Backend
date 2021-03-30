<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MasterPackage extends Model
{
    public function getColorCodeAttribute($value) {
        if($value){
            return '#'.$value;
        }else{
            return null;
        }
    }
}
