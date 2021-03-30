<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PayoutRequest extends Model
{
	public function cus_info()
    {
        return $this->hasOne('App\User','id','vendor_id');
    }

    public function transaction()
    {
        return $this->hasOne('App\Model\Transaction','id','transaction_id');
    }

    public function account()
    {
        return $this->hasOne('App\Model\BankAccount','id','account_id');
    }
}
