<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    protected $fillable = [
        'user_id', 'provider_id', 'provider',
    ];
}
