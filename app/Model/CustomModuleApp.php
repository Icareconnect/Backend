<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
class CustomModuleApp extends Model
{
   protected $fillable = ['name','domain_name', 'domain_url','app_url','image','country_code','country_name','properties','status'];

	use SoftDeletes;
    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['deleted_at'];

    protected $casts = [
        'properties' => 'array'
    ];
}
