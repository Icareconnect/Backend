<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Package;
use Illuminate\Http\Request;
use App\Http\Traits\CategoriesTrait;
use Auth;
class PackageController extends Controller
{

     use CategoriesTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $packages = Package::orderBy('id','DESC')->
         where(function($query){
                $query->whereHas('category', function($q){
                  return $q->where('enable', 1);
              })->where('package_type','category');
          })
         ->orWhere('package_type','open')
         ->where('created_by',null)->get();
        return view('admin.package.index',compact('packages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = $this->getAllCategories();
        return view('admin.package.add',compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $msg = [];
        $rules = [
                'title' => 'required',
                'description' => 'required|string',
                'price'      => 'required|integer|min:1',
                'image' => 'required',
                'total_requests' => 'required|integer|min:1',
                'package_type' => 'required'
          ];
          $input = $request->all();
          $category_id = null;
          $filter_id = null;
          if(isset($request->image)){
             $rules['image']='required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=480,min_height=400';
             $msg['image.dimensions'] = "image should be min_width=480,min_height=400";
         }
          $validator = \Validator::make($request->all(),$rules,$msg);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
          $category_id = null;
          $enable = '0';
          if(isset($input['category']) && $input['category']){
            $category = explode('_', $input['category']);
            if(count($category)>1){
                $category_id = $category[3];
                $filter_id = $category[1];
            }else{
              $category_id = $category[0];
            }
          }
          if(isset($input['enable']) && $input['enable']){
            $enable = $input['enable'];
          }
          $package = new Package();
          if($request->hasfile('image')) {
            if ($image = $request->file('image')) {
                $extension = $image->getClientOriginalExtension();
                $filename = str_replace(' ','', md5(time()).'_'.$image->getClientOriginalName());
                $thumb = \Image::make($image)->resize(100, 100,
                  function ($constraint) {
                      $constraint->aspectRatio();
                  })->encode($extension);
                $normal = \Image::make($image)->resize(400, 480,
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
                $package->image = $filename;
            }
          }
          $package->title = $input['title'];
          $package->category_id = $category_id;
          $package->filter_id = $filter_id;
          $package->description = $input['description'];
          $package->price = $input['price'];
          $package->total_requests = $input['total_requests'];
          $package->package_type = $input['package_type'];
          $package->enable = $enable;
          $package->save();
          return redirect('admin/package');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Package  $package
     * @return \Illuminate\Http\Response
     */
    public function show(Package $package)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\Package  $package
     * @return \Illuminate\Http\Response
     */
    public function edit(Package $package)
    {
        $categories = $this->getAllCategories();
        return view('admin.package.edit',compact('categories','package'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\Package  $package
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Package $package)
    {
         $msg = [];
         $rules = [
                'title' => 'required',
                'description' => 'required|string',
                'price'      => 'required|integer|min:1',
                'total_requests' => 'required|integer|min:1',
                'enable' => 'required',
          ];
          $input = $request->all();
          if($package->package_type=='category'){
            $rules['category'] = "required";
          }
          if($request->hasfile('image')) {
             $rules['image']='required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=480,min_height=400';
             $msg['image.dimensions'] = "image should be min_width=480,min_height=400";
          }
          $validator = \Validator::make($request->all(),$rules,$msg);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
          $category_id = null;
          $filter_id = null;
          if(isset($input['category']) && $input['category']){
            $category = explode('_', $input['category']);
            if(count($category)>1){
                $category_id = $category[3];
                $filter_id = $category[1];
            }else{
              $category_id = $category[0];
            }
          }
          $enable = $input['enable'];
          if($request->hasfile('image')) {
            if ($image = $request->file('image')) {
                $extension = $image->getClientOriginalExtension();
                $filename = str_replace(' ','', md5(time()).'_'.$image->getClientOriginalName());
                $thumb = \Image::make($image)->resize(100, 100,
                  function ($constraint) {
                      $constraint->aspectRatio();
                  })->encode($extension);
                $normal = \Image::make($image)->resize(400, 400,
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
                $package->image = $filename;
            }
          }
          $package->title = $input['title'];
          $package->category_id = $category_id;
          $package->filter_id = $filter_id;
          $package->description = $input['description'];
          $package->price = $input['price'];
          $package->total_requests = $input['total_requests'];
          $package->enable = $enable;
          $package->save();
          return redirect('admin/package');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Package  $package
     * @return \Illuminate\Http\Response
     */
    public function destroy(Package $package)
    {
        //
    }
}
