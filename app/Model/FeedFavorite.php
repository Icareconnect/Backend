<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FeedFavorite extends Model
{
	protected $fillable = [
        'user_id','feed_id'
    ];
}
