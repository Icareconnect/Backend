<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    /**
     * Get the Request History From RequestHistory Model.
     */
    public function country()
    {
        return $this->hasOne('App\Model\Country','currency_id');
    }
}
