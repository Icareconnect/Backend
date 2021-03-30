<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\CustomField;
use App\Model\Role;
use Illuminate\Http\Request;

class VendorCustomFieldsController extends Controller
{
    public $role,$action_url;

    public function __construct() {
        $this->role = Role::where('name','service_provider')->first();
        $this->action_url = 'admin/vendor/custom-fields';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $text = __('text.Vendor').' Custom Field List';
        $action_url = $this->action_url;
        $customfields = CustomField::where('user_type',$this->role->id)->orderBy('id','DESC')->get();
        return view('admin.customfields.index',compact('customfields','text','action_url'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $role = $this->role;
        $action_url = $this->action_url;
        $text = 'Add '.__('text.Vendor').' Custom Field';
        return view('admin.customfields.add',compact('role','text','action_url'));
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
                'user_type' => 'required',
                'field_name'      => 'required|string',
                'field_type' => 'required',
          ]);
          $input = $request->all();
          // print_r($input);die;
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
          $CustomField = new CustomField();
          $CustomField->user_type = $input['user_type'];
          $CustomField->field_name = $input['field_name'];
          $CustomField->field_type = $input['field_type'];
          $CustomField->required_sign_up = isset($input['required_sign_up'])?'1':'0';
          $CustomField->save();
          return redirect($this->action_url);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\CustomField  $customField
     * @return \Illuminate\Http\Response
     */
    public function show(CustomField $customField)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\CustomField  $customField
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomField $customField)
    {
        $text = 'Edit '.__('text.Vendor').' Custom Field';
        $action_url = $this->action_url;
        return view('admin.customfields.update', compact('customField','text','action_url'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\CustomField  $customField
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomField $customField)
    {
        $validator = \Validator::make($request->all(), [
                'field_name'      => 'required|string',
                'field_type' => 'required',
          ]);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
          $input = $request->all();
          $customField->field_name = $input['field_name'];
          $customField->field_type = $input['field_type'];
          $customField->required_sign_up = isset($input['required_sign_up'])?'1':'0';
          $customField->save();
          return redirect($this->action_url);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\CustomField  $customField
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomField $customField)
    {
        //
    }
}
