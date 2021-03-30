<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RequestDate extends Model
{
    /**
     * Get the Request History From RequestHistory Model.
     */
    public function requesthistory()
    {
        return $this->hasOne('App\Model\RequestHistory','request_id','request_id');
    }

    public function request()
    {
        return $this->hasOne('App\Model\Request','id','request_id');
    }
}
