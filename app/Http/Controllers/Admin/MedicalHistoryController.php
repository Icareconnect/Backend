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
class MedicalHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $masterpreferences = MasterPreference::orderBy('id','DESC')->whereIn('type',['medical_history'])->where('created_by',null)->get();
        return view('admin.medical_history.index', compact('masterpreferences'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.medical_history.add');
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
          $filtertype->type = 'medical_history';
          if(isset($input['show_on'])){
            $filtertype->show_on = $input['show_on'];
          }
          if($filtertype->save()){
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
                $filtertypeoption->image = $filename;
                $filtertypeoption->save();
            }
          }
          return redirect('admin/master/medical_history');
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
        $masterPreference->filter_option = MasterPreferencesOption::where('preference_id',$masterPreference->id)->get();
        return view('admin.medical_history.edit', compact('masterPreference'));
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
        $currentPath= Route::getFacadeRoot()->current()->uri();
         $validatedData = $this->validate($request, [
                'preference_name' => 'required',
                'multiselect' => 'required',
            ]);
          $input = $request->all();
          $masterPreference->name = $input['preference_name'];
          $masterPreference->is_multi = ($input['multiselect']=="1")?1:0;
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
                        $filtertypeoption->image = $filename;
                        $filtertypeoption->save();
                    }
                }
            }
          }
        return redirect('admin/master/medical_history');
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
