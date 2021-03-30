<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Covid19;
use Illuminate\Http\Request;

class MarketingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $covid19s = Covid19::orderBy('id','DESC')->get();
        return view('admin.covid19.index')->with(array('covid19s'=>$covid19s));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.covid19.add');
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
                'type' => 'required',
                'title' => 'required',
          ];
          if(isset($request->type)){
            if($request->type=='banner'){
                 if(isset($request->image_web)){
                     $rule['image_web']='required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=344:min_height=112';
                     $msg['image_web.dimensions'] = "image_web dimensions should be minimum 344*112";
                 }
                 if(isset($request->image_mobile)){
                     $rule['image_mobile']='required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=344:min_height=112';
                      $msg['image_mobile.dimensions'] = "image_mobile dimensions should be minimum 344*112";
                 }
            }else{
                if(isset($request->image_web)){
                     $rule['image_web']='required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=60:min_height=60';
                     $msg['image_web.dimensions'] = "image_web dimensions should be minimum 60*60";
                 }
                 if(isset($request->image_mobile)){
                     $rule['image_mobile']='required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=60:min_height=60';
                      $msg['image_mobile.dimensions'] = "image_mobile dimensions should be minimum 60*60";
                 }
            }
          }
         $validator = \Validator::make($request->all(),$rule,$msg);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
          $banner = new Covid19();
          $banner->image_web = null;
          $banner->image_mobile = null;
          $banner->type = $input['type'];
          $banner->title = $input['title'];
          $banner->on_click_info = (isset($input['on_click_info']))?$input['on_click_info']:null;
          $banner->description = isset($input['description'])?$input['description']:null;
          $banner->home_screen = isset($input['home_screen'])?1:0;
          $banner->enable = isset($input['enable'])?1:0;
          if($request->hasfile('image_web')) {
            if ($image = $request->file('image_web')) {
                $extension = $image->getClientOriginalExtension();
                $filename = str_replace(' ','', md5(time()).'_'.$image->getClientOriginalName());
                $thumb = \Image::make($image)->resize(100, 100,
                  function ($constraint) {
                      $constraint->aspectRatio();
                  })->encode($extension);
                if($request->type=='banner'){
                    $normal = \Image::make($image)->resize(600, 400,
                      function ($constraint) {
                          $constraint->aspectRatio();
                      })->encode($extension);
                }else{
                    $normal = \Image::make($image)->resize(260, 260,
                      function ($constraint) {
                          $constraint->aspectRatio();
                      })->encode($extension);
                }
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
                \Storage::disk('spaces')->put('original/'.$filename, (string)$big, 'public');
                $banner->image_web = $filename;
            }
          }
          if($request->hasfile('image_mobile')) {
            if ($image = $request->file('image_mobile')) {
                $extension = $image->getClientOriginalExtension();
                $filename = str_replace(' ','', md5(time()).'_'.$image->getClientOriginalName());
                $thumb = \Image::make($image)->resize(100, 100,
                  function ($constraint) {
                      $constraint->aspectRatio();
                  })->encode($extension);
                if($request->type=='banner'){
                    $normal = \Image::make($image)->resize(600, 400,
                      function ($constraint) {
                          $constraint->aspectRatio();
                      })->encode($extension);
                }else{
                    $normal = \Image::make($image)->resize(260, 260,
                      function ($constraint) {
                          $constraint->aspectRatio();
                      })->encode($extension);
                }
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
                \Storage::disk('spaces')->put('original/'.$filename, (string)$big, 'public');
                $banner->image_mobile = $filename;
            }
          }
          $banner->save();
          return redirect('admin/covid19');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Covid19  $covid19
     * @return \Illuminate\Http\Response
     */
    public function show(Covid19 $covid19)
    {
        return view('admin.covid19.edit',compact('covid19'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\Covid19  $covid19
     * @return \Illuminate\Http\Response
     */
    public function edit(Covid19 $covid19)
    {
        return view('admin.covid19.edit',compact('covid19'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\Covid19  $covid19
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Covid19 $covid19)
    {
        $input = $request->all();
        $msg = [];
          $rule = [
                'type' => 'required',
                'title' => 'required',
          ];
          if(isset($request->type)){
            if($request->type=='banner'){
                 if($request->hasfile('image_web')){
                     $rule['image_web']='required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=344:min_height=112';
                     $msg['image_web.dimensions'] = "image_web dimensions should be minimum 344*112";
                 }
                 if($request->hasfile('image_mobile')){
                     $rule['image_mobile']='required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=344:min_height=112';
                      $msg['image_mobile.dimensions'] = "image_mobile dimensions should be minimum 344*112";
                 }
            }else{
                if($request->hasfile('image_web')){
                     $rule['image_web']='required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=60:min_height=60';
                     $msg['image_web.dimensions'] = "image_web dimensions should be minimum 60*60";
                 }
                 if($request->hasfile('image_mobile')){
                     $rule['image_mobile']='required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=60:min_height=60';
                      $msg['image_mobile.dimensions'] = "image_mobile dimensions should be minimum 60*60";
                 }
            }
          }
         $validator = \Validator::make($request->all(),$rule,$msg);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
          $covid19->type = $input['type'];
          $covid19->title = $input['title'];
          $covid19->on_click_info = isset($input['on_click_info'])?$input['on_click_info']:$covid19->on_click_info;
          $covid19->description = isset($input['description'])?$input['description']:$covid19->description;
          $covid19->home_screen = isset($input['home_screen'])?1:0;
          $covid19->enable = isset($input['enable'])?1:0;
          if($request->hasfile('image_web')) {
            if ($image = $request->file('image_web')) {
                $extension = $image->getClientOriginalExtension();
                $filename = str_replace(' ','', md5(time()).'_'.$image->getClientOriginalName());
                $thumb = \Image::make($image)->resize(100, 100,
                  function ($constraint) {
                      $constraint->aspectRatio();
                  })->encode($extension);
                if($request->type=='banner'){
                    $normal = \Image::make($image)->resize(600, 400,
                      function ($constraint) {
                          $constraint->aspectRatio();
                      })->encode($extension);
                }else{
                    $normal = \Image::make($image)->resize(260, 260,
                      function ($constraint) {
                          $constraint->aspectRatio();
                      })->encode($extension);
                }
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
                \Storage::disk('spaces')->put('original/'.$filename, (string)$big, 'public');
                $covid19->image_web = $filename;
            }
          }
          if($request->hasfile('image_mobile')) {
            if ($image = $request->file('image_mobile')) {
                $extension = $image->getClientOriginalExtension();
                $filename = str_replace(' ','', md5(time()).'_'.$image->getClientOriginalName());
                $thumb = \Image::make($image)->resize(100, 100,
                  function ($constraint) {
                      $constraint->aspectRatio();
                  })->encode($extension);
                if($request->type=='banner'){
                    $normal = \Image::make($image)->resize(600, 400,
                      function ($constraint) {
                          $constraint->aspectRatio();
                      })->encode($extension);
                }else{
                    $normal = \Image::make($image)->resize(260, 260,
                      function ($constraint) {
                          $constraint->aspectRatio();
                      })->encode($extension);
                }
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
                \Storage::disk('spaces')->put('original/'.$filename, (string)$big, 'public');
                $covid19->image_mobile = $filename;
            }
          }
          $covid19->save();
          return redirect('admin/covid19');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Covid19  $covid19
     * @return \Illuminate\Http\Response
     */
    public function destroy(Covid19 $covid19)
    {
        if($covid19->delete()){
            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'error']);
        }
    }
}
