<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Model\UserMasterPreference;
use Carbon\Carbon;
class MasterPreference extends Model
{
    use SoftDeletes;
	/**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['deleted_at'];
    //
    protected $fillable = ['id'];

    public function options()
    {
        return $this->hasMany('App\Model\MasterPreferencesOption','preference_id','id')->select(['id', 'name as option_name','preference_id','image','description'])->orderBy('option_name','ASC');
    }
    // public function symptom_options()
    // {
    //     return $this->hasMany('App\Model\MasterPreferencesOption','preference_id','id')->select(['id', 'name as option_name','preference_id','image','description']);
    // }

    public static function getMasterPreferences($user_id){
        $filters = [];
        $raw_filters = self::with(['options' => function($query) {
            return $query->select(['id', 'name as option_name','preference_id','image','description']);
        }])->get();
       foreach ($raw_filters as $key => $filter) {
            if($filter->options->count()>0){
               if($user_id!=null){
                  $options = UserMasterPreference::where(['user_id'=>$user_id,'preference_id'=>$filter->id])->pluck('preference_option_id')->toArray();
                   if(count($options)>0){
                      $data = MasterPreferencesOption::select(['id', 'name as option_name','preference_id','image','description'])->whereIn('id',$options)->get();
                      foreach ($data as $key => $d) {
                        $d->isSelected = true;
                      }
                      $filters[] = array(
                        'id'=>$filter->id,
                        'preference_name'=>$filter->name,
                        'is_multi'=>(string)$filter->is_multi,
                        'options'=>$data,
                        'preference_type'=>$filter->type,
                       ); 
                   }
               }else{
                 $filters[] = array(
                  'id'=>$filter->id,
                  'preference_name'=>$filter->name,
                  'is_multi'=>(string)$filter->is_multi,
                  'options'=>$filter->options,
                  'preference_type'=>$filter->type,
                 ); 
               }
            }
       }
       return $filters;
    }


    public static function getMasterPreferencesByType($user_id,$preference_type,$timezone){
        $filters = [];
        $raw_filters = self::with(['options' => function($query) {
            return $query->select(['id', 'name as option_name','preference_id','image','description']);
        }])->where('type',$preference_type)->get();
       foreach ($raw_filters as $key => $filter) {
            if($filter->options->count()>0){
                  $options = UserMasterPreference::where(['user_id'=>$user_id,'preference_id'=>$filter->id])->pluck('preference_option_id')->toArray();
                   if(count($options)>0){
                      $data = MasterPreferencesOption::select(['id', 'name as option_name','preference_id','image','description'])->whereIn('id',$options)->get();
                      foreach ($data as $key => $d) {
                        $ump = UserMasterPreference::where([
                          'user_id'=>$user_id,
                          'preference_id'=>$filter->id,
                          'preference_option_id'=>$d->id
                        ])->orderBy('updated_at','DESC')->first();
                        $d->isSelected = true;
                        $d->createdAt = Carbon::parse($ump->created_at,$timezone)->setTimezone('UTC')->diffForHumans();
                      }
                      $filters[] = array(
                        'id'=>$filter->id,
                        'preference_name'=>$filter->name,
                        'is_multi'=>(string)$filter->is_multi,
                        'options'=>$data,
                        'preference_type'=>$filter->type,
                       ); 
                   }
            }
       }
       return $filters;
    }

    // private function getFilters(){
    //     foreach ($raw_filters as $key => $filter) {
    //         if($filter->options->count()>0){
    //               $options = UserMasterPreference::where(['user_id'=>$user_id,'preference_id'=>$filter->id])->pluck('preference_option_id')->toArray();
    //                if(count($options)>0){
    //                   $data = MasterPreferencesOption::select(['id', 'name as option_name','preference_id','image','description'])->whereIn('id',$options)->get();
    //                   foreach ($data as $key => $d) {
    //                     $d->isSelected = true;
    //                   }
    //                   $filters[] = array(
    //                     'id'=>$filter->id,
    //                     'preference_name'=>$filter->name,
    //                     'is_multi'=>(string)$filter->is_multi,
    //                     'options'=>$data,
    //                     'preference_type'=>$filter->type,
    //                    ); 
    //                }
    //         }
    //    }
    // }

    public static function getMasterPreferencesByRequest($user_id,$request_id,$type){
        $filters = [];
        $raw_filters = self::with(['options' => function($query) {
            return $query->select(['id', 'name as option_name','preference_id','image','description']);
        }])->where(['type'=>$type])->get();
       foreach ($raw_filters as $key => $filter) {
            if($filter->options->count()>0){
               if($user_id!=null){
                  $options = UserMasterPreference::where(['user_id'=>$user_id,'preference_id'=>$filter->id,'request_id'=>$request_id])->pluck('preference_option_id')->toArray();
                   if(count($options)>0){
                      $data = MasterPreferencesOption::select(['id', 'name as option_name','preference_id','image','description'])->whereIn('id',$options)->get();
                      foreach ($data as $key => $d) {
                        $d->isSelected = true;
                      }
                      $filters[] = array(
                        'id'=>$filter->id,
                        'preference_name'=>$filter->name,
                        'is_multi'=>(string)$filter->is_multi,
                        'options'=>$data,
                        'preference_type'=>$filter->type,
                       ); 
                   }
               }else{
                 $filters[] = array(
                  'id'=>$filter->id,
                  'preference_name'=>$filter->name,
                  'is_multi'=>(string)$filter->is_multi,
                  'options'=>$filter->options,
                  'preference_type'=>$filter->type,
                 ); 
               }
            }
       }
       return $filters;
    }

    public static function getMasterPreferencesDuty($user_id,$filter_ids){
        $filters = [];
        $selected = UserMasterPreference::where(['preference_option_id'=>$value->id,'user_id'=>$user_id,'preference_id'=>$value->preference_id])->first();
        $raw_filters = self::with(['options' => function($query) {
            return $query->select(['id', 'name as option_name','preference_id','image','description']);
        }])->where('type','duty')->get();
       foreach ($raw_filters as $key => $filter) {
            if($filter->options->count()>0){
               if($user_id!=null){
                   foreach ($filter->options as $key => $value) {
                        if($selected)
                            $value->isSelected = true;
                   }
               }
               $filters[] = array(
                'id'=>$filter->id,
                'preference_name'=>$filter->name,
                'is_multi'=>(string)$filter->is_multi,
                'options'=>$filter->options,
                'preference_type'=>$filter->type,
               ); 
            }
       }
       return $filters;
    }

    public static function getUsersByMasterPre($user_id,$consultant_ids){
        $preference_option_id =  \App\Model\UserMasterPreference::where([
          'user_id'=>$user_id
        ])->pluck('preference_option_id')->toArray();
        $map_option_ids = \App\Model\MasterPreferencesOption::whereHas('masterpreference',function($query){
            $query->where([
              'type'=>'work_environment',
            ]);
          })->whereIn('id',$preference_option_id)->where('map_option_id','!=',null)->pluck('map_option_id')->toArray();
        if(count($map_option_ids)>0){
          foreach ($map_option_ids as $map_option_id) {
              $exist =  \App\Model\UserMasterPreference::whereIn('user_id',$consultant_ids)->where('preference_option_id',$map_option_id)->pluck('user_id')->toArray();
              $consultant_ids = $exist;
          }
        }else{
            return $consultant_ids;
        }
        return $consultant_ids;
    }
    public static function getUsersByMasterPreById($id,$consultant_ids){
        $map_option = \App\Model\MasterPreferencesOption::where('id',$id)->first();
        $map_option_data = \App\Model\MasterPreferencesOption::whereHas('masterpreference',function($query){
            $query->where([
              'type'=>'work_environment',
              'show_on'=>'sp',
              'deleted_at'=>NULL
            ]);
          })->where('name',$map_option->name)->first();
        $consultant_ids =  \App\Model\UserMasterPreference::whereIn('user_id',$consultant_ids)->where('preference_option_id',$map_option_data->id)->pluck('user_id')->toArray();
        return $consultant_ids;
    }
}
