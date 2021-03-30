<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserSecurityAnswer extends Model
{
    /**
    * User Cards
    * @param 
    */
    protected $fillable = [
        'user_id', 'security_question_id','answer'
    ];
    public function question()
    {
        return $this->hasOne('App\Model\SecurityQuestion','id','security_question_id');
    }
}
