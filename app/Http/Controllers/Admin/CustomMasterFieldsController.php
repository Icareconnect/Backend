<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\CustomMasterField;
use Illuminate\Http\Request;

class CustomMasterFieldsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customfields = CustomMasterField::where('module_type','medical_report')->orderBy('id','DESC')->get();
        return view('admin.medical_report.index',compact('customfields'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.medical_report.add');
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
                'field_name'      => 'required|unique:custom_master_fields,name',
                'field_type' => 'required',
          ]);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
          $input = $request->all();
          $CustomField = new CustomMasterField();
          $CustomField->name = $input['field_name'];
          $CustomField->type = $input['field_type'];
          $CustomField->module_type = 'medical_report';
          $CustomField->save();
          return redirect('admin/custom/masterfields');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\CustomMasterField  $customMasterField
     * @return \Illuminate\Http\Response
     */
    public function show(CustomMasterField $customMasterField)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\CustomMasterField  $customMasterField
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomMasterField $customMasterField)
    {
        return view('admin.medical_report.edit', compact('customMasterField'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\CustomMasterField  $customMasterField
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomMasterField $customMasterField)
    {
        $validator = \Validator::make($request->all(), [
                'field_name'      => 'required|unique:custom_master_fields,name,'.$customMasterField->id,
                'field_type' => 'required',
          ]);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
          $input = $request->all();
          $customMasterField->name = $input['field_name'];
          $customMasterField->type = $input['field_type'];
          $customMasterField->save();
          return redirect('admin/custom/masterfields');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\CustomMasterField  $customMasterField
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomMasterField $customMasterField)
    {
        if($customMasterField->delete()){
            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'error']);
        }
    }
}
