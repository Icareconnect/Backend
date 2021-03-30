<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\slot;
use App\Model\HealthCareVisit;

class HealthCareVisitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $slots = HealthCareVisit::orderBy('id','DESC')->get();
        return view('admin.HealthCareVisit.index')->with(array('slots'=>$slots));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.HealthCareVisit.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
          $validator = \Validator::make($request->all(), [
                'health_care_value' => 'required|string'
          ]);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
          $input = $request->all();
          $slot = new HealthCareVisit();
          $slot->health_care_value = $input['health_care_value'];
          $slot->save();
          return redirect('admin/healthCareVisit');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $slot = HealthCareVisit::where('id',$id)->first();
        return view('admin.HealthCareVisit.edit',compact('slot'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = \Validator::make($request->all(), [
                'health_care_value' => 'required|string'
          ]);
        if ($validator->fails()) {
          return back()->withErrors($validator)->withInput();
        }
        $input = $request->all();
        $slot = HealthCareVisit::where('id',$id)->first();
        $slot->health_care_value = $request->health_care_value;
        $slot->save();
        return redirect('admin/healthCareVisit');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      
       $slot = HealthCareVisit::findOrFail($id);
       $slot->delete();
       return redirect('admin/healthCareVisit'); 
    }
}
