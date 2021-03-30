<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Coupon extends Model
{

	 use SoftDeletes;
	/**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['deleted_at'];

    public static function usedCoupon($id){
        $used_coupon = \App\Model\CouponUsed::where(['coupon_id'=>$id])->count();
        return $used_coupon;
    }

    public function category()
    {
        return $this->hasOne('App\Model\Category','id','category_id');
    }
    public function service()
    {
        return $this->hasOne('App\Model\Service','id','service_id');
    }
}
