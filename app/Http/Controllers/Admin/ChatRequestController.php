<?php

namespace App\Http\Controllers\Admin;
use Auth;
use App\Http\Controllers\Controller;
use App\Model\Request as RequestTable;
use App\Model\RequestHistory;
use Illuminate\Http\Request;
use Carbon\Carbon;
class ChatRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $admin = \Auth::user();
        $category = $admin->getCategoryData($admin->id);
        $doctors = [];
        if($category){
            $doctors = \App\Model\CustomInfo::where([
            'ref_table'=>'category',
            'ref_table_id'=>$category->id,
            'info_type'=>'custom_sp'])->get();
        }
        if($admin->hasRole('service_provider')){
            $chats = RequestTable::where('to_user',$admin->id)->orderBy('id','desc')->get();
        }else{
            $chats = RequestTable::orderBy('id','desc')->get();
        }
        return view('admin.chats')->with(['chats'=>$chats,'doctors'=>$doctors]);
    }

    public function changeAppointmentStatus(Request $request){
        $admin = \Auth::user();
        $req = RequestHistory::where('request_id',$request->request_id)->first();
        if($req){
            if(isset($request->admin_status)){
                $req->request->verified_hours = $request->hours;
                $req->request->admin_status = $request->status;
                $req->request->save();
            }else{
                $custominfo = new \App\Model\CustomInfo();
                $custominfo->info_type = 'request_status';
                $custominfo->ref_table = 'request';
                $custominfo->ref_table_id = $request->request_id;
                $custominfo->status = $request->status;
                $custominfo->save();
                $req->status = $request->status;
                $req->save();
                $status = ucwords(strtolower(str_replace('_', ' ', $request->status)));
                $notification = new \App\Notification();
                $notification->sender_id = $admin->id;
                $notification->receiver_id = $req->request->from_user;
                $notification->module_id = $req->request->id;
                $notification->module ='request';
                $notification->notification_type = strtoupper($request->status);
                $notification->message =__('notification.call_req_text', ['user_name' => $admin->name,'call_status'=>$status]);
                $notification->save();
                $notification->push_notification(
                array($notification->receiver_id),
                array('pushType'=>strtoupper($request->status),
                    'message'=>__('notification.call_req_text', ['user_name' => $admin->name,'call_status'=>$status]),
                    'request_id'=>$request->request_id,
                ));
            }
        }
        return response()->json(['status'=>'success']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }
     /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function createSessionAppointment(){
        $admin = \Auth::user();
        $category = $admin->getCategoryData($admin->id);
        $doctors = [];
        if($category){
            $doctors = \App\Model\CustomInfo::where([
            'ref_table'=>'category',
            'ref_table_id'=>$category->id,
            'info_type'=>'custom_sp'])->get();
        }
        $customers = \App\User::whereHas('roles', function ($query) {
                           $query->where('name','customer');
                        })->where('created_by',$admin->id)->orderBy('id','DESC')->get();
        return view('admin.appointment')->with(['customers'=>$customers,'doctors'=>$doctors]);
     }

     public function postSessionAppointment(Request $request){
        $user = \Auth::user();
        $validator = \Validator::make($request->all(), [
                'patient' => 'required',
                'physiotherapist'      => 'required',
                'appointment_date' => 'required',
          ]);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
        $input = $request->all();
        $category_id = $user->getCategoryData($user->id);
        $categoryservicetype_id = \App\Model\CategoryServiceType::where(['category_id'=>$category_id->id])->first();
        $spservicetype_id = null;
        $service_id = null;
        if($categoryservicetype_id){
            $service_id = $categoryservicetype_id->service_id;
            $spservicetype_id = \App\Model\SpServiceType::where(['category_service_id'=>$categoryservicetype_id->id,'sp_id'=>$user->id])->first();
        }
        $timezone = 'Asia/Kolkata';
        $datenow = Carbon::parse($request->appointment_date,$timezone)->setTimezone('UTC')->format('Y-m-d H:i:s');
        $sr_request = new RequestTable();
        $sr_request->from_user = $input['patient'];
        $sr_request->booking_date = $datenow;
        $sr_request->to_user = $user->id;
        $sr_request->service_id = $service_id;
        $sr_request->sp_service_type_id = ($spservicetype_id)?$spservicetype_id->id:null;
        $sr_request->save();

        $requesthistory = new \App\Model\RequestHistory();
        $requesthistory->duration = 0;
        $requesthistory->total_charges = 0;
        $requesthistory->schedule_type = 'schedule';
        $requesthistory->status = 'pending';
        $requesthistory->source_from = 'WEB';
        $requesthistory->request_id = $sr_request->id;
        $requesthistory->save();

        $new_sp = new \App\Model\CustomInfo();
        $new_sp->ref_table = 'requests';
        $new_sp->ref_table_id = $sr_request->id;
        $new_sp->info_type = 'doctor_assign';
        $new_sp->raw_detail = json_encode([
            'category_id'=>$category_id->id,
            'doctor_id'=>$input['physiotherapist'],
            'request_id'=>$sr_request->id]);
        $new_sp->save();
        return redirect('admin/requests');
     }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $request_info = RequestTable::find($id);
        return view('admin.appointment-view',compact('request_info'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(RequestTable $requesttable)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RequestTable $requesttable)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(RequestTable $requesttable)
    {
        //
    }
}
