<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\AppDetail;
use Illuminate\Http\Request;

class AppDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $AppDetails = AppDetail::select('*','user_side_logo as app_logo')->orderBy('id','DESC')->get();
        return view('admin.app_detail.index',compact('AppDetails'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.app_detail.add');
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
        $rules = [
            'app_logo' => 'required',
            'background_color' => 'required'
        ];
        $validator = \Validator::make($request->all(),$rules);
        if($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $appDetail = new AppDetail();
        if($request->hasfile('app_logo')) {
          if ($image = $request->file('app_logo')) {
              $extension = $image->getClientOriginalExtension();
                $filename = str_replace(' ','', md5(time()).'_'.$image->getClientOriginalName());
                $thumb = \Image::make($image)->resize(100, 100,
                  function ($constraint) {
                      $constraint->aspectRatio();
                  })->encode($extension);
               $normal = \Image::make($image)->resize(260, 260,
                  function ($constraint) {
                      $constraint->aspectRatio();
                  })->encode($extension);
                $big = \Image::make($image)->encode($extension);
                $_800x800 = \Image::make($image)->resize(800, 800,
                  function ($constraint) {
                      $constraint->aspectRatio();
                  })->encode($extension);
                $_400x400 = \Image::make($image)->resize(400, 400,
                  function ($constraint) {
                      $constraint->aspectRatio();
                  })->encode($extension);
                \Storage::disk('spaces')->put('thumbs/'.$filename, (string)$thumb, 'public');
                \Storage::disk('spaces')->put('uploads/'.$filename, (string)$normal, 'public');
                \Storage::disk('spaces')->put('original/'.$filename, (string)$big, 'public');
                \Storage::disk('spaces')->put('800x800/'.$filename, (string)$_800x800, 'public');
                \Storage::disk('spaces')->put('400x400/'.$filename, (string)$_400x400, 'public');
              $appDetail->user_side_logo = $filename;
          }
        }

        $appDetail->background_color = str_replace('#','',$input['background_color']);
        $appDetail->save();
        return redirect('admin/app_detail');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\AppDetail  $appDetail
     * @return \Illuminate\Http\Response
     */
    public function show(AppDetail $appDetail)
    {
        return view('admin.app_detail.view',compact('appDetail'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\AppDetail  $appDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(AppDetail $appDetail)
    {
        $appDetail->app_logo = $appDetail->user_side_logo;
        return view('admin.app_detail.edit',compact('appDetail'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\AppDetail  $appDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AppDetail $appDetail)
    {
        $input = $request->all();
        $rules = [
            'background_color' => 'required'
        ];
        $validator = \Validator::make($request->all(),$rules);
        if($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        if($request->hasfile('app_logo')) {
          if ($image = $request->file('app_logo')) {
              $extension = $image->getClientOriginalExtension();
                $filename = str_replace(' ','', md5(time()).'_'.$image->getClientOriginalName());
                $thumb = \Image::make($image)->resize(100, 100,
                  function ($constraint) {
                      $constraint->aspectRatio();
                  })->encode($extension);
               $normal = \Image::make($image)->resize(260, 260,
                  function ($constraint) {
                      $constraint->aspectRatio();
                  })->encode($extension);
                $big = \Image::make($image)->encode($extension);
                $_800x800 = \Image::make($image)->resize(800, 800,
                  function ($constraint) {
                      $constraint->aspectRatio();
                  })->encode($extension);
                $_400x400 = \Image::make($image)->resize(400, 400,
                  function ($constraint) {
                      $constraint->aspectRatio();
                  })->encode($extension);
                \Storage::disk('spaces')->put('thumbs/'.$filename, (string)$thumb, 'public');
                \Storage::disk('spaces')->put('uploads/'.$filename, (string)$normal, 'public');
                \Storage::disk('spaces')->put('original/'.$filename, (string)$big, 'public');
                \Storage::disk('spaces')->put('800x800/'.$filename, (string)$_800x800, 'public');
                \Storage::disk('spaces')->put('400x400/'.$filename, (string)$_400x400, 'public');
              $appDetail->user_side_logo = $filename;
          }
        }
        $appDetail->background_color = str_replace('#','',$input['background_color']);
        $appDetail->save();
        return redirect('admin/app_detail');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\AppDetail  $appDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(AppDetail $appDetail)
    {
        //
    }
}
