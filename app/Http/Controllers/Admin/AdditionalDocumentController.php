<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\AdditionalDetail;
use App\Model\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Validation\Rule;
class AdditionalDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AdditionalDetail $category)
    {
         $additionals = AdditionalDetail::orderBy('id','DESC')->get();
        return view('admin.additionaldocument.index', compact('additionals'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Category $category)
    {
        return view('admin.additionaldocument.add', compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

          $validator = \Validator::make($request->all(), [
                'name' => 'required',
                'field_type' => 'required',
                
                'is_enable' => 'required',
          ]);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
          $input = $request->all();
          $filtertype = new AdditionalDetail();
          
          $filtertype->name = $input['name'];
          $filtertype->type = $input['field_type'];
          $filtertype->is_enable = $input['is_enable'];
          if($filtertype->save()){
            return redirect('admin/additional-document');
          }
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
    public function edit(AdditionalDetail $additionalDetail)
    {
        if(!$additionalDetail){
            abort(404);
        }
        $filterType = $additionalDetail;
        //dd($filterType);
        $parentCategories = Category::where('parent_id',NULL)->where('enable','=','1')->get();
        
        return view('admin.additionaldocument.edit', compact('filterType'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\FilterType  $filterType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,AdditionalDetail $additionalDetail)
    {
        $validatedData = $this->validate($request, [
                'name' => 'required',
                'field_type' => 'required',
                
                'is_enable' => 'required',
           ]);
          $input = $request->all();
          $additionalDetail->name = $input['name'];
          $additionalDetail->type = $input['field_type'];
          $additionalDetail->is_enable = $input['is_enable'];
          if($additionalDetail->save()){
            
          }
          return redirect('admin/additional-document');
    }


   

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\FilterType  $filterType
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category,AdditionalDetail $filterType)
    {
        if($filterType->delete()){
            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'error']);
        }
    }
}
