<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    //
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'from', 'to', 'transaction_id'
	    ];

    public function transaction()
    {
        return $this->hasOne('App\Model\Transaction','id','transaction_id');
    }
}
