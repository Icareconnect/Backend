<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\MasterPreference;

use App\Model\MasterPreferencesOption;
use App\Model\UserMasterPreference;
use Illuminate\Http\Request;


use Illuminate\Support\Str;
use Exception;
use Illuminate\Validation\Rule;
class SymptomsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $masterpreferences = MasterPreference::where('type','symptoms')->orderBy('id','DESC')->get();
        return view('admin.symptoms.index', compact('masterpreferences'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.symptoms.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // print_r($request->all());die;
          // $request->merge(['preference_name' => Str::slug($request->preference_name,'_')]);
          // $request->merge(['filter_name' => Str::slug($request->filter_name,'_')]);
          $validator = \Validator::make($request->all(), [
                'symptom_name' => 'required',
                'filter_option' => 'required',
                'multiselect' => 'required',
          ]);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
          $input = $request->all();
          $filtertype = new MasterPreference();
          $filtertype->name = $input['symptom_name'];
          $filtertype->is_multi = $input['multiselect'];
          $filtertype->type = 'symptoms';
          $filename = null;
          if ($image = $request->file('icon')){
                $filename = $this->uploadImage($image);
          }
          $filtertype->image = $filename;
          if($filtertype->save()){
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
                if(isset($input['filter_option']['description'][$key])){
                    $filtertypeoption->description = $input['filter_option']['description'][$key];
                }
                $filtertypeoption->image = $filename;
                $filtertypeoption->save();
            }
          }
          return redirect('admin/master/symptoms');
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
        if(!$masterPreference){
            abort(404);
        }
        $masterPreference->filter_option = MasterPreferencesOption::where('preference_id',$masterPreference->id)->get();
        return view('admin.symptoms.edit', compact('masterPreference'));
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
         $validatedData = $this->validate($request, [
                'symptom_name' => 'required',
                'multiselect' => 'required',
            ]);
          $input = $request->all();
          $masterPreference->name = $input['symptom_name'];
          $masterPreference->is_multi = ($input['multiselect']=="1")?1:0;
          if ($image = $request->file('icon')){
                $filename = $this->uploadImage($image);
                $masterPreference->image = $filename;
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
                    if(isset($input['filter_option']['description'][$f_id])){
                        $f_data->description = $input['filter_option']['description'][$f_id];
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
                        if(isset($input['new_option']['description'][$key])){
                            $filtertypeoption->description = $input['new_option']['description'][$key];
                        }
                        $filtertypeoption->image = $filename;
                        $filtertypeoption->save();
                    }
                }
            }
          }
          return redirect('admin/master/symptoms');
    }


    public function deleteMasterOption(Request $request){
        $filtertypeoption_id  = $request->filtertypeoption_id;
        $exist = UserMasterPreference::where('preference_option_id',$filtertypeoption_id)->first();
        if($exist){
            return response()->json(['status'=>'error','message'=>"Can't Delete already use that option"]);
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
