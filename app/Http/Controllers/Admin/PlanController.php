<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    protected $permissions;

     public function __construct(){
        $this->permissions = [
              "Real Time Patient Resource Directory",
              "Area Counselors List",
              "Area Peer Support List",
              "Area MAT Provider List",
              "Unlimited Instant Messaging",
              "Service Provider Profile",
              "Listing in a Local region Active Directory with Open Sheduling(up to 10 zip codes)",
              "Geo-targeted Banner Ad Rotaion",
              "Access to On-Demand Video Conferencing",
              "Listing in up to 3 state-wide Active Directories with Open Sheduling",
              "Emphasize Bold Listing",
              "Unlimited Real-time Insurance Elegibility Verification",
              "$25 Additional Per Month Unlimited Real-time Insurance Elegibility Verification",
              "$500 Additional Per Month Group Practice Button Association",
            ];
     }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $plans = Plan::orderBy('id', 'desc')->get();
        return view('admin.plan.index')->with(array('plans'=>$plans));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = $this->permissions;
        return view('admin.plan.add',compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
          
          $input = $request->all();
          $msg = [];
          $rule = [
                'name' => 'required',
                'description' => 'required',
                'subscription_id' => 'required|unique:plans,plan_id',
                'price' => 'required',
                'status' => 'required',
                'permission' => 'required',
          ];
          $validator = \Validator::make($request->all(),$rule,$msg);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
          $plan = new Plan();
          $plan->name = $input['name'];
          $plan->description = $input['description'];
          $plan->plan_id = $input['subscription_id'];
          $plan->plan_type = 'monthly';
          $plan->price = $input['price'];
          $plan->status = $input['status'];
          $plan->permission = json_encode($input['permission']);
          $plan->save();
          return redirect('admin/subscription');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function show(Plan $plan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function edit(Plan $plan)
    {
        $permissions = $this->permissions;
        return view('admin.plan.edit',compact('plan','permissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Plan $plan)
    {
          $input = $request->all();
          $msg = [];
          $rule = [
                'name' => 'required',
                'description' => 'required',
                'subscription_id' => 'required|unique:plans,plan_id,' . $plan->id,
                'price' => 'required',
                'status' => 'required',
                'permission' => 'required',
          ];
          $validator = \Validator::make($request->all(),$rule,$msg);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
          $plan->name = $input['name'];
          $plan->description = $input['description'];
          $plan->plan_id = $input['subscription_id'];
          $plan->price = $input['price'];
          $plan->permission = json_encode($input['permission']);
          $plan->status = $input['status'];
          $plan->save();
          return redirect('admin/subscription');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Plan $plan)
    {
       if($plan->delete()){
            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'error']);
        }
    }
}
