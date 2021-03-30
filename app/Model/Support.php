<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\SupportReply;
use App\User;
use App\Model\SupportAssignee;
use App\Model\MasterPackage;
class Support extends Model
{
   protected $fillable = [
        'title','description','type','created_by','status','amount','transaction_id','master_package_id'
    ];

    public function support(){
      return $this->hasOne('App\Model\SupportAssignee','support_id','id');
    }

    public static function checkCanCreateQuestion($user_id){
        $today_date = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        $today_end_date = \Carbon\Carbon::now()->addDays(-5)->format('Y-m-d H:i:s');
        $asked = self::where(['created_by'=>$user_id])
        ->whereBetween('created_at',[$today_end_date,$today_date])->first();
        if($asked){
            return false;
        }else{
            return true;
        }
    }

    public static function getUserQuestionFormat($question,$user_id=null)
    {

    	$answers = [];
    	$supportreply = SupportReply::select('description as answer','created_at','updated_at','answered_by')->where('support_id',$question->id)->get();
    	$question->created_by = User::select(['id', 'name','profile_image'])->where('id',$question->created_by)->first();
        $you_answered = false;
    	foreach ($supportreply as $key => $answer) {
    		$user_data = User::select(['id', 'name','profile_image'])->where('id',$answer->answered_by)->first();
            if($user_id == $answer->answered_by){
                $you_answered = true;
            }
    		$answer->user = $user_data;
    		unset($answer->answered_by);
    		$answers[] = $answer;
    	}
        if($user_id){
            $question->you_answered = $you_answered;
        }
        $package = null;
        if($question->master_package_id){
            $package = \App\Model\MasterPackage::select('id','title','description','price')->where('id',$question->master_package_id)->first();
        }
        $question->package = $package;
    	$question->answers = $answers;
    	return $question;
    }

    public static function getUserQuestionFormat2($question_id,$user_id=null)
    {
    	$question = self::where('id',$question_id)->first();
    	$answers = [];
    	$supportreply = SupportReply::select('description as answer','created_at','updated_at','answered_by')->where('support_id',$question->id)->get();
    	$question->created_by = User::select(['id', 'name','profile_image'])->where('id',$question->created_by)->first();
        $you_answered = false;
    	foreach ($supportreply as $key => $answer) {
    		$user_data = User::select(['id', 'name','profile_image'])->where('id',$answer->answered_by)->first();
            if($user_id == $answer->answered_by){
                $you_answered = true;
            }
    		$answer->user = $user_data;
    		unset($answer->answered_by);
    		$answers[] = $answer;
    	}
        if($user_id){
            $question->you_answered = $you_answered;
        }
        $package = null;
        if($question->master_package_id){
            $package = \App\Model\MasterPackage::select('id','title','description','price')->where('id',$question->master_package_id)->first();
        }
    	$question->package = $package;
        $question->answers = $answers;
    	return $question;
    }
}
