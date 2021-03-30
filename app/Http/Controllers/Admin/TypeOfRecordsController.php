<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\slot;
use App\Model\TypeOfRecords;

class TypeOfRecordsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $slots = TypeOfRecords::orderBy('id','DESC')->get();
        return view('admin.TypeOfRecords.index')->with(array('slots'=>$slots));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.TypeOfRecords.add');
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
                'records_value' => 'required|string'
          ]);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
          $input = $request->all();
          $slot = new TypeOfRecords();
          $slot->records_value = $input['records_value'];
          $slot->save();
          return redirect('admin/typeOfRecord');
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
        $slot = TypeOfRecords::where('id',$id)->first();
        return view('admin.TypeOfRecords.edit',compact('slot'));
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
                'records_value' => 'required|string'
          ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $input = $request->all();
        $slot = TypeOfRecords::where('id',$id)->first();
        $slot->records_value = $request->records_value;
        $slot->save();
        return redirect('admin/typeOfRecord');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      
       $slot = TypeOfRecords::findOrFail($id);
       $slot->delete();
       return redirect('admin/typeOfRecord'); 
    }
}
