<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Course;
use App\Model\SpCourse;
use App\User;
use App\Model\Category;
use App\Model\ConsultClass;
use App\Model\Package,App\Model\UserPackage,App\Model\Transaction,App\Model\Payment;
use App\Model\AdditionalDetail;
use App\Model\SpAdditionalDetail;
use App\Notification;
use Illuminate\Support\Facades\Auth;
use Validator,Hash,Mail,DB;
use DateTime,DateTimeZone;
use Redirect,Response,File,Image;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use App\Model\EnableService;
use App\Model\CategoryServiceProvider;
class CourseController extends Controller
{
    public function __construct() {
        $this->middleware('auth')->except(['getcourses']);
    }
    /**
     * @SWG\Get(
     *     path="/courses",
     *     description="Get Class Courses",
     * tags={"Category"},
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     */

     public static function getcourses(Request $request) {
        try{
            $per_page = (isset($request->per_page)?$request->per_page:10);
            $Course = Course::orderBy('id', 'desc')->cursorPaginate($per_page);
            
            $after = null;
            if($Course->meta['next']){
                $after = $Course->meta['next']->target;
            }
            $before = null;
            if($Course->meta['previous']){
                $before = $Course->meta['previous']->target;
            }
            $per_page = $Course->perPage();
            return response(['status' => "success", 'statuscode' => 200,
                                'message' => __('course'), 'data' =>['spCourses'=>$Course->items(),'after'=>$after,'before'=>$before,'per_page'=>$per_page]], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }


    /**
     * @SWG\POST(
     *     path="/sp-course",
     *     description="Course For Service Provider",
     * tags={"Service Provider Course"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="course_id",
     *         in="query",
     *         type="integer",
     *         description="Course ID",
     *         required=false,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     */

    public static function postspcourses(Request $request) {
        try{
            
            $rules = [
                    'course_id'=>'required',    
            ];
            
          
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $input = $request->all();

            $user=Auth::user();

           
            if(isset($input['course_id'])){
                $coursearray=explode(',',$input['course_id']);
                $data=[];
                SpCourse::where(['sp_id'=>$user->id])->delete();
                foreach ($coursearray as $courses) {
                    $spcourse = SpCourse::firstOrCreate([
                        'sp_id'=>$user->id,
                        'course_id'=>$courses,
                    ]);

                    $data[]=$spcourse;
                }


            }

            return response(['status' => "success", 'statuscode' => 200,'message' => __('SPcourse'), 'data' =>['spCourses'=>$user->getcourseSP($user->id)]], 200);
        }catch(Exception $ex){
            return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->getMessage()], 500);
        }
    }


    
}
