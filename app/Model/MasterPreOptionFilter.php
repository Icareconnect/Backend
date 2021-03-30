<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\FilterType;
class MasterPreOptionFilter extends Model
{
    protected $fillable = ['option_id', 'module_table','module_id'];


    public static  function getFiltersByDuties($duties){
    	$filters = \App\Model\MasterPreOptionFilter::whereIn('option_id',$duties)
    	->where(['module_table'=>'filter_options'])
    	->groupBy('module_id')->pluck('module_id')->toArray();
        return FilterType::getFiltersByIds($filters);
    }
}
