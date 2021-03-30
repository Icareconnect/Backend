<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FeedComment extends Model
{

	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
    ];

    public function user()
    {
        return $this->hasOne('App\User','id','user_id');
    }
}
