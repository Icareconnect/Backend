<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{

	protected $table = 'feedbacks';

  protected $fillable = ['consultant_id','from_user','request_id','rating','comment'];
    //
    /**
    * User Profile
    * @param 
    */
    public function user()
    {
        return $this->hasOne('App\User','id','from_user');
    }

    public function consultant()
    {
        return $this->hasOne('App\User','id','consultant_id');
    }

    public static function updateReview($consultant_id){
       $rating = self::where('consultant_id',$consultant_id)->sum('rating');
       $total = self::where('consultant_id',$consultant_id)->count();
       $avg_rating = $rating/$total;
       $profile = \App\Model\Profile::where('user_id',$consultant_id)->first();
       if($profile){
         $profile->rating = $avg_rating;
         $profile->save();
       }
       return;
    }

    public static function reviewCountByConsulatant($consultant_id){
       $total = self::where('consultant_id',$consultant_id)->count();
       return $total;
    }
    public static function recentReviewByConsulatant($consultant_id){
       return  self::where('consultant_id',$consultant_id)->orderBy('id','desc')->get()->take(5);
    }
}
