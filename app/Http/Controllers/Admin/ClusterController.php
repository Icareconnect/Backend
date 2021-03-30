<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Cluster;
use App\Model\ClusterCategory;
use Illuminate\Http\Request;
use App\Http\Traits\CategoriesTrait;
class ClusterController extends Controller
{
    use CategoriesTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clusters = Cluster::orderBy('id','DESC')->get();
        return view('admin.cluster.index')->with(array('clusters'=>$clusters));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = $this->parentCategories();
        return view('admin.cluster.add',compact('categories'));
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
                'description'      => 'required|string',
                'categories' => 'required|array|min:1',
          ]);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
          $input = $request->all();
          $cluster = new Cluster();
          $cluster->name = $input['name'];
          $cluster->description = $input['description'];
          if($cluster->save()){
            foreach ($input['categories'] as $key => $category_id) {
                $clustercategory = new ClusterCategory();
                $clustercategory->category_id = $category_id;
                $clustercategory->cluster_id = $cluster->id;
                $clustercategory->save();
            }
          }
          return redirect('admin/cluster');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Cluster  $cluster
     * @return \Illuminate\Http\Response
     */
    public function show(Cluster $cluster)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\Cluster  $cluster
     * @return \Illuminate\Http\Response
     */
    public function edit(Cluster $cluster)
    {
       $categories = $this->parentCategories();
       return view('admin.cluster.edit', compact('categories','cluster'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\Cluster  $cluster
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cluster $cluster)
    {
        $validator = \Validator::make($request->all(), [
                'name' => 'required|string',
                'description'      => 'required|string',
                'categories' => 'required|array|min:1',
          ]);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
          $input = $request->all();
          $cluster->name = $input['name'];
          $cluster->description = $input['description'];
          if($cluster->save()){
            $deleted = array_diff($cluster->cluster_category->pluck('category_id')->toArray(), $input['categories']);
            $new_category = array_diff($input['categories'],$cluster->cluster_category->pluck('category_id')->toArray());
            if(count($deleted)>0)
                $deleted_category = ClusterCategory::where('cluster_id',$cluster->id)->whereIn('category_id',$deleted)->delete();
            foreach ($new_category as $key => $category_id) {
                if($category_id){
                    $clustercategory = new ClusterCategory();
                    $clustercategory->category_id = $category_id;
                    $clustercategory->cluster_id = $cluster->id;
                    $clustercategory->save();
                }
            }
          }

          return redirect('admin/cluster');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Cluster  $cluster
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cluster $cluster)
    {
       $deleted_cluscategory = ClusterCategory::where('cluster_id',$cluster->id)->delete();
       if($cluster->delete()){
            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'error']);
        }
    }
}
