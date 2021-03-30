<?php

namespace App\Http\Controllers\Admin;

use App\Model\Insurance;
use Illuminate\Http\Request;
use App\Imports\InsurancesImport;
use App\Http\Traits\CategoriesTrait;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
class InsuranceController extends Controller
{
    use CategoriesTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $insurances = Insurance::orderBy('id','DESC')->get();
        return view('admin.insurances.index')->with(array('insurances'=>$insurances));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = $this->parentCategories();
        return view('admin.insurances.add',compact('categories'));
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
                'name' => 'required|string',
                // 'company'      => 'required|string',
          ]);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
          $input = $request->all();
          $insurance = new Insurance();
          $insurance->name = $input['name'];
          $insurance->company = isset($input['company'])?$input['company']:'';
          $insurance->carrier_code = isset($input['carrier_code'])?$input['carrier_code']:null;
          $insurance->category_id = isset($input['category_id'])?$input['category_id']:null;
          $insurance->save();
          $client = new \Predis\Client();
          $client->flushAll();
          return redirect('admin/insurance');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Insurance  $insurance
     * @return \Illuminate\Http\Response
     */
    public function show(Insurance $insurance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\Insurance  $insurance
     * @return \Illuminate\Http\Response
     */
    public function edit(Insurance $insurance)
    {
         $categories = $this->parentCategories();
         return view('admin.insurances.edit', compact('categories','insurance'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\Insurance  $insurance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Insurance $insurance)
    {
         $validator = \Validator::make($request->all(), [
                'name' => 'required|string',
                // 'company' => 'required|string',
          ]);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
          $input = $request->all();
          $insurance->name = $input['name'];
          $insurance->company = isset($input['company'])?$input['company']:'';
          $insurance->carrier_code = isset($input['carrier_code'])?$input['carrier_code']:$insurance->carrier_code;
          $insurance->category_id = isset($input['category_id'])?$input['category_id']:null;
          $insurance->save();
          $client = new \Predis\Client();
          $client->flushAll();
          return redirect('admin/insurance');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Insurance  $insurance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Insurance $insurance)
    {
        //
    }

    public function PostUploadxls(Request $request){
      try {
            $image = $request->file('fileName');
            $extension = $image->getClientOriginalExtension();
            $name = $image->getFilename();
            \Illuminate\Support\Facades\Storage::disk('media')->put(time() . $image->getFilename() . '.' . $extension, \Illuminate\Support\Facades\File::get($image));
            Excel::import(new InsurancesImport, base_path('public/media/'.time() . $image->getFilename() . '.' . $extension));
            return response()->json(['status'=>'success']);
        } catch (\Exception $e) {
            return response()->json(['status'=>'error', 'message' => $e->getMessage()]);
        }
    }
}
