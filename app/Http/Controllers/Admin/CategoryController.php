<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Category;
use App\Model\Service;
use App\Model\CategoryServiceType;
use Illuminate\Http\Request;
use Config;
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $parentCategories = Category::where('parent_id',NULL)->orderBy('id','desc')->get();
        return view('admin.categories.index', compact('parentCategories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.categories.add');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addSubCategory(Category $category)
    {
        return view('admin.categories.add',compact('category'));
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
        $rule = [
            'name'      => 'required|min:3|max:255|string',
            'parent_id' => 'sometimes|nullable|numeric',
            'color_code' => 'required',
      ];
      $sessionatcentre = false;
      if(Config('client_connected') && Config::get("client_data")->domain_name=="physiotherapist" && isset($input['parent_id']) && $input['parent_id']==2){
        $sessionatcentre = true;
        $rule['email'] = 'required|email|unique:users,email';
      }
      $msg = [];
      if($request->hasfile('category_image')) {
          $rule['category_image']='required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=50,min_height=50';
          $msg['category_image.dimensions'] = "image should be min_width=50,min_height=50";
       }
       if($request->hasfile('image_icon')) {
          $rule['image_icon']='required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=50,min_height=50';
          $msg['image_icon.dimensions'] = "image should be min_width=50,min_height=50";
       }
      $validator = \Validator::make($request->all(),$rule,$msg);
      if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
      }
      $image_name = null;
      $cat = new Category();
      if($request->hasfile('category_image')) {
            if ($image = $request->file('category_image')) {
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
              $cat->image = $filename;
            }
        }
      if(isset($input['parent_id'])){
        $cat->parent_id = $input['parent_id'];
      }
      if(isset($input['name'])){
        $cat->name = $input['name'];
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
              $cat->image_icon = $filename;
          }
      }
      if(isset($input['color_code'])){
        $cat->color_code = str_replace('#','',$input['color_code']);
      }
      if(isset($input['description'])){
        $cat->description = $input['description'];
      }
      if(isset($input['enable_service_type'])){
        $cat->enable_service_type = $input['enable_service_type'];
      }
      $cat->enable = '1';
      if(isset($input['enable'])){
        $cat->enable = $input['enable'];
      }
      $cat->save();
      if(Config('client_connected') && Config::get("client_data")->domain_name=="iedu"){
          $categoryservicetype = CategoryServiceType::createServiceByCategory(1,$cat->id,null,1,100000);
      }
      if($sessionatcentre){
          $service_id = Service::getServiceIdByMainType('clinic_visit');
          $categoryservicetype = CategoryServiceType::createServiceByCategory($service_id,$cat->id,$input['price'],null,null);
          $row = [
            'category_id'=>$cat->id,
            'address'=>$cat->description,
            'name'=>$cat->name,
            'email'=>$input['email'],
            'lat'=>$input['lat'],
            'long'=>$input['long'],
            'image'=>$cat->image_icon,
            'cat_service_type'=>$categoryservicetype->id,
            'service_id'=>$service_id,
          ];
          \App\User::createSessionUser($row);
      }
      if($cat->parent_id){
        return redirect()->route('categories.edit',$cat->parent_id)->withSuccess('You have successfully created a Category!');
      }else{
        return redirect()->route('categories.index')->withSuccess('You have successfully created a Category!');
      }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        // print_r($category);die;
        $category->user = \App\User::getSessionUser($category->id);
        $category->price = CategoryServiceType::getSessionPrice($category->id);
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
      $input = $request->all();
        $rule = [
            'name'      => 'required|min:3|max:255|string',
            'color_code' => 'required',
      ];
      $sessionatcentre = false;
      if(Config('client_connected') && Config::get("client_data")->domain_name=="physiotherapist" && isset($category->parent_id) && $category->parent_id==2){
        $user = \App\User::getSessionUser($category->id);
        $sessionatcentre = true;
        if($user)
          $rule['email'] = 'email|unique:users,email,' . $user->id;
        else
          $rule['email'] = 'required|email|unique:users,email';
        $rule['price'] = 'required';
      }
      $msg = [];
      if($request->hasfile('image')) {
          $rule['image']='required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=50,min_height=50';
          $msg['image.dimensions'] = "image should be min_width=50,min_height=50";
       }
       if($request->hasfile('image_icon')) {
          $rule['image_icon']='required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=50,min_height=50';
          $msg['image_icon.dimensions'] = "image should be min_width=50,min_height=50";
       }
      $validator = \Validator::make($request->all(),$rule,$msg);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
      if($request->hasfile('image')) {
        if ($image = $request->file('image')) {
            $extension = $image->getClientOriginalExtension();
                $filename = str_replace(' ','', md5(time()).'_'.$image->getClientOriginalName());
                $thumb = \Image::make($image)->resize(100, 100,
                  function ($constraint) {
                      $constraint->aspectRatio();
                  })->encode($extension);
                $normal1 = \Image::make($image)->resize(260, 260,
                  function ($constraint1) {
                      $constraint1->aspectRatio();
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
                \Storage::disk('spaces')->put('uploads/'.$filename, (string)$normal1, 'public');
                \Storage::disk('spaces')->put('original/'.$filename, (string)$big, 'public');
                \Storage::disk('spaces')->put('800x800/'.$filename, (string)$_800x800, 'public');
                \Storage::disk('spaces')->put('400x400/'.$filename, (string)$_400x400, 'public');
                \Storage::disk('spaces')->put('original/'.$filename, (string)$big, 'public');  
            $category->image = $filename;
        }
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
            $category->image_icon = $filename;
        }
      }


      if($request->hasfile('banner')) {
        if ($image = $request->file('banner')) {
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
            $category->banner = $filename;
        }
      }


      $input = $request->all();
      if(isset($input['name'])){
        $category->name = $input['name'];
      }

      if(isset($input['description_text'])){
        $category->description_text = $input['description_text'];
      }
      if($request->hasfile('video')) {
          if ($image = $request->file('video')) {
          $extension = $image->getClientOriginalExtension();
          $filename = str_replace(' ','', md5(time()).'_'.$image->getClientOriginalName());
           $FileEnconded=  \File::get($request->video);
            \Storage::disk('spaces')->put('video/'.$filename, (string)$FileEnconded,'public');
            $category->video = $filename;
        }
      }
      if(isset($input['color_code'])){
        $category->color_code = str_replace('#','',$input['color_code']);
      }
      if(isset($input['description'])){
        $category->description = $input['description'];
      }
      if(isset($input['enable_service_type'])){
        $category->enable_service_type = $input['enable_service_type'];
      }
      if(isset($input['enable'])){
        $category->enable = $input['enable'];
      }
      $category->save();
      if(Config('client_connected') && Config::get("client_data")->domain_name=="iedu"){
          $categoryservicetype = CategoryServiceType::createServiceByCategory(1,$category->id,null,1,100000);
      }
      if($sessionatcentre){
          $service_id = Service::getServiceIdByMainType('clinic_visit');
          $categoryservicetype = CategoryServiceType::createServiceByCategory($service_id,$category->id,$input['price'],null,null);
          $row = [
            'category_id'=>$category->id,
            'address'=>$category->description,
            'name'=>$category->name,
            'email'=>$input['email'],
            'lat'=>$input['lat'],
            'long'=>$input['long'],
            'image'=>$category->image_icon,
            'cat_service_type'=>$categoryservicetype->id,
            'service_id'=>$service_id,
            'user'=>$user,
            'update'=>true,
          ];
          // dd($row);die;
          \App\User::createSessionUser($row);
      }
      return redirect()->back()->withSuccess('You have successfully updated a Category!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        //
    }
}
