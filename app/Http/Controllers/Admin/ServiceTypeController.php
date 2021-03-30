<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Service,App\Model\CategoryServiceType,App\Model\Category;
use App\Model\ServiceType;
use Illuminate\Support\Str;
use Exception;
use App\Http\Traits\CategoriesTrait;
class ServiceTypeController extends Controller
{
	use CategoriesTrait;
	 /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getServices()
    {
        $services = Service::orderBy('id','DESC')->get();
        return view('admin.services.index')->with(array('services'=>$services));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $service_types = ServiceType::get();
        return view('admin.services.add',compact('service_types'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
          // $request->merge(['service_name' => $request->service_name,'')]);
          $validator = \Validator::make($request->all(), [
                'service_name' => 'required|unique:services,type|min:3|max:255|string',
                'description'      => 'required|min:3|max:255|string',
                'need_availability' => 'required',
                'color_code' => 'required|string',
                'service_type'=>'required'
          ]);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
          $input = $request->all();
          $service = new Service();
          if(isset($input['color_code'])){
            $service->color_code = str_replace('#','',$input['color_code']);
          }
          if(isset($input['service_type'])){
             $service->service_type = $input['service_type'];
          }
          $service->type = $input['service_name'];
          $service->description = $input['description'];
          $service->need_availability =$input['need_availability'];
          if($service->save()){

          }
          return redirect('admin/services');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\EnableService  $enableService
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $service = Service::where('id',$id)->first();
        $service_types = ServiceType::get();
        return view('admin.services.edit')->with(array(
            'service'=>$service,
            'service_types'=>$service_types
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\EnableService  $enableService
     * @return \Illuminate\Http\Response
     */
    public function getCategories($id)
    {
    	$service_data = Service::where('id',$id)->first();
        $services = CategoryServiceType::where('service_id',$id)->orderBy('id','DESC')->get();
        return view('admin.services.categories.index',compact('services','service_data'));
    }

     /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\EnableService  $enableService
     * @return \Illuminate\Http\Response
     */
    public function addCategoryToService($id)
    {

        $categories = $this->parentCategories();
        $service = Service::where('id',$id)->first();
        $already_add_categories = CategoryServiceType::where('service_id',$id)->pluck('category_id');
        return view('admin.services.categories.add',compact('categories','service','already_add_categories'));
    }


     /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\EnableService  $enableService
     * @return \Illuminate\Http\Response
     */
    public function showCategoryServiceForm(Category $category)
    {

        $services = $this->services();
        $add_services = CategoryServiceType::where('category_id',$category->id)
        ->groupBy('service_id')
        ->pluck('service_id')->toArray();
        return view('admin.services.categories.add',compact('category','add_services','services'));
    }

     /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\EnableService  $enableService
     * @return \Illuminate\Http\Response
     */
    public function editCategoryToService(Category $category,$id)
    {
        $categoryservicetype = CategoryServiceType::where('id',$id)->first();
        return view('admin.services.categories.edit',compact('category','categoryservicetype'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\EnableService  $enableService
     * @return \Illuminate\Http\Response
     */
    public function putCategoryToService(Category $category,$id,Request $request)
    {
        $categoryservicetype = CategoryServiceType::where('id',$id)->first();
        $custom_message = [];
         $rules = [
                'gap_duration'      => 'required|numeric',
                'minimum_duration'      => 'required|numeric',
                'fixed_value'      => 'nullable',
                'price_calculation_active'      => 'required',
          ];
        if(isset($request->price_calculation_active) && $request->price_calculation_active=='fixed_price'){
        	$rules['fixed_value'] = 'required|numeric';
        }
    	$validator = \Validator::make($request->all(),$rules,$custom_message);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
	      $input = $request->all();
	      $range = explode(';', $input['price_range']);
	      $categoryservicetype->is_active = $input['is_active'];
	      $categoryservicetype->gap_duration = $input['gap_duration'];
	      $categoryservicetype->minimum_duration = $input['minimum_duration'];
	      if($input['price_calculation_active']=='fixed_price'){
	      	$categoryservicetype->price_fixed = $input['fixed_value'];
	      }else{
	      	$categoryservicetype->price_fixed = null;
	      }
	      $categoryservicetype->price_minimum = $range[0];
	      $categoryservicetype->price_maximum = $range[1];
	      $categoryservicetype->save();
	      return redirect('admin/categories/'.$category->id.'/edit');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\EnableService  $enableService
     * @return \Illuminate\Http\Response
     */
    public function postCategoryToService($service_id,Request $request)
    {
    		 $custom_message = [];
	         $rules = [
	                'category_id' => 'required',
	                'gap_duration'      => 'required|numeric',
	                'minimum_duration'      => 'required|numeric',
	                'fixed_value'      => 'nullable',
	                'price_calculation_active'      => 'required',
	          ];
	        if(isset($request->price_calculation_active) && $request->price_calculation_active=='fixed_price'){
	        	$rules['fixed_value'] = 'required|numeric';
	        }
			if(isset($request->category_id)){
				$already_add_category = CategoryServiceType::where(['service_id'=>$service_id,'category_id'=>$request->category_id])->first();
				if($already_add_category){
					$rules['category_id'] = 'unique:category_service_types,category_id';
					$custom_message['category_id.unique'] = 'This Category already assing to this service';
				}
	        }
    		$validator = \Validator::make($request->all(),$rules,$custom_message);
	      if ($validator->fails()) {
	            return back()->withErrors($validator)->withInput();
	      }
	      $input = $request->all();
	      $range = explode(',', $input['price_range']);
	      $categoryservicetype = new CategoryServiceType();
	      $categoryservicetype->service_id = $service_id;
	      $categoryservicetype->category_id = $input['category_id'];
	      $categoryservicetype->is_active = $input['is_active'];
	      $categoryservicetype->gap_duration = $input['gap_duration'];
	      $categoryservicetype->minimum_duration = $input['minimum_duration'];
	      if($input['price_calculation_active']=='fixed_price'){
	      	$categoryservicetype->price_fixed = $input['fixed_value'];
	      }else{
	      	$categoryservicetype->price_fixed = null;
	      }
	      $categoryservicetype->price_minimum = $range[0];
	      $categoryservicetype->price_maximum = $range[1];
	      $categoryservicetype->save();
	      return redirect('admin/services/'.$service_id.'/categories');
       
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\EnableService  $enableService
     * @return \Illuminate\Http\Response
     */
    public function createCategoryToService(Category $category,Request $request)
    {
             $custom_message = [];
             $rules = [
                    'service_id' => 'required',
                    'gap_duration'      => 'required|numeric',
                    'minimum_duration'      => 'required|numeric',
                    'fixed_value'      => 'nullable',
                    'price_calculation_active'      => 'required',
              ];
            if(isset($request->price_calculation_active) && $request->price_calculation_active=='fixed_price'){
                $rules['fixed_value'] = 'required|numeric';
            }
            if(isset($request->service_id)){
                $already_add_category = CategoryServiceType::where(['service_id'=>$request->service_id,'category_id'=>$category->id])->first();
                if($already_add_category){
                    $rules['service_id'] = 'unique:category_service_types,service_id';
                    $custom_message['service_id.unique'] = 'This Category already assing to this service';
                }
            }
          $validator = \Validator::make($request->all(),$rules,$custom_message);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
          $input = $request->all();
          $range = explode(';', $input['price_range']);
          $categoryservicetype = new CategoryServiceType();
          $categoryservicetype->service_id = $input['service_id'];
          $categoryservicetype->category_id = $category->id;
          $categoryservicetype->is_active = $input['is_active'];
          $categoryservicetype->gap_duration = $input['gap_duration'];
          $categoryservicetype->minimum_duration = $input['minimum_duration'];
          if($input['price_calculation_active']=='fixed_price'){
            $categoryservicetype->price_fixed = $input['fixed_value'];
          }else{
            $categoryservicetype->price_fixed = null;
          }
          $categoryservicetype->price_minimum = $range[0];
          $categoryservicetype->price_maximum = $range[1];
          $categoryservicetype->save();
          return redirect('admin/categories/'.$category->id.'/edit');
       
    }

     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\EnableService  $enableService
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
	      $validator = \Validator::make($request->all(), [
	            'service_name'      => 'required|string',
                'description'      => 'required|min:3|max:255|string',
	            'need_availability' => 'required',
                'color_code' => 'required|string',
                'service_type'=>'required'
	      ]);
	      if ($validator->fails()) {
	            return back()->withErrors($validator)->withInput();
	      }
	      $input = $request->all();
        $service = Service::where('id',$request->service_id)->first();
        $service->description = $request->description;
        if(isset($input['color_code'])){
            $service->color_code = str_replace('#','',$input['color_code']);
        }
        if(isset($input['service_type'])){
            $service->service_type = $input['service_type'];
        }
        $service->need_availability = $request->need_availability;
        $service->type = $request->service_name;
        $service->save();
        return redirect('admin/services');
    }

}
