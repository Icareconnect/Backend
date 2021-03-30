<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Course;
use App\Model\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Validation\Rule;
class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Course $Course)
    {
         $course = Course::orderBy('id','DESC')->get();
        return view('admin.course.index', compact('course'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Category $category)
    {
        return view('admin.course.add', compact('category'));
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
                'title' => 'required',
                'color_code' => 'required',
                'image_icon' => 'required',
            ]);
          if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
          }
          $input = $request->all();
          $course = new Course();
          
          $course->title = $input['title'];
          $course->color_code = $input['color_code'];
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
                  $course->image_icon = $filename;
              }
          }
          if($course->save()){
            return redirect('admin/course');
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
    public function edit(Course $Course)
    {
        if(!$Course){
            abort(404);
        }
        $filterType = $Course;
        //dd($filterType);
        
        return view('admin.course.edit', compact('filterType'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\FilterType  $filterType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Course $course)
    {
            $validator = \Validator::make($request->all(), [
                'title' => 'required',
                'color_code' => 'required',
                'image_icon' => 'required',
            ]);

            $validator = \Validator::make($request->all(),$rule,$msg);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $input = $request->all();
         
          
          $course->title = $input['title'];
          $course->color_code = $input['color_code'];
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
                  $course->image_icon = $filename;
              }
          }
          return redirect('admin/course');
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
