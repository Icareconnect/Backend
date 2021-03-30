<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CategoryServiceType extends Model
{
    //

    public function category()
    {
        return $this->hasOne('App\Model\Category','id','category_id');
    }
    public function service()
    {
        return $this->hasOne('App\Model\Service','id','service_id');
    }

    public static function getSessionPrice($category_id){
    	$cat = Self::where('category_id',$category_id)->first();
    	if($cat)
    		return $cat->price_fixed;
    	else
    		return 0;
    }

    public static function createServiceByCategory($service_id,$category_id,$price,$min=null,$max=null){
    	  $categoryservicetype = Self::where(['service_id'=>$service_id,'category_id'=>$category_id])->first();
    	  if(!$categoryservicetype){
	      	  $categoryservicetype = new Self();
		      $categoryservicetype->service_id = $service_id;
		      $categoryservicetype->category_id = $category_id;
    	  }
	      $categoryservicetype->is_active = '1';
	      $categoryservicetype->gap_duration = 10;
	      $categoryservicetype->minimum_duration = 30;
	      $categoryservicetype->price_fixed = $price;
	      $categoryservicetype->price_minimum = $min;
	      $categoryservicetype->price_maximum = $max;
	      $categoryservicetype->save();
	      return $categoryservicetype;
    }
}
