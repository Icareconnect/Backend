<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SupportReply extends Model
{
     protected $fillable = [
        'support_id', 'answered_by','description'
    ];
}
