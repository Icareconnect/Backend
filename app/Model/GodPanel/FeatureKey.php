<?php

namespace App\Model\GodPanel;

use Illuminate\Database\Eloquent\Model;

class FeatureKey extends Model
{
    protected $table = 'godpanel_feature_keys';
    protected $connection= 'godpanel';
}
