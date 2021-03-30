<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Support,App\Model\SupportAssignee,App\Model\SupportReply;
use Auth;
use App\Notification;
class SupportQuestionController extends Controller
{
    public function getaskSupportQuestion(Request $request){
        $user = Auth::user();
        $questions = SupportAssignee::select('*')->where([
            'assigned_to'=>$user->id
        ])
        ->orderBy('id', 'desc')
        ->get();
        foreach ($questions as $key => $question) {
            $questions[$key] =  Support::getUserQuestionFormat2($question->support_id);
        }
        return view('admin.support.index')->with(array('questions'=>$questions));
    }

    public function replyQuestion(Request $request,$id){
    	$user = Auth::user();
    	$supportassignee = SupportAssignee::where(['assigned_to'=>$user->id,'support_id'=>$id])->first();
    	if(!$supportassignee){
    		abort(400);
    	}
        $question =  Support::getUserQuestionFormat2($id);
        return view('admin.support.reply')->with(array('question'=>$question));
    }

    public function viewAskSupportQuestion(Request $request,$id){
    	$user = Auth::user();
    	$supportassignee = SupportAssignee::where(['assigned_to'=>$user->id,'support_id'=>$id])->first();
    	if(!$supportassignee){
    		abort(400);
    	}
        $question =  Support::getUserQuestionFormat2($id);
        return view('admin.support.view')->with(array('question'=>$question));
    }

    public function postReplyQuestion(Request $request,$id){
    	$user = Auth::user();
    	$supportassignee = SupportAssignee::where(['assigned_to'=>$user->id,'support_id'=>$id])->first();
    	if(!$supportassignee){
    		abort(400);
    	}
    	SupportReply::create(['support_id'=>$id,'answered_by'=>$user->id,'description'=>$request->answer]);
    	$support =  Support::where('id',$id)->first();
    	$support->status = 'answered';
    	$support->save();

    	$notification = new Notification();
	    $notification->push_notification(
	            array($support->created_by),
	                array(
	                'pushType'=>'QUESTION_ANSWERED',
	                'request_id'=>$support->id,
	                'message'=>__("Your question has been answered by $user->name")
	            )
	     );
    	return redirect('admin/support_questions');
    }
}
