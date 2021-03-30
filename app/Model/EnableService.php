<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class EnableService extends Model
{
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'key_name', 'value','refrence_table_id'
    ];
}
