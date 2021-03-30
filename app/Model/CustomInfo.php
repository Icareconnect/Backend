<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CustomInfo extends Model
{
    protected $fillable = ['info_type','raw_detail','ref_table','ref_table_id','status'];
}
