<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    //
    public function getBalanceAttribute($value) {
	    return round($value, 2);
	}
}
