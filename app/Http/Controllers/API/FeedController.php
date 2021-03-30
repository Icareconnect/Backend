<?php

namespace App\Http\Controllers\API;

use App\Feed;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\User;
use Illuminate\Support\Facades\Auth;
use Validator,Hash,Mail,DB;
use DateTime,DateTimeZone;
use Redirect,Response,File,Image;
use Illuminate\Support\Facades\URL;
use App\Model\Role;
use App\Model\Wallet;
use App\Model\Feedback;
use App\Model\Support,App\Model\SupportAssignee,App\Model\SupportReply;
use App\Model\Profile;
use App\Model\Payment;
use App\Model\FeedComment;
use App\Model\FeedLike;
use App\Model\Service;
use App\Model\SocialAccount;
use App\Model\Subscription,App\Model\FeedFavorite,App\Model\FeedView;
use Socialite,Exception;
use Intervention\Image\ImageManager;
use Carbon\Carbon,Config;
use App\Notification;
class FeedController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/feeds",
     *     description="feed listing",
     * tags={"Feeds"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="type",
     *         in="query",
     *         type="string",
     *         description="Feed Type blog,article,faq,promotional,tip,question",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="consultant_id",
     *         in="query",
     *         type="string",
     *         description="consultant id",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="favorite",
     *         in="query",
     *         type="number",
     *         description="favorite 1,0 1 is use when user logged in to see our favorite listing",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="before",
     *         in="query",
     *         type="string",
     *         description="before id",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="after",
     *         in="query",
     *         type="string",
     *         description="after id",
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $rules = [
                'type' => 'required|in:blog,faq,article,promotional,tip,question',
        ];
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                $validator->getMessageBag()->first()), 400);
        }
        $feed_ids = [];
        if(isset($request->favorite) && $request->favorite){ 
            if(!Auth::guard('api')->check()){
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>"Your session has been expired, Please login again to continue."), 400);
            }else{
                $feed_ids = FeedFavorite::where([
                    "user_id"=>Auth::guard('api')->user()->id,
                    "favorite"=>1
                ])->pluck('feed_id')->toArray();
            }
        }
        $per_page = (isset($request->per_page)?$request->per_page:10);
        if($request->type=='tip'){
            $feeds = Feed::select('id','title','image','description','like','user_id','created_at','views','favorite')->where('type',$request->type)
            ->where("created_at",">",\Carbon\Carbon::now()->subDay())->where("created_at","<",\Carbon\Carbon::now())
            ->when('user_id', function($query) use ($request,$feed_ids){
                    if(isset($request->consultant_id)){
                        return $query->where('user_id',$request->consultant_id);
                    }
                    if(isset($request->favorite) && $request->favorite){
                        return $query->whereIn('id',$feed_ids);
                    }
            })
            ->orderBy('id', 'desc')
            ->cursorPaginate($per_page);
        }else{
            $feeds = Feed::select('id','title','image','description','like','user_id','created_at','views','favorite')->where('type',$request->type)
            ->when('user_id', function($query) use ($request,$feed_ids){
                    if(isset($request->consultant_id)){
                        return $query->where('user_id',$request->consultant_id);
                    }
                    if(isset($request->favorite) && $request->favorite){
                        return $query->whereIn('id',$feed_ids);
                    }
            })
            ->orderBy('id', 'desc')
            ->cursorPaginate($per_page);
        }

        foreach ($feeds as $key => $feed) {
            $feed->comment_count = FeedComment::where('feed_id',$feed->id)->count();
            $user_data = User::select(['id', 'name', 'email','phone','profile_image'])->with('profile')->where('id',$feed->user_id)->first();
            $feed->user_data = $user_data;
            if(Auth::guard('api')->check()){
                $feedfavorite = FeedFavorite::where([
                    "user_id"=>Auth::guard('api')->user()->id,
                    "feed_id"=>$feed->id,
                    "favorite"=>1
                ])->first();
                $feed->is_favorite = false;
                if($feedfavorite){
                    $feed->is_favorite = true;
                }

                $like = FeedLike::where([
                    "user_id"=>Auth::guard('api')->user()->id,
                    "feed_id"=>$feed->id,
                    "like"=>'1'
                ])->first();
                $feed->is_like = false;
                if($like){
                    $feed->is_like = true;
                }
            }
        }
        $after = null;
        if($feeds->meta['next']){
            $after = $feeds->meta['next']->target;
        }
        $before = null;
        if($feeds->meta['previous']){
            $before = $feeds->meta['previous']->target;
        }
        $per_page = $feeds->perPage();
        return response([
            'status' => "success",
            'statuscode' => 200,
            'message' => __("$request->type listing"),
            'data' =>['feeds'=>$feeds->items(),'after'=>$after,'before'=>$before,'per_page'=>$per_page]],
            200);
    }


    /**
     * @SWG\Get(
     *     path="/faqs",
     *     description="faqs listing",
     * tags={"Feeds"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="before",
     *         in="query",
     *         type="string",
     *         description="before id",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="after",
     *         in="query",
     *         type="string",
     *         description="after id",
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getFAQs(Request $request)
    {
        $user = Auth::user();
        $per_page = (isset($request->per_page)?$request->per_page:10);
        $faqs = Feed::select('id','title','image','description')->where('type','faq')
        ->orderBy('id', 'desc')
        ->cursorPaginate($per_page);
        $after = null;
        if($faqs->meta['next']){
            $after = $faqs->meta['next']->target;
        }
        $before = null;
        if($faqs->meta['previous']){
            $before = $faqs->meta['previous']->target;
        }
        $per_page = $faqs->perPage();
        return response([
            'status' => "success",
            'statuscode' => 200,
            'message' => __('FAQs List '),
            'data' =>['faqs'=>$faqs->items(),'after'=>$after,'before'=>$before,'per_page'=>$per_page]],
            200);
    } 


    
    public function getTips(Request $request)
    {
        $per_page = (isset($request->per_page)?$request->per_page:10);
        $faqs = Feed::select('id','title','image','description')->where('type','tip')->where("created_at",">",\Carbon\Carbon::now()->subDay())->where("created_at","<",\Carbon\Carbon::now())
        ->orderBy('id', 'desc')
        ->cursorPaginate($per_page);
        $after = null;
        if($faqs->meta['next']){
            $after = $faqs->meta['next']->target;
        }
        $before = null;
        if($faqs->meta['previous']){
            $before = $faqs->meta['previous']->target;
        }
        $per_page = $faqs->perPage();
        return response([
            'status' => "success",
            'statuscode' => 200,
            'message' => __('Tip of the day List '),
            'data' =>['tips'=>$faqs->items(),'after'=>$after,'before'=>$before,'per_page'=>$per_page]],
            200);
    }

    /**
     * @SWG\Post(
     *     path="/feeds",
     *     description="Store Feed",
     * tags={"Feeds"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="title",
     *         in="query",
     *         type="string",
     *         description=" Feed Title",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="description",
     *         in="query",
     *         type="string",
     *         description="Feed Description",
     *         required=true,
     *     ),
     *  @SWG\Parameter(
     *         name="type",
     *         in="query",
     *         type="string",
     *         description="feed type blog,article",
     *         required=true,
     *     ),
     *    @SWG\Parameter(
     *      name="image",
     *      in="query",
     *      description="image",
     *      required=false,
     *      type="string"
     *      ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $user = Auth::user();
            // if(!$user->hasrole('service_provider')){
            //     return response(array('status' => "error", 'statuscode' => 400, 'message' =>'Invalid user role, must be role as service_provider'), 400);
            // }
            $rules = [
                'title' => 'required',
                'description' =>'required',
                'type'=>'required|in:blog,article,promotional',
            ];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $input = $request->all();
            $create = new Feed();
            $create->title = $input['title'];
            $create->description = $input['description'];
            $create->type = $input['type'];
            $create->user_id = $user->id;
            $create->image = isset($request->image)?$request->image:null;
            $create->save();
            if($create){
                return response(['status' =>"success", 'statuscode' => 200, 'message' => __('Feed Created'), 'data' =>['feed'=>$create]], 200);
            }else{
                return response(['status' => 'error', 'statuscode' => 400, 'message' => __('Feed not created')], 400);
            }
        } catch (Exception $e) {
            return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function show(Feed $feed)
    {
        return response(['status' =>"success", 'statuscode' => 200, 'message' => __('Feed detail'), 'data' =>['feed'=>$feed]], 200);
    }



    /*
     *
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Feed $feed)
    {
        
    }



     /**
     * @SWG\Post(
     *     path="/feeds/update/{feed_id}",
     *     description="Store Feed",
     * tags={"Feeds"},
     *     security={
     *     {"Bearer": {}},
     *   },
     * @SWG\Parameter(
     *       name="feed_id",
     *       in="path",
     *       required=true, 
     *       type="string" 
     *      ),
     *  @SWG\Parameter(
     *         name="title",
     *         in="query",
     *         type="string",
     *         description=" Feed Title",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="description",
     *         in="query",
     *         type="string",
     *         description="Feed Description",
     *         required=false,
     *     ),
     *    @SWG\Parameter(
     *      name="image",
     *      in="query",
     *      description="image",
     *      required=false,
     *      type="string"
     *      ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     *
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function postUpdateFeed(Request $request,$feed_id)
    {
        try {
            $user = Auth::user();
            // if(!$user->hasrole('service_provider')){
            //     return response(array('status' => "error", 'statuscode' => 400, 'message' =>'Invalid user role, must be role as service_provider'), 400);
            // }
            $rules = [
                'title' => 'required',
                'description' =>'required',
                'type'=>'required|in:blog,article,promotional',
            ];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $input = $request->all();
            $feed = Feed::where('id',$feed_id)->first();
            $feed->title = $input['title'];
            $feed->description = $input['description'];
            $feed->type = $input['type'];
            $feed->image = isset($request->image)?$request->image:$feed->image;
            $feed->save();
            if($feed){
                return response(['status' =>"success", 'statuscode' => 200, 'message' => __('Feed Updated'), 'data' =>['feed'=>$feed]], 200);
            }else{
                return response(['status' => 'error', 'statuscode' => 400, 'message' => __('Feed not Updated')], 400);
            }
        } catch (Exception $e) {
           return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500); 
        }
    }


     /**
     * @SWG\Post(
     *     path="/feeds/delete/{feed_id}",
     *     description="Delete Feed",
     * tags={"Feeds"},
     *     security={
     *     {"Bearer": {}},
     *   },
     * @SWG\Parameter(
     *       name="feed_id",
     *       in="path",
     *       required=true, 
     *       type="string" 
     *      ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     *
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function postDeleteFeed(Request $request,$feed_id)
    {
        try {
            $user = Auth::user();
            $rules = [
                'feed_id' => 'required',
            ];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $FeedFavorite = FeedFavorite::where('feed_id',$feed_id)->delete();
            $FeedView = FeedView::where('feed_id',$feed_id)->delete();
            $feed = Feed::where('id',$feed_id)->delete();
            return response(['status' =>"success", 'statuscode' => 200, 'message' => __('Feed Delete'), 'data' =>(Object)[]], 200);
        } catch (Exception $e) {
           return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500); 
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function destroy(Feed $feed)
    {
        //
    }

      /**
     * @SWG\Post(
     *     path="/feeds/add-favorite/{feed_id}",
     *     description="addToFavorite",
     * tags={"Feeds"},
     *     security={
     *     {"Bearer": {}},
     *   },
     * @SWG\Parameter(
     *       name="feed_id",
     *       in="path",
     *       required=true, 
     *       type="string" 
     *      ),     
     * @SWG\Parameter(
     *       name="favorite",
     *       in="query",
     *       required=true,
     *       description="0,1", 
     *       type="string" 
     *      ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     *
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function addToFavorite(Request $request,$feed_id){
        try {
            $user = Auth::user();
            $rules = [
                'favorite' => 'required|numeric|in:1,0',
            ];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $feed = Feed::where("id",$feed_id)->first();
            $feedfavorite = FeedFavorite::firstOrCreate([
                "user_id"=>$user->id,
                "feed_id"=>$feed_id
            ]);
            $feedfavorite->favorite = $request->favorite;
            $feedfavorite->save();
            $count = FeedFavorite::where("feed_id",$feed_id)->where("favorite",1)->count();
            $feed->favorite = $count;
            $feed->save();
            return response(['status' =>"success", 'statuscode' => 200, 'message' => __('Add to Favorite'), 'data' =>(Object)[]], 200);
        } catch (Exception $e) {
           return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500); 
        }
    } 

    /**
     * @SWG\Post(
     *     path="/feeds/add-comment/{feed_id}",
     *     description="addToComment",
     * tags={"Feeds"},
     *     security={
     *     {"Bearer": {}},
     *   },
     * @SWG\Parameter(
     *       name="feed_id",
     *       in="path",
     *       required=true, 
     *       type="string" 
     *      ),     
     * @SWG\Parameter(
     *       name="comment",
     *       in="query",
     *       required=true,
     *       description="comment", 
     *       type="string" 
     *      ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     *
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function addToComment(Request $request,$feed_id){
        try {
            $user = Auth::user();
            $rules = [
                'comment' => 'required',
            ];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $feed = Feed::where("id",$feed_id)->first();
            if($feed){
                $feedfavorite = new FeedComment();
                $feedfavorite->feed_id = (int)$feed_id;
                $feedfavorite->comment = $request->comment;
                $feedfavorite->user_id = $user->id;
                $feedfavorite->save();
                $feedfavorite->user = User::select('name','email','id','profile_image','phone','country_code')->where('id',$feedfavorite->user_id)->first();
                return response(['status' =>"success", 'statuscode' => 200, 'message' => __('Commented'), 'data' =>['comment'=>$feedfavorite]], 200);
            }
            return response(['status' =>"success", 'statuscode' => 200, 'message' => __('Commented'), 'data' =>(Object)[]], 200);
        } catch (Exception $e) {
           return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500); 
        }
    }

    /**
     * @SWG\Get(
     *     path="/feeds/comments/{feed_id}",
     *     description="Get Comments",
     * tags={"Feeds"},
     * @SWG\Parameter(
     *       name="feed_id",
     *       in="path",
     *       required=true, 
     *       type="string" 
     *      ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     *
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function getComments(Request $request,$feed_id){
        try {
            $per_page = (isset($request->per_page)?$request->per_page:10);
            $comments = FeedComment::where('feed_id',$feed_id)->with(['user' => function($query) {
                            return $query->select(['name','email','id','profile_image','phone','country_code']);
                }])
            ->orderBy('id', 'desc')
            ->cursorPaginate($per_page);
            $after = null;
            if($comments->meta['next']){
                $after = $comments->meta['next']->target;
            }
            $before = null;
            if($comments->meta['previous']){
                $before = $comments->meta['previous']->target;
            }
            $per_page = $comments->perPage();
            return response([
                'status' => "success",
                'statuscode' => 200,
                'message' => __('Comments'),
                'data' =>['comments'=>$comments->items(),'after'=>$after,'before'=>$before,'per_page'=>$per_page]],
                200);
        } catch (Exception $e) {
           return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500); 
        }
    }

    /**
     * @SWG\Post(
     *     path="/feeds/add-like/{feed_id}",
     *     description="addToLike",
     * tags={"Feeds"},
     *     security={
     *     {"Bearer": {}},
     *   },
     * @SWG\Parameter(
     *       name="feed_id",
     *       in="path",
     *       required=true, 
     *       type="string" 
     *      ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     *
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function addToLike(Request $request,$feed_id){
        try {
            $user = Auth::user();
            $feed = Feed::where("id",$feed_id)->first();
            if($feed){
                $like = FeedLike::where(['user_id'=>$user->id,'feed_id'=>$feed_id])->first();
                if($like){
                    $like->like = '1';
                    $like->save();
                }else{
                    $like = new FeedLike();
                    $like->feed_id = $feed_id;
                    $like->like = '1';
                    $like->user_id = $user->id;
                    $like->save();
                    $count = FeedLike::where("feed_id",$feed_id)->where("like",'1')->count();
                    $feed->like = $count;
                    $feed->save();
                }
                return response(['status' =>"success", 'statuscode' => 200, 'message' => __('Liked'), 'data' =>['like'=>$like]], 200);
            }
            return response(['status' =>"success", 'statuscode' => 200, 'message' => __('Liked'), 'data' =>(Object)[]], 200);
        } catch (Exception $e) {
           return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500); 
        }
    }


       /**
     * @SWG\Get(
     *     path="/feeds/view/{feed_id}",
     *     description="postViewFeed",
     * tags={"Feeds"},
     *     security={
     *     {"Bearer": {}},
     *   },
     * @SWG\Parameter(
     *       name="feed_id",
     *       in="path",
     *       required=true, 
     *       type="string" 
     *      ),     
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     *
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function postViewFeed(Request $request,$feed_id){
        try {
            $user = Auth::user();
            $feed = Feed::where("id",$feed_id)->first();
            $feedview = FeedView::firstOrCreate([
                "user_id"=>$user->id,
                "feed_id"=>$feed_id
            ]);
            $count = FeedView::where("feed_id",$feed_id)->count();
            $feed->views = $count;
            $feed->save();
            $feedfavorite = FeedFavorite::where([
                "user_id"=>$user->id,
                "feed_id"=>$feed->id,
                "favorite"=>1
            ])->first();
            $feed->is_favorite = false;
            if($feedfavorite){
                $feed->is_favorite = true;
            }
            $like = FeedLike::where([
                    "user_id"=>Auth::guard('api')->user()->id,
                    "feed_id"=>$feed->id,
                    "like"=>'1'
            ])->first();
            $feed->is_like = false;
            if($like){
                $feed->is_like = true;
            }
            $user_data = User::select(['id', 'name', 'email','phone','profile_image'])->with('profile')->where('id',$feed->user_id)->first();
            $feed->user_data = $user_data;
            return response(['status' =>"success", 'statuscode' => 200, 'message' => __('Add to Favorite'), 'data' =>["feed"=>$feed]], 200);
        } catch (Exception $e) {
           return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500); 
        }
    }

    /**
     * @SWG\Post(
     *     path="/ask-questions",
     *     description="askSupportQuestion",
     * tags={"Ask Question"},
     *     security={
     *     {"Bearer": {}},
     *   },
     * @SWG\Parameter(
     *       name="title",
     *       in="query",
     *       required=true,
     *       description="title", 
     *       type="string" 
     *      ), 
     * @SWG\Parameter(
     *       name="description",
     *       in="query",
     *       required=true,
     *       description="description", 
     *       type="string" 
     *      ),   
     * @SWG\Parameter(
     *       name="amount",
     *       in="query",
     *       required=false,
     *       description="amount ", 
     *       type="string" 
     *      ), 
     * @SWG\Parameter(
     *       name="category_id",
     *       in="query",
     *       required=false,
     *       description="category_id for doctors", 
     *       type="string" 
     *      ),
     * @SWG\Parameter(
     *       name="consultant_id",
     *       in="query",
     *       required=false,
     *       description="consultant_id", 
     *       type="string" 
     *      ), 
     * @SWG\Parameter(
     *       name="package_id",
     *       in="query",
     *       required=false,
     *       description="support package id ", 
     *       type="string" 
     *      ),    
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     *
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function askSupportQuestion(Request $request){
        try {
            $user = Auth::user();
            $rules = [
                'title' => 'required',
                'description' => 'required',
            ];
            if(isset($request->package_id)){
                $rules["package_id"] = "required|exists:master_packages,id";
            }
            if(isset($request->consultant_id)){
                $rules["consultant_id"] = "required|exists:users,id";
            }
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $amount = null;
            $package_id = null;
            if(isset($request->package_id)){
                $package_id = $request->package_id;
                $package = \App\Model\MasterPackage::where('id',$request->package_id)->first();
                if($package->price >0){
                    $amount = $package->price;
                    if($user->wallet->balance<$amount){
                        return response([
                                'status' => "success",
                                'statuscode' => 200,
                                'message' => __('Low Balance'),
                                'data'=>['amountNotSufficient'=>true,
                                'wallet_type'=>'user_wallet']
                            ], 200);
                    }
                }
            }
            $support = Support::create([
                'title'=>$request->title,
                'description'=>$request->description,
                'type'=>'ask_question',
                'created_by'=>$user->id,
                'amount'=>$amount,
                'master_package_id'=>$package_id,
                'transaction_id'=>null
            ]);
            $admin = User::whereHas('roles', function ($query) {
                       $query->where('name','admin');
                    })->first();
            if($support){
                if(Config::get('client_connected') && Config::get('client_data')->domain_name=='heal'){
                    if(isset($request->category_id)){
                        $service_providers = User::whereHas('roles', function ($query) {
                           $query->where('name','service_provider');
                        })->whereHas('categoryserviceProvider',function($query) use($request){
                           $query->where('category_id',$request->category_id);
                        })->get()->take(5);
                        if(count($service_providers)<=0){
                            return response(['status' => "error", 'statuscode' => 400, 'message' =>'No expert found under this category'], 400); 
                        }
                    }else{
                        $service_providers = User::whereHas('roles', function ($query) {
                           $query->where('name','service_provider');
                        })->get()->random(5);
                    }
                    $sp_ids = [];
                    foreach ($service_providers as $key => $service_provider) {
                        if($service_provider){
                            $sp_ids[] = $service_provider->id;
                            SupportAssignee::create([
                                'assigned_to'=>$service_provider->id,
                                'support_id'=>$support->id
                            ]);
                        }
                    }
                    if(count($sp_ids)>0){
                        $notification = new Notification();
                        $notification->push_notification(
                                $sp_ids,
                                    array(
                                    'pushType'=>'FREE_EXPERT_ADVISE',
                                    'request_id'=>$support->id,
                                    'message'=>__("$user->name has asked a free expert advise")
                                )
                         );
                    }
                }else{
                    if(isset($request->consultant_id)){
                        SupportAssignee::create([
                            'assigned_to'=>$request->consultant_id,
                            'support_id'=>$support->id
                        ]);
                        $notification = new Notification();
                        $notification->push_notification(
                                [$request->consultant_id],
                                    array(
                                    'pushType'=>'FREE_EXPERT_ADVISE',
                                    'request_id'=>$support->id,
                                    'message'=>__("$user->name has asked a free expert advise")
                                )
                         );
                    }else{
                        $admin = User::whereHas('roles', function ($query) {
                           $query->where('name','admin');
                        })->first();
                        SupportAssignee::create([
                            'assigned_to'=>$admin->id,
                            'support_id'=>$support->id
                        ]);
                    }
                }
            }
            if($amount>0){
                $status = 'succeeded';
                $withdrawal_to = array(
                    'balance'=>$amount,
                    'transaction_type'=>'asked_question',
                    'user'=>$user,
                    'from_id'=>$admin->id,
                    'module_table'=>'supports',
                    'module_id'=>$support->id,
                    'status'=>$status
                );
                $tra = \App\Model\Transaction::createWithdrawalNew($withdrawal_to);
                $support->transaction_id = $tra->id;
                $support->save();
                // \App\Model\Transaction::createDeposit($deposit_to);
            }
            $support = Support::getUserQuestionFormat2($support->id);
            return response(['status' =>"success", 'statuscode' => 200, 'message' => __('Question Sent to Admin Support'), 'data' =>["question"=>$support]], 200);
        } catch (Exception $e) {
           return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage().' line'.$e->getLine()], 500); 
        }
    }


    /**
     * @SWG\Post(
     *     path="/water-limit",
     *     description="postSetWaterLimit",
     * tags={"Water Intake"},
     *     security={
     *     {"Bearer": {}},
     *   },
     * @SWG\Parameter(
     *       name="limit",
     *       in="query",
     *       required=true,
     *       description="limit in liter", 
     *       type="string" 
     *      ),  
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     *
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function postSetWaterLimit(Request $request){
        try {
            $user = Auth::user();
            $rules = [
                'limit' => 'required',
            ];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $timezone = $request->header('timezone');
            if(!$timezone){
                $timezone = 'Asia/Kolkata';
            }
            $start_date = Carbon::now('UTC')->format('Y-m-d');
            $waterintake = \App\Model\WaterIntake::where(['user_id'=>$user->id])->first();
            if(!$waterintake){
                $waterintake = new \App\Model\WaterIntake();
                $waterintake->user_id = $user->id;
            }
            $waterintake->daily_limit = $request->limit;
            $waterintake->save();

            $DailyWaterDate = \App\Model\DailyWaterDate::where([
                'user_id'=>$user->id,
                'date'=>$start_date
            ])->first();
            if(!$DailyWaterDate){
                $DailyWaterDate = new \App\Model\DailyWaterDate();
                $DailyWaterDate->user_id = $user->id;
                $DailyWaterDate->date = $start_date;
                $DailyWaterDate->total_usage = 0;
            }
            $DailyWaterDate->daily_limit = $request->limit;
            $DailyWaterDate->save();

            $waterintake = \App\Model\WaterIntake::where('id',$waterintake->id)->first();

            $response = $this->getWaterLimitData($user,$start_date);
            return response(['status' =>"success", 'statuscode' => 200, 'message' => __('Done'), 'data' =>$response], 200);
        } catch (Exception $e) {
           return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500); 
        }
    }


    /**
     * @SWG\Get(
     *     path="/water-limit",
     *     description="getWaterLimit",
     * tags={"Water Intake"},
     *     security={
     *     {"Bearer": {}},
     *   },  
     * @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     *
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function getWaterLimit(Request $request){
        try {
            $user = Auth::user();
            $rules = [];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $timezone = $request->header('timezone');
            if(!$timezone){
                $timezone = 'Asia/Kolkata';
            }
            $start_date = Carbon::now("UTC")->format('Y-m-d');
            $response = $this->getWaterLimitData($user,$start_date);
            return response(['status' =>"success", 'statuscode' => 200, 'message' => __('Limit '), 'data' =>$response], 200);
        } catch (Exception $e) {
           return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500); 
        }
    }


    private function getWaterLimitData($user,$start_date){
        $DailyWaterDate =  null;
        $limit = null;
        $today_intake = 0;
        $total_achieved_goal = 0;
        $waterintake = \App\Model\WaterIntake::where(['user_id'=>$user->id])->first();
        if($waterintake){
            $DailyWaterDate = \App\Model\DailyWaterDate::where([
                'user_id'=>$user->id,
                'date'=>$start_date
            ])->first();
            if(!$DailyWaterDate){
                $DailyWaterDate = new \App\Model\DailyWaterDate();
                $DailyWaterDate->user_id = $user->id;
                $DailyWaterDate->date = $start_date;
                $DailyWaterDate->total_usage = 0;
            }
            $DailyWaterDate->daily_limit = $waterintake->daily_limit;
            $DailyWaterDate->save();
            $limit = $waterintake->daily_limit;
        }
        $DailyWaterDates = \App\Model\DailyWaterDate::where([
                'user_id'=>$user->id
        ])->get();
        foreach ($DailyWaterDates as $key => $DailyWater) {
            if($DailyWater->total_usage>=$DailyWater->daily_limit){
                $total_achieved_goal++;
            }
        }
        if($DailyWaterDate){
            $today_intake =  $DailyWaterDate->total_usage;
        }
        return ['limit'=>$limit,'today_intake'=>$today_intake,'total_achieved_goal'=>$total_achieved_goal];
    }    


    /**
     * @SWG\Get(
     *     path="/daily-usage",
     *     description="getDailyUsage",
     * tags={"Water Intake"},
     *     security={
     *     {"Bearer": {}},
     *   },
     * @SWG\Parameter(
     *       name="date_time",
     *       in="query",
     *       required=true,
     *       description="date Y-m-d H:i:s fomat like 2020-11-13 18:29:59", 
     *       type="string" 
     *      ),    
     * @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     *
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function getDailyUsage(Request $request){
        try {
            $user = Auth::user();
            $rules = ['date'=>'required'];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $timezone = $request->header('timezone');
            if(!$timezone){
                $timezone = 'Asia/Kolkata';
            }
            $start_time = Carbon::parse($request->date, $timezone)->setTimezone('UTC')->format('Y-m-d');
            $response = $this->getWaterLimitData($user,$start_time);
            return response(['status' =>"success", 'statuscode' => 200, 'message' => __('Limit '), 'data' =>$response], 200);
        } catch (Exception $e) {
           return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500); 
        }
    }    

    /**
     * @SWG\Post(
     *     path="/drink-water",
     *     description="postDrinkWater",
     * tags={"Water Intake"},
     *     security={
     *     {"Bearer": {}},
     *   },
     * @SWG\Parameter(
     *       name="quantity",
     *       in="query",
     *       required=true,
     *       description="quantity in liter", 
     *       type="string" 
     *      ),    
     * @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     *
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function postDrinkWater(Request $request){
        try {
            $user = Auth::user();
            $rules = ['quantity'=>'required'];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $timezone = $request->header('timezone');
            if(!$timezone){
                $timezone = 'Asia/Kolkata';
            }
            $start_date_time_utc = Carbon::now('UTC')->format('Y-m-d H:i:s');
            $start_date = Carbon::now('UTC')->format('Y-m-d');
            $response = $this->getWaterLimitData($user,$start_date);

            $DailyWaterDate = \App\Model\DailyWaterDate::where([
                'user_id'=>$user->id,
                'date'=>$start_date
            ])->first();
            $DailyGlass = new \App\Model\DailyGlass();
            $DailyGlass->user_id = $user->id;
            $DailyGlass->date_time = $start_date_time_utc;
            $DailyGlass->glass_quantity = $request->quantity; 
            $DailyGlass->save();
            $DailyWaterDate->increment('total_usage',$request->quantity);
            $response = $this->getWaterLimitData($user,$start_date);
            return response(['status' =>"success", 'statuscode' => 200, 'message' => __('Limit '), 'data' =>$response], 200);
        } catch (Exception $e) {
           return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500); 
        }
    }


    /**
     * @SWG\Post(
     *     path="/reply-question",
     *     description="replySupportQuestion",
     * tags={"Ask Question"},
     *     security={
     *     {"Bearer": {}},
     *   },
     * @SWG\Parameter(
     *       name="question_id",
     *       in="query",
     *       required=true,
     *       description="question_id of Ask Question", 
     *       type="string" 
     *      ),   
     * @SWG\Parameter(
     *       name="answer",
     *       in="query",
     *       required=true,
     *       description="description answer", 
     *       type="string" 
     *      ),    
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     *
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function replySupportQuestion(Request $request){
        try {
            $user = Auth::user();
            $rules = [
                'question_id' => 'required|exists:supports,id',
                'answer' => 'required',
            ];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $input = $request->all();
            SupportReply::create([
                'support_id'=>$input['question_id'],
                'answered_by'=>$user->id,
                'description'=>$input['answer']
            ]);
            $support =  Support::where('id',$input['question_id'])->first();
            $support->status = 'answered';
            $support->save();
            $notification = new Notification();
            $notification->push_notification(
                    array($support->created_by),
                        array(
                        'pushType'=>'QUESTION_ANSWERED',
                        'request_id'=>$support->id,
                        'message'=>__("Your question has been answered by $user->name")
                    )
             );
            $support = Support::getUserQuestionFormat2($support->id,$user->id);
            return response([
                'status' =>"success",
                'statuscode' => 200,
                'message' => __("Answered"),
                'data' =>["question"=>$support]
            ], 200);
        } catch (Exception $e) {
           return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500); 
        }
    }
    

    /**
     * @SWG\Get(
     *     path="/ask-questions",
     *     description="getaskSupportQuestion",
     * tags={"Ask Question"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="before",
     *         in="query",
     *         type="string",
     *         description="before id",
     *         required=false,
     *     ),
     *  @SWG\Parameter(
     *         name="after",
     *         in="query",
     *         type="string",
     *         description="after id",
     *         required=false,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK questions",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getaskSupportQuestion(Request $request){
        try {
            $user = Auth::user();
            $per_page = (isset($request->per_page)?$request->per_page:10);
            $user_id = null;
            if($user->hasrole('service_provider')){
                $user_id = $user->id;
                $questions = Support::select('*')
                ->whereHas('support', function ($query) use($user) {
                    $query->where('assigned_to',$user->id);
                })
                ->where([
                    'type'=>'ask_question'
                ])
                ->orderBy('id', 'desc')
                ->cursorPaginate($per_page);
            }else{
                $questions = Support::select('*')->where([
                    'type'=>'ask_question',
                    'created_by'=>$user->id
                ])
                ->orderBy('id', 'desc')
                ->cursorPaginate($per_page);
            }
            // print_r($user_id);die;
            foreach ($questions as $key => $question) {
                $question =  Support::getUserQuestionFormat($question,$user_id);
            }
            $after = null;
            if($questions->meta['next']){
                $after = $questions->meta['next']->target;
            }
            $before = null;
            if($questions->meta['previous']){
                $before = $questions->meta['previous']->target;
            }
            $can_ask_question = false;
            $can_ask_question = \App\Model\Support::checkCanCreateQuestion($user->id);
            $per_page = $questions->perPage();
            return response([
                'status' => "success",
                'statuscode' => 200,
                'message' => __('Questions Listing'),
                'data' =>['questions'=>$questions->items(),'after'=>$after,'before'=>$before,'per_page'=>$per_page,'can_ask_question'=>$can_ask_question]],
                200);
        } catch (Exception $e) {
           return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500); 
        }
    } 


    /**
     * @SWG\Get(
     *     path="/ask-question-detail",
     *     description="getaskSupportQuestionDetail",
     * tags={"Ask Question"},
     *     security={
     *     {"Bearer": {}},
     *   },
     *  @SWG\Parameter(
     *         name="question_id",
     *         in="query",
     *         type="string",
     *         description="question_id",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK questions",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Please provide all data"
     *     )
     * )
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getaskSupportQuestionDetail(Request $request){
        try {
            $user = Auth::user();
            $rules = [
                'question_id' => 'required|exists:supports,id',
            ];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return response(array('status' => "error", 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }
            $user_id = null;
            if($user->hasrole('service_provider')){
                $user_id = $user->id;
            }
            $question = Support::getUserQuestionFormat2($request->question_id,$user_id);
            return response([
                'status' => "success",
                'statuscode' => 200,
                'message' => __('Question Detail'),
                'data' =>['question'=>$question]],
                200);
        } catch (Exception $e) {
           return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getMessage()], 500); 
        }
    }



}
