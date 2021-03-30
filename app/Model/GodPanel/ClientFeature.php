<?php

namespace App\Model\GodPanel;

use Illuminate\Database\Eloquent\Model;

class ClientFeature extends Model
{
    protected $table = 'godpanel_client_features';

    protected $connection= 'godpanel';
    
    protected $fillable = ['client_id', 'feature_id'];

    public function feature()
    {
        return $this->hasOne('App\Model\GodPanel\Feature','id','feature_id');
    }
}
