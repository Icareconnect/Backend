<?php

namespace App\Model\GodPanel;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    //
    protected $table = 'godpanel_subscriptions';
    protected $connection= 'godpanel';
}
