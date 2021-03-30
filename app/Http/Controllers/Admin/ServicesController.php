<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\EnableService;
use App\Model\Currency;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $services = EnableService::get();
        return view('admin.services')->with(array('services'=>$services));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * @param  \App\Model\EnableService  $enableService
     * @return \Illuminate\Http\Response
     */
    public function show($id,EnableService $enableService)
    {
        return view('admin.service_update')->with(array('enableservice'=>$enableService));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\EnableService  $enableService
     * @return \Illuminate\Http\Response
     */
    public function edit($id,EnableService $enableService)
    {
        $enableService = EnableService::where('id',$id)->first();
        $currecnies = Currency::select('code','currency','symbol')->get();
        return view('admin.service_update')->with(array('enableservice'=>$enableService,'currecnies'=>$currecnies));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\EnableService  $enableService
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EnableService $enableService)
    {
        $client = new \Predis\Client();
        $client->flushAll();
        $enableService = EnableService::where('id',$request->service_id)->first();
        if($enableService->type=='unit_price' && \Config('client_connected') && \Config::get("client_data")->domain_name=="intely"){
            $enableService->value = $request->value*60;
        }else{
            $enableService->value = $request->value;
        }
        $enableService->save();
        return redirect('admin/service_enable');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\EnableService  $enableService
     * @return \Illuminate\Http\Response
     */
    public function destroy(EnableService $enableService)
    {
        //
    }
}
