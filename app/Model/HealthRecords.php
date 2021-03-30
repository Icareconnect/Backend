<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class HealthRecords extends Model
{
     protected $fillable = ['user_id','type','name','hospital_name','health_care_visit','date_of_approved','records_value','tell_us'];

     public function htRecordImg()
    {
        return $this->hasMany('App\Model\HealthRecordImage','ht_record_id','id');
    }
}
