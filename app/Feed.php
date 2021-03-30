<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Feed extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'title', 'image', 'description','user_id'
    ];

    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['deleted_at'];

    /**
     * Get the Service Type From Service Model.
     */
    public function user()
    {
        return $this->hasOne('App\User','id','user_id');
    }
}
