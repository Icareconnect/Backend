<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RequestHistory extends Model
{
    //
    protected $table = 'request_history';

    protected $fillable = [
        'duration', 'total_charges', 'status','request_id'
    ];

    /**
     * Get the Request History From RequestHistory Model.
     */
    public function request()
    {
        return $this->hasOne('App\Model\Request','id','request_id');
    }
}
