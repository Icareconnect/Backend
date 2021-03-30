<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    //

    public function getLastFourDigitAttribute($value) {
    	// return str_repeat("x", strlen($value)-4) . substr($value, -4);
    	return substr($value, -4);
    }
   
}
