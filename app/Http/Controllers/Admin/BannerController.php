<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Banner;
use Illuminate\Http\Request;
use App\Http\Traits\CategoriesTrait;
use DateTime,DateTimeZone;
class BannerController extends Controller
{
    use CategoriesTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $banners = Banner::orderBy('id','DESC')->get();
        return view('admin.banner.index')->with(array('banners'=>$banners));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = $this->parentCategories();
        $service_providers = $this->serviceProviders();
        $consultclasses = $this->consultClasses();
        return view('admin.banner.add',compact('categories','service_providers','consultclasses'));
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
                'date_range' => 'required',
                'banner_type' => 'required',
                'position' => 'required',
          ];
          if(isset($request->banner_type)){
            if($request->banner_type=='category'){
                $rule['category']='required';
            }elseif ($request->banner_type=='class') {
                $rule['class']='required';
            }elseif ($request->banner_type=='service_provider') {
                $rule['service_provider']='required';
            }
          }
         if(isset($request->image_web)){
             $rule['image_web']='required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=516';
             $msg['image_web.dimensions'] = "image should be min_width=516";
         }
         if(isset($request->image_mobile)){
             $rule['image_mobile']='required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=516';
              $msg['image_mobile.dimensions'] = "image should be min_width=516";
         }
         $validator = \Validator::make($request->all(),$rule,$msg);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
          $date_range = explode(' to ', $input['date_range']);
          $start_date =  date('Y-m-d', strtotime($date_range[0]));
          $end_date =  date('Y-m-d', strtotime($date_range[1]));
          // print_r($start_date);die;
          $banner = new Banner();
          $banner->image_web = null;
          $banner->image_mobile = null;
          $banner->start_date = $start_date;
          $banner->end_date = $end_date;
          $banner->position = $input['position'];
          $banner->category_id = $input['category'];
          $banner->sp_id = $input['service_provider'];
          $banner->class_id = $input['class'];
          $banner->banner_type = $input['banner_type'];
          if($request->hasfile('image_web')) {
            if ($image = $request->file('image_web')) {
                $extension = $image->getClientOriginalExtension();
                $filename = str_replace(' ','', md5(time()).'_'.$image->getClientOriginalName());
                $thumb = \Image::make($image)->resize(100, 100,
                  function ($constraint) {
                      $constraint->aspectRatio();
                  })->encode($extension);
                $normal = \Image::make($image)->resize(688, 416,
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
                $normal = \Image::make($image)->resize(688, 416,
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
                \Storage::disk('spaces')->put('original/'.$filename, (string)$big, 'public');
                $banner->image_mobile = $filename;
            }
          }
          $banner->save();
          return redirect('admin/banner');


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Banner  $banner
     * @return \Illuminate\Http\Response
     */
    public function show(Banner $banner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\Banner  $banner
     * @return \Illuminate\Http\Response
     */
    public function edit(Banner $banner)
    {
        $categories = $this->parentCategories();
        $service_providers = $this->serviceProviders();
        $consultclasses = $this->consultClasses();
        $start_date =  date('Y-m-d', strtotime($banner->start_date));
        $end_date =  date('Y-m-d', strtotime($banner->end_date));
        $banner->date_range = $start_date.' to '.$end_date;
        $created_by = \App\User::where('id',$banner->created_by)->first();
        if($created_by){
          $banner->created_name = $created_by->name;
        }else{
          $banner->created_name = 'Admin';
        }
        return view('admin.banner.edit',compact('categories','service_providers','consultclasses','banner'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\Banner  $banner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Banner $banner)
    {
        $input = $request->all();
        $msg = [];
          $rule = [
                'date_range' => 'required',
                'banner_type' => 'required',
                'position' => 'required',
          ];
          if(isset($request->banner_type)){
            if($request->banner_type=='category'){
                $rule['category']='required';
            }elseif ($request->banner_type=='class') {
                $rule['class']='required';
            }elseif ($request->banner_type=='service_provider') {
                $rule['service_provider']='required';
            }
          }
         if($request->hasfile('image_web')) {
            $rule['image_web']='required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=516';
            $msg['image_web.dimensions'] = "image should be min_width=516";
         }
         if($request->hasfile('image_mobile')) {
            $rule['image_mobile']='required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=516';
            $msg['image_mobile.dimensions'] = "image should be min_width=516";
         }
         $validator = \Validator::make($request->all(),$rule,$msg);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
          $date_range = explode(' to ', $input['date_range']);
          $start_date =  date('Y-m-d', strtotime($date_range[0]));
          $end_date =  date('Y-m-d', strtotime($date_range[1]));
          $banner->start_date = $start_date;
          $banner->end_date = $end_date;
          $banner->position = $input['position'];
          $banner->category_id = $input['category'];
          $banner->sp_id = $input['service_provider'];
          $banner->class_id = $input['class'];
          $banner->banner_type = $input['banner_type'];
          $banner->enable = $input['enable'];
          if($request->hasfile('image_web')) {
            if ($image = $request->file('image_web')) {
                $extension = $image->getClientOriginalExtension();
                $filename = str_replace(' ','', md5(time()).'_'.$image->getClientOriginalName());
                $thumb = \Image::make($image)->resize(100, 100,
                  function ($constraint) {
                      $constraint->aspectRatio();
                  })->encode($extension);
                $normal = \Image::make($image)->resize(688, 416,
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
                $normal = \Image::make($image)->resize(688, 416,
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
                \Storage::disk('spaces')->put('original/'.$filename, (string)$big, 'public');
                $banner->image_mobile = $filename;
            }
          }
          $banner->save();
          return redirect('admin/banner');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Banner  $banner
     * @return \Illuminate\Http\Response
     */
    public function destroy(Banner $banner)
    {
       if($banner->delete()){
            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'error']);
        }
    }
}
