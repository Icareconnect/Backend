<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SupportAssignee extends Model
{
    protected $fillable = [
        'assigned_to', 'support_id'
    ];
}
