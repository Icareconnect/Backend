<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CustomUserMasterField extends Model
{
    protected $fillable = [
        'field_value', 'description','user_id', 'custom_field_id','module_table','module_table_id','field_value_type'
    ];
}
