<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Category extends Model
{
	 protected $fillable = ['parent_id', 'name','image','description','multi_select','color_code','image_icon'];

	 use SoftDeletes;
	/**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['deleted_at'];
    
    public function subcategory(){
        return $this->hasMany('App\Model\Category', 'parent_id');
    }
    public function parent(){
        return $this->hasOne('App\Model\Category','id','parent_id');
    }

    public function filters(){
        return $this->hasMany('App\Model\FilterType', 'category_id');
    }
    public function additionals(){
        return $this->hasMany('App\Model\AdditionalDetail', 'category_id');
    }
    public function services(){
        return $this->hasMany('App\Model\CategoryServiceType', 'category_id');
    }
    
    public function getColorCodeAttribute($value) {
        if($value){
            return '#'.$value;
        }else{
            return null;
        }
    }

   }
