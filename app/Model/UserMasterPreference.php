<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserMasterPreference extends Model
{
    protected $fillable = ['preference_id','preference_option_id','user_id','request_id'];
}
