<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class SpCourse extends Model
{
	 protected $fillable = ['sp_id','course_id'];

	 //use SoftDeletes;
	/**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    // protected $dates = ['deleted_at'];

    public function getcourseUser($sp_id){
        $cat = Course::where('id',$sp_id)->first();

        // dd($cat);
        if($cat){
            
            unset($cat->SpCourses);  
        }
        return $cat;
    }

    public function course(){
        return $this->belongsTo('App\Model\Course');
    }
    

}
