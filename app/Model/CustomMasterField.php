<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;
class CustomMasterField extends Model
{
    use SoftDeletes;

    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['deleted_at'];

    public function data()
    {
        return $this->hasMany('App\Model\CustomUserMasterField','custom_field_id')->select(['id', 'field_value as name','field_value_type as type','created_at','custom_field_id'])->where('user_id',Auth::user()->id);
    }
}
