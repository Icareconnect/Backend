<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\FilterType;
use App\Model\FilterTypeOption;
use App\Model\Category;
use App\Model\ServiceProviderFilterOption;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Validation\Rule;
class FilterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Category $category)
    {
         $filtertypes = FilterType::where('category_id',$category->id)->orderBy('id','DESC')->get();
        return view('admin.filter.index', compact('parentCategories','filtertypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Category $category)
    {
        return view('admin.filter.add', compact('category'));
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'preference_name' => Str::slug($this->slug,'_'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Category $category)
    {


        // print_r($request->all());die;
          // $request->merge(['preference_name' => Str::slug($request->preference_name,'_')]);
          // $request->merge(['filter_name' => Str::slug($request->filter_name,'_')]);
          $validator = \Validator::make($request->all(), [
                'filter_name' => 'required',
                // .Rule::unique('filter_types')->where(function ($query) use($category) {
                //     return $query->where(['category_id'=>$category->id]);
                // }),
                'preference_name' => 'required',
                // .Rule::unique('filter_types')->where(function ($query) use($category) {
                //     return $query->where(['category_id'=>$category->id]);
                // }),
                'category' => 'required',
                'filter_option' => 'required',
                'multiselect' => 'required',
          ]);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
          $input = $request->all();
          $filtertype = new FilterType();
          $filtertype->category_id = $input['category'];
          /*$filtertype->filter_name = Str::slug($input['filter_name'],'_');
          $filtertype->preference_name = Str::slug($input['preference_name'],'_');*/
          $filtertype->filter_name = $input['filter_name'];
          $filtertype->preference_name = $input['preference_name'];
          $filtertype->is_multi = $input['multiselect'];
          if($filtertype->save()){
            $filter_options = $input['filter_option']['name'];
            foreach ($filter_options as $key => $filter_option) {
                $filtertypeoption = FilterTypeOption::firstOrcreate(array(
                    'filter_type_id'=>$filtertype->id,
                    'option_name'=>$filter_option,
                    ));
                $filename = null;
                if(isset($input['filter_option']['image'][$key])){
                    if ($image = $request->file('filter_option')['image'][$key]){
                        $filename = $this->uploadImage($image);
                    }
                }
                if(isset($input['filter_option']['description'][$key])){
                    $filtertypeoption->description = $input['filter_option']['description'][$key];
                }
                if(isset($input['filter_option']['price'][$key])){
                    $filtertypeoption->price = $input['filter_option']['price'][$key];
                }
                $filtertypeoption->image = $filename;
                $filtertypeoption->save();
            }
          }
          return redirect('admin/categories/'.$category->id.'/edit');
    }

    private function uploadImage($image){
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
           return $filename;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\FilterType  $filterType
     * @return \Illuminate\Http\Response
     */
    public function show(FilterType $filterType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\FilterType  $filterType
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category,FilterType $filterType)
    {
        if(!$filterType){
            abort(404);
        }
        $parentCategories = Category::where('parent_id',NULL)->where('enable','=','1')->get();
        $filterType->filter_option = FilterTypeOption::where('filter_type_id',$filterType->id)->get();
        $filterType->category_name = $filterType->category->name;
        return view('admin.filter.edit', compact('parentCategories','filterType','category'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\FilterType  $filterType
     * @return \Illuminate\Http\Response
     */
    public function addsFilterOption($option_id)
    {
        // print_r($option_id);die;
        $filter_option = FilterTypeOption::where('id',$option_id)->first();
        if(!$filter_option){
          abort(404);
        }
        $FilterType = FilterType::where('id',$filter_option->filter_type_id)->first();
        $filter_option->category_id = $FilterType->category_id;
        return view('admin.filter.filter_option.edit', compact('filter_option'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\FilterType  $filterType
     * @return \Illuminate\Http\Response
     */
    public function postAddsFilterOption(Request $request,$option_id)
    {
        $filter_option = FilterTypeOption::where('id',$option_id)->first();
        if(!$filter_option){
          abort(404);
        }
        $input = $request->all();
      if(isset($input['name'])){
        $category->name = $input['name'];
      }

      if(isset($input['description'])){
        $filter_option->description = $input['description'];
      }
      if($request->hasfile('video')) {
          if ($image = $request->file('video')) {
          $extension = $image->getClientOriginalExtension();
          $filename = str_replace(' ','', md5(time()).'_'.$image->getClientOriginalName());
           $FileEnconded=  \File::get($request->video);
            \Storage::disk('spaces')->put('video/'.$filename, (string)$FileEnconded,'public');
            $filter_option->video = $filename;
        }
      }
      if($request->hasfile('banner')) {
        if ($image = $request->file('banner')) {
           $filename = $this->uploadImage($image);
           $filter_option->banner = $filename;
        }
      }
      $filter_option->save();
     return redirect()->back();

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\FilterType  $filterType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Category $category, FilterType $filterType)
    {

        // print_r($request->all());die;
        // $request->merge(['preference_name' => Str::slug($request->preference_name,'_')]);
        // $request->merge(['filter_name' => Str::slug($request->filter_name,'_')]);
        $validatedData = $this->validate($request, [
                'filter_name' => 'required',
                // .Rule::unique('filter_types')->ignore($filterType->id)->where(function ($query) use($category) {
                //     return $query->where(['category_id'=>$category->id]);
                // }),
                'preference_name' => 'required',
                // .Rule::unique('filter_types')->ignore($filterType->id)->where(function ($query) use($category) {
                //     return $query->where(['category_id'=>$category->id]);
                // }),
                'category' => 'required',
                'multiselect' => 'required',
            ]);
          $input = $request->all();
          // print_r($input);die;
          $filterType->category_id = $input['category'];
          // $filterType->preference_name = Str::slug($input['preference_name'],'_');
          // $filterType->filter_name = Str::slug($input['filter_name'],'_');
          $filterType->preference_name = $input['preference_name'];
          $filterType->filter_name = $input['filter_name'];
          $filterType->is_multi = $input['multiselect'];
          if($filterType->save()){
            if(isset($input['filter_option'])){
                $filter_options = $input['filter_option']['name'];
                foreach ($filter_options as $f_id => $filter_option) {
                    $f_data = FilterTypeOption::where('id',$f_id)->first();
                    $f_data->option_name = $filter_option;
                    $filename = $f_data->image;
                    if(isset($input['filter_option']['image'][$f_id])){
                        if ($image = $request->file('filter_option')['image'][$f_id]){
                            $filename = $this->uploadImage($image);
                        }
                    }
                    if(isset($input['filter_option']['description'][$f_id])){
                        $f_data->description = $input['filter_option']['description'][$f_id];
                    }
                    if(isset($input['filter_option']['price'][$f_id])){
                        $f_data->price = $input['filter_option']['price'][$f_id];
                    }
                    $f_data->image = $filename;
                    $f_data->save();
                }
            }

            if(isset($input['new_option'])){
                foreach ($input['new_option']['name'] as $key=>$filter_option) {
                    if($filter_option){
                        $filtertypeoption = FilterTypeOption::firstOrCreate(array(
                            'filter_type_id'=>$filterType->id,
                            'option_name'=>$filter_option,
                        ));
                        $filename = null;
                        if(isset($input['new_option']['image'][$key])){
                            if ($image = $request->file('new_option')['image'][$key]){
                                $filename = $this->uploadImage($image);
                            }
                        }
                        if(isset($input['new_option']['description'][$key])){
                          $filtertypeoption->description = $input['new_option']['description'][$key];
                        }
                        if(isset($input['new_option']['price'][$key])){
                          $filtertypeoption->price = $input['new_option']['price'][$key];
                        }
                        $filtertypeoption->image = $filename;
                        $filtertypeoption->save();
                    }
                }
            }
          }
          return redirect()->back();
    }


    public function deleteFilterOption(Request $request){
        $filtertypeoption_id  = $request->filtertypeoption_id;
        FilterTypeOption::where('id',$filtertypeoption_id)->delete();
        return response()->json(['status'=>'success']);
        // $spIds= \App\User::whereHas('roles', function ($query) {
        //                    $query->whereIn('name',['customer','service_provider']);
        //                 })->pluck('id')->toArray();
        // $exist = ServiceProviderFilterOption::whereIn('sp_id',$spIds)->where('filter_option_id',$filtertypeoption_id)->first();
        // if($exist){
        //     return response()->json(['status'=>'error','message'=>"Can't Delete vendors already set this option"]);
        // }else{
        // }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\FilterType  $filterType
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category,FilterType $filterType)
    {
        if($filterType->delete()){
            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'error']);
        }
    }
}
