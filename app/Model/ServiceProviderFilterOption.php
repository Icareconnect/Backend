<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ServiceProviderFilterOption extends Model
{
    //
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sp_id', 'filter_type_id', 'filter_option_id'
    ];
}
