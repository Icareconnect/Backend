<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\User;
class Role extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    public function users()
	{
	  return $this->belongsToMany(User::class);
	}
}
