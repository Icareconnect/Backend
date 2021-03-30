<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Course extends Model
{
	 protected $fillable = ['title','color_code','image_icon'];

	 //use SoftDeletes;
	/**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    // protected $dates = ['deleted_at'];

   	public function SpCourses(){
        return $this->hasMany('App\Model\SpCourse', 'course_id');
    }
    

 }
