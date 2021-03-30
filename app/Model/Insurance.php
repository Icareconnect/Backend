<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{
	protected $fillable = [
        'name', 'carrier_code',
    ];
    
    public function category()
    {
        return $this->hasOne('App\Model\Category','id','category_id');
    }


    public function userinsurance()
    {
        return $this->hasOne('App\Model\UserInsurance');
    }
}
