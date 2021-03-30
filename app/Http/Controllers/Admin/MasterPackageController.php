<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\MasterPackage;
use Illuminate\Http\Request;

class MasterPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $packages = MasterPackage::where('type','support_package')->orderBy('id','desc')->get();
        return view('admin.support_package.index', compact('packages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.support_package.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      
        $rule = [
            'title'      => 'required|string',
            'description' => 'required',
            'color_code' => 'required',
            'price' => 'required',
            'image_icon' => 'required',
        ];
        $msg = [];
       if($request->hasfile('image_icon')) {
          $rule['image_icon']='required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=50,min_height=50';
          $msg['image_icon.dimensions'] = "image should be min_width=50,min_height=50";
         }
        $validator = \Validator::make($request->all(),$rule,$msg);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $input = $request->all();
        $cat = new MasterPackage();
        $cat->title = $input['title'];
        if(isset($input['price']))
            $cat->price = $input['price'];
        else
            $cat->price = 0;

        if($request->hasfile('image_icon')) {
          if ($image = $request->file('image_icon')) {
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
              $cat->image_icon = $filename;
          }
        }
        $cat->color_code = str_replace('#','',$input['color_code']);
        if(isset($input['description'])){
            $cat->description = $input['description'];
        }
        $cat->type = 'support_package';
        $cat->created_by = \Auth::user()->id;
        $cat->save();
        return redirect()->route('support_packages.index')->withSuccess('You have successfully created a Package!'); 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\MasterPackage  $masterPackage
     * @return \Illuminate\Http\Response
     */
    public function show(MasterPackage $masterPackage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\MasterPackage  $masterPackage
     * @return \Illuminate\Http\Response
     */
    public function edit(MasterPackage $masterpackage)
    {
        // $masterpackage->color_code = str_replace('#','',$masterpackage->color_code);
        // print_r($masterpackage);die;
       return view('admin.support_package.edit', compact('masterpackage'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\MasterPackage  $masterPackage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MasterPackage $masterpackage)
    {
        $rule = [
            'title'      => 'required|string',
            'description' => 'required',
            'color_code' => 'required',
            'price' => 'required',
        ];
      $msg = [];
      if($request->hasfile('image_icon')) {
          $rule['image_icon']='required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=50,min_height=50';
          $msg['image_icon.dimensions'] = "image should be min_width=50,min_height=50";
      }
      $validator = \Validator::make($request->all(),$rule,$msg);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
      }
      if($request->hasfile('image_icon')) {
        if ($image = $request->file('image_icon')) {
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
                \Storage::disk('spaces')->put('original/'.$filename, (string)$big, 'public'); 
            $masterpackage->image_icon = $filename;
        }
      }
      $input = $request->all();
      $masterpackage->title = $input['title'];
      $masterpackage->price = $input['price'];
      $masterpackage->color_code = str_replace('#','',$input['color_code']);
      if(isset($input['description'])){
        $masterpackage->description = $input['description'];
      }
     $masterpackage->save();
      return redirect()->back()->with('message', 'You have successfully updated a Package!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\MasterPackage  $masterPackage
     * @return \Illuminate\Http\Response
     */
    public function destroy(MasterPackage $masterPackage)
    {
        //
    }
}
