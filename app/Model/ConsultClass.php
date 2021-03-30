<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ConsultClass extends Model
{
    //
    protected $table = 'ct_classes';

    protected $fillable = ['name', 'image','booking_date','price','limit_enroll','category_id','created_by'];
	use SoftDeletes;
    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['deleted_at'];

    public function consultant()
    {
        return $this->hasOne('App\User','id','created_by');
    }

    public function enroll_users()
    {
        return $this->hasMany('App\Model\EnrolledUser','class_id','id');
    }

    public function enroll_user_data() {
        return $this->enroll_users()->select('assinged_user')->with(['user' => function($query) {
                            return $query->select(['id', 'name', 'email','phone','profile_image']);
                        }]);
    }
    public function isOccupied($class_id,$user_id)
    {
        $enr_cls = \App\Model\EnrolledUser::where(['class_id'=>$class_id,'assinged_user'=>$user_id])->first();
        if($enr_cls){
        	return true;
        }else{
        	return false;
        }
    }
}
