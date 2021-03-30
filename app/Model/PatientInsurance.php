<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PatientInsurance extends Model
{
    /**
     * Get the Request History From RequestHistory Model.
     */
    public function insurance()
    {

        return $this->belongsTO('App\Model\Insurance','insurance_id','id');
    }
}
