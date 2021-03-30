<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Model\CustomModuleApp;
use Illuminate\Http\Request;
use PragmaRX\Countries\Package\Countries;
use Illuminate\Support\Str;
class CustomModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       die('hdh');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Countries::all()
        ->map(function ($country) {
            $commonName = $country->name->common;

            $languages = $country->languages ?? collect();

            $language = $languages->keys()->first() ?? null;

            $nativeNames = $country->name->native ?? null;

            if (
                filled($language) &&
                    filled($nativeNames) &&
                    filled($nativeNames[$language]) ?? null
            ) {
                $native = $nativeNames[$language]['common'] ?? null;
            }

            if (blank($native ?? null) && filled($nativeNames)) {
                $native = $nativeNames->first()['common'] ?? null;
            }

            $native = $native ?? $commonName;

            if ($native !== $commonName && filled($native)) {
                $native = "$native ($commonName)";
            }

            return ['code'=>$country->cca2 ,'name'=>$native];
        })
        ->sortBy('name')
        ->values()
        ->toArray();
        return view('superadmin.create_app')->with(['countries'=>$countries]);
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
            'app_name' => 'required',
            'domain_name' => 'required',
            'email'=>'required',
            'first_name'=>'required',
            'last_name'=>'required',
            'password'=>'required',
            'country'=>'required',
            'class_module'=>'required',
            'payment_gateway'=>'required',
            'in_app_calling'=>'required',
            'tele_calling'=>'required',
            'service_type'=>'required',
            'status'=>'required',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        return back()->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\CustomModuleApp  $customModuleApp
     * @return \Illuminate\Http\Response
     */
    public function show(CustomModuleApp $customModuleApp)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\CustomModuleApp  $customModuleApp
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomModuleApp $customModuleApp)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\CustomModuleApp  $customModuleApp
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomModuleApp $customModuleApp)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\CustomModuleApp  $customModuleApp
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomModuleApp $customModuleApp)
    {
        //
        die('hdhh');
    }

    public function checkDomain(Request $request){
        $domain = Str::slug($request->domain,'');
        $exist = CustomModuleApp::where('domain_name',$domain)->first();
        if($exist){
            return response()->json(['status'=>'error']);
        }else{
            return response()->json(['status'=>'success','domain'=>$domain]);
        }
    }
}
