<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GroupVendor extends Model
{
	protected $table = 'group_vendores';

	public function vendor()
    {
        return $this->hasOne('App\User','id','user_id');
    }

    public function group()
    {
        return $this->hasOne('App\Model\Group','id','group_id');
    }
}
