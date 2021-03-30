<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ClientQueue extends Model
{
	/**
     * Get the Request History From RequestHistory Model.
     */
    public function client()
    {
        return $this->hasOne('App\Model\CustomModuleApp','id','client_id');
    }
}
