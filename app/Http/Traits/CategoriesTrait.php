<?php
namespace App\Http\Traits;
use App\Model\Category;
use App\Model\ConsultClass;
use App\Model\Service;
use App\User;
use Carbon\Carbon;
use DateTime,DateTimeZone;
trait CategoriesTrait {
    public function parentCategories() {
        // Get all the brands from the Brands Table.
        $parentCategories = Category::where('name','!=','Find Local Resources')->where('parent_id',NULL)->where('enable','=','1')->get();
        return $parentCategories;
    }

    public function getAllCategories() {
        $parentCategories = Category::where('enable','=','1')->orderBy('name','ASC')->get();
        foreach ($parentCategories as $key => $value) {
            $value->is_filter = false;
            $value->filters = \App\Model\FilterType::getFiltersOptionsByCategory($value->id);
            if(count($value->filters)>0){
                $value->is_filter = true;
            }
        }
        return $parentCategories;
    }

    public function serviceProviders() {
        $sr = User::whereHas('roles', function ($query) {
                           $query->where('name','service_provider');
                        })->get();
        return $sr;
    } 

    public function services() {
        $sr = Service::get();
        return $sr;
    }

    public function consultClasses() {
    	$dateznow = new DateTime("now", new DateTimeZone('UTC'));
        $datenow = $dateznow->format('Y-m-d H:i:s');
        $consultclasses = ConsultClass::where('booking_date','>',$datenow)->get();
        return $consultclasses;
    }
}