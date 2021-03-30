<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\slot;

class SlotController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $slots = slot::orderBy('id','DESC')->get();
        return view('admin.slots.index')->with(array('slots'=>$slots));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.slots.add');
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
                'slot_value' => 'required|not_in:0'
          ]);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
          $input = $request->all();
          $slot = new slot();
          $slot->slot_value = $input['slot_value'];
          $slotvalue = slot::where('slot_value',$input['slot_value'])->first();
            if( !empty($slotvalue)){
                return redirect()->back()->with('alert', 'Slots Already Exist');
            }else{
                $slot->save();
                return redirect('admin/slots');
            }
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
        $slot = slot::where('id',$id)->first();
        return view('admin.slots.edit',compact('slot'));
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
                'slot_value' => 'required|not_in:0'
          ]);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
        $input = $request->all();
        $slot = slot::where('id',$id)->first();
        $slot->slot_value = $request->slot_value;
        $slot->save();
        return redirect('admin/slots');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      
       $slot = slot::findOrFail($id);
       $slot->delete();
       return redirect('admin/slots'); 
    }
}
