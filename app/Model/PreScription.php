<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PreScription extends Model
{
	 /**
     * Get the Request History From RequestHistory Model.
     */
    public function medicines()
    {
        return $this->hasMany('App\Model\PreScriptionMedicine','pre_scription_id');
    }
}
