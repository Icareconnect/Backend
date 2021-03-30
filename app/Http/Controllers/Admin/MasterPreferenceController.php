<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\MasterPreference;
use App\Model\MasterPreferencesOption;
use App\Model\UserMasterPreference;
use App\Model\MasterPreOptionFilter;
use App\Model\FilterTypeOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Str;
use Exception;
use Illuminate\Validation\Rule;
class MasterPreferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currentPath= Route::getFacadeRoot()->current()->uri();
        if($currentPath=='admin/master/duties'){
            $masterpreferences = MasterPreference::orderBy('id','DESC')->whereIn('type',['duty'])->get();
            return view('admin.custom_preference.index', compact('masterpreferences'));
        }else{
            $masterpreferences = MasterPreference::orderBy('id','DESC')->whereIn('type',['covid','personal_interest','work_environment','preferences'])->get();
          return view('admin.preference.index', compact('masterpreferences'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $currentPath= Route::getFacadeRoot()->current()->uri();
        if($currentPath=='admin/master/duties/create'){
          $FilterTypeOption = FilterTypeOption::get();
          return view('admin.custom_preference.add', compact('FilterTypeOption'));
        }else{
          return view('admin.preference.add');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $currentPath= Route::getFacadeRoot()->current()->uri();
        // print_r($request->all());die;
          // $request->merge(['preference_name' => Str::slug($request->preference_name,'_')]);
          // $request->merge(['filter_name' => Str::slug($request->filter_name,'_')]);
          $validator = \Validator::make($request->all(), [
                'preference_name' => 'required',
                'filter_option' => 'required',
                'multiselect' => 'required',
          ]);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
          $input = $request->all();
          $filtertype = new MasterPreference();
          $filtertype->name = $input['preference_name'];
          $filtertype->is_multi = $input['multiselect'];
          if(isset($input['type'])){
            $filtertype->type = $input['type'];
          }
          if(isset($input['is_required'])){
            $filtertype->is_required = $input['is_required'];
          }
          if(isset($input['show_on'])){
            $filtertype->show_on = $input['show_on'];
          }

          if($filtertype->save()){
            if($filtertype->type=='duty'){
                $filtertype->module_table = 'filter_options';
            }
            if(isset($input['module_table'])){
              $filtertype->module_table = $input['module_table'];
            }
            $filtertype->save();
            $filter_options = $input['filter_option']['name'];
            foreach ($filter_options as $key => $filter_option) {
                $filtertypeoption = MasterPreferencesOption::firstOrcreate(array(
                    'preference_id'=>$filtertype->id,
                    'name'=>$filter_option,
                    ));
                $filename = null;
                if(isset($input['filter_option']['image'][$key])){
                    if ($image = $request->file('filter_option')['image'][$key]){
                        $filename = $this->uploadImage($image);
                    }
                }
                if(isset($input['filter_option']['category_filter_option'][$key])){
                    foreach ($input['filter_option']['category_filter_option'][$key] as $key2 => $option_id) {
                        MasterPreOptionFilter::create(['option_id'=>$filtertypeoption->id,'module_table'=>'filter_options','module_id'=>$option_id]);
                    }
                }
                $filtertypeoption->image = $filename;
                $filtertypeoption->save();
            }
          }
          if($currentPath=='admin/master/duties'){
            return redirect('admin/master/duties');
          }else{
            return redirect('admin/master/preferences');
          }
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
     * @param  \App\Model\MasterPreference  $masterPreference
     * @return \Illuminate\Http\Response
     */
    public function show(MasterPreference $masterPreference)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\MasterPreference  $masterPreference
     * @return \Illuminate\Http\Response
     */
    public function edit(MasterPreference $masterPreference)
    {
       $currentPath= Route::getFacadeRoot()->current()->uri();
        if(!$masterPreference){
            abort(404);
        }
        $masterPreference->filter_option = MasterPreferencesOption::where('preference_id',$masterPreference->id)->get();
        if($masterPreference->name=='Select your Service Conditions' && $masterPreference->type=='work_environment'){
          $masterPreference->mapOptions = MasterPreferencesOption::whereHas('masterpreference',function($query){
            $query->where([
              'name'=>'Select your Preferred Conditions',
              'type'=>'work_environment',
            ]);
          })->get();
        }
        if($currentPath=='admin/master/duties/{master_preference}/edit'){
          $masterPreference->FilterTypeOption = FilterTypeOption::get();
          return view('admin.custom_preference.edit', compact('masterPreference'));
        }else{
          return view('admin.preference.edit', compact('masterPreference'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\MasterPreference  $masterPreference
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MasterPreference $masterPreference)
    {
         // print_r($request->all());die;
        // $request->merge(['preference_name' => Str::slug($request->preference_name,'_')]);
        // $request->merge(['filter_name' => Str::slug($request->filter_name,'_')]);
      $currentPath= Route::getFacadeRoot()->current()->uri();
         $validatedData = $this->validate($request, [
                'preference_name' => 'required',
                'multiselect' => 'required',
            ]);
          $input = $request->all();
          $masterPreference->name = $input['preference_name'];
          $masterPreference->is_multi = ($input['multiselect']=="1")?1:0;
          if(isset($input['type'])){
            $masterPreference->type = $input['type'];
          }
          if(isset($input['is_required'])){
            $masterPreference->is_required = $input['is_required'];
          }
          if(isset($input['show_on'])){
            $masterPreference->show_on = $input['show_on'];
          }
          if($masterPreference->save()){
            if(isset($input['filter_option'])){
                $filter_options = $input['filter_option']['name'];
                foreach ($filter_options as $f_id => $filter_option) {
                    $f_data = MasterPreferencesOption::where('id',$f_id)->first();
                    $f_data->name = $filter_option;
                    $filename = $f_data->image;
                    if(isset($input['filter_option']['image'][$f_id])){
                        if ($image = $request->file('filter_option')['image'][$f_id]){
                            $filename = $this->uploadImage($image);
                        }
                    }
                    if(isset($input['filter_option']['map_option_id']) && isset($input['filter_option']['map_option_id'][$f_id])){
                        $f_data->map_option_id = $input['filter_option']['map_option_id'][$f_id];
                    }
                    if(isset($input['filter_option']['category_filter_option'][$f_id])){
                        MasterPreOptionFilter::where(['option_id'=>$f_data->id,'module_table'=>'filter_options'])->delete();
                        foreach ($input['filter_option']['category_filter_option'][$f_id] as $key2 => $option_id) {
                            MasterPreOptionFilter::create([
                              'option_id'=>$f_data->id,
                              'module_table'=>'filter_options',
                              'module_id'=>$option_id
                            ]);
                        }
                    }
                    $f_data->image = $filename;
                    $f_data->save();
                }
            }
            if(isset($input['new_option'])){
                foreach ($input['new_option']['name'] as $key=>$filter_option) {
                    if($filter_option){
                        $filtertypeoption = MasterPreferencesOption::firstOrCreate(array(
                            'preference_id'=>$masterPreference->id,
                            'name'=>$filter_option,
                        ));
                        $filename = null;
                        if(isset($input['new_option']['image'][$key])){
                            if ($image = $request->file('new_option')['image'][$key]){
                                $filename = $this->uploadImage($image);
                            }
                        }
                        if(isset($input['new_option']['map_option_id']) && isset($input['new_option']['map_option_id'][$key])){
                          $filtertypeoption->map_option_id = $input['new_option']['map_option_id'][$key];
                        }
                        if(isset($input['new_option']['category_filter_option'][$key])){
                          foreach ($input['new_option']['category_filter_option'][$key] as $key2 => $option_id) {
                              MasterPreOptionFilter::create([
                                'option_id'=>$filtertypeoption->id,
                                'module_table'=>'filter_options',
                                'module_id'=>$option_id
                              ]);
                          }
                        }
                        $filtertypeoption->image = $filename;
                        $filtertypeoption->save();
                    }
                }
            }
          }
          if($currentPath=='admin/master/duties/{master_preference}'){
            return redirect('admin/master/duties');
          }else{
            return redirect('admin/master/preferences');
          }
    }


    public function deleteMasterOption(Request $request){
        $filtertypeoption_id  = $request->filtertypeoption_id;
        $exist = UserMasterPreference::where('preference_option_id',$filtertypeoption_id)->first();
        if($exist){
            return response()->json(['status'=>'error','message'=>"Can't Delete already used that option"]);
        }else{
            MasterPreferencesOption::where('id',$filtertypeoption_id)->delete();
            return response()->json(['status'=>'success']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\MasterPreference  $masterPreference
     * @return \Illuminate\Http\Response
     */
    public function destroy(MasterPreference $masterPreference)
    {
        if($masterPreference->delete()){
            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'error']);
        }
    }
}
