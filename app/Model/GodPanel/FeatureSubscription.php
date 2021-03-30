<?php

namespace App\Model\GodPanel;

use Illuminate\Database\Eloquent\Model;

class FeatureSubscription extends Model
{
    protected $table = 'godpanel_feature_subscriptions';
    protected $connection= 'godpanel';
    public function subscription()
    {
        return $this->hasOne('App\Model\GodPanel\Subscription','id','subscription_id');
    }
}
