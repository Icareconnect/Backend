<?php

namespace App\Model\GodPanel;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $table = 'godpanel_features';
    protected $connection= 'godpanel';
    public function subscriptions()
    {
        return $this->hasMany('App\Model\GodPanel\FeatureSubscription','feature_id');
    }

    public function feature_keys()
    {
        return $this->hasMany('App\Model\GodPanel\FeatureKey','feature_id');
    }

    public function feature_type()
    {
        return $this->hasOne('App\Model\GodPanel\FeatureType','id','feature_type_id');
    }
}
