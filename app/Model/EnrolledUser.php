<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class EnrolledUser extends Model
{
	public function user()
    {
        return $this->hasOne('App\User','id','assinged_user');
    }
}
