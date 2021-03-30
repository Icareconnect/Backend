<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FeedLike extends Model
{
	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at','dislike'
    ];
    public function user()
    {
        return $this->hasOne('App\User','id','user_id');
    }
}
