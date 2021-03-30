<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class FilterType extends Model
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
    /**
    * User Profile
    * @param 
    */
    public function category()
    {
        return $this->hasOne('App\Model\Category','id','category_id');
    }

    public function options()
    {
        return $this->hasMany('App\Model\FilterTypeOption','filter_type_id','id');
    }

    public static function getFiltersByCategory($category_id,$user_id=null){
        $filters = [];
        $raw_filters = self::where('category_id',$category_id)
        ->with(['options' => function($query) {
            return $query->select(['id', 'option_name','filter_type_id','image','description','video','banner','price'])->orderBy('option_name','ASC');
        }])->get();
       foreach ($raw_filters as $key => $filter) {
            if($filter->options->count()>0){
               if($user_id!=null){
                   foreach ($filter->options as $key => $value) {
                        $selected = ServiceProviderFilterOption::where(['filter_option_id'=>$value->id,'sp_id'=>$user_id])->first();
                        if($selected)
                            $value->isSelected = true;
                   }
               }
               $filters[] = array(
                'id'=>$filter->id,
                'category_id'=>$filter->category_id,
                'filter_name'=>$filter->filter_name,
                'preference_name'=>$filter->preference_name,
                'is_multi'=>$filter->is_multi,
                'options'=>$filter->options,
               ); 
            }
       }
       return $filters;
    }
    public static function getSelectedFiltersByCategory($category_id,$user_id){
        $filters = [];
        $raw_filters = self::where('category_id',$category_id)
        ->with(['options' => function($query) {
            return $query->select(['id', 'option_name','filter_type_id','image','description','video','banner','price'])->orderBy('option_name','ASC');
        }])->get();
       foreach ($raw_filters as $key => $filter) {
            if($filter->options->count()>0){
               if($user_id!=null){
                   foreach ($filter->options as $key => $value) {
                        $selected = ServiceProviderFilterOption::where(['filter_option_id'=>$value->id,'sp_id'=>$user_id])->first();
                        if($selected){
                            $value->isSelected = true;
                            $filters[] = $value;
                          }
                   }
               }
            }
       }
       return $filters;
    }

    public static function getUserFiltersNameByCategory($category_id,$user_id=null){
        $filters = [];
        $raw_filters = self::where('category_id',$category_id)
        ->with(['options' => function($query) {
            return $query->select(['id', 'option_name','filter_type_id','image','description','video','banner','price'])->orderBy('option_name','ASC');
        }])->get();
       foreach ($raw_filters as $key => $filter) {
            if($filter->options->count()>0){
               if($user_id!=null){
                   foreach ($filter->options as $key => $value) {
                        $selected = ServiceProviderFilterOption::where(['filter_option_id'=>$value->id,'sp_id'=>$user_id])->first();
                        if($selected)
                          $filters[] = $value->option_name;
                   }
               }
            }
       }
       return $filters;
    }

    public static function getFiltersOptionsByCategory($category_id){
        $filters = [];
        $raw_filters = self::where('category_id',$category_id)
        ->with(['options' => function($query) {
            return $query->select(['id', 'option_name','filter_type_id','image','description','video','banner','price'])->orderBy('option_name','ASC');
        }])->get();
       foreach ($raw_filters as $key => $filter) {
            if($filter->options->count()>0){
               foreach ($filter->options as $key2 => $op) {
                 $filters[] = array(
                  'id'=>$filter->id,
                  'category_id'=>$filter->category_id,
                  'filter_name'=>$filter->filter_name,
                  'preference_name'=>$filter->preference_name,
                  'is_multi'=>$filter->is_multi,
                  'data'=>$op,
                 ); 
               }
            }
       }
       return $filters;
    }

    public static function getFiltersByIds($ids){
        $filters = [];
        $raw_filters = self::with(['options' => function($query) use($ids) {
            return $query->select(['id', 'option_name','filter_type_id','image','description','video','banner','price'])->orderBy('option_name','ASC')->whereIn('id',$ids);
        }])->get();
       foreach ($raw_filters as $key => $filter) {
            if($filter->options->count()>0){
               $filters[] = array(
                'id'=>$filter->id,
                'category_id'=>$filter->category_id,
                'filter_name'=>$filter->filter_name,
                'preference_name'=>$filter->preference_name,
                'is_multi'=>$filter->is_multi,
                'options'=>$filter->options,
               ); 
            }
       }
       return $filters;
    }
}
