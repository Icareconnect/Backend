<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\CustomInfo;
class Family extends Model
{


    public function getInsurnceData($user_id){
        $insurances = \App\Model\PatientInsurance::select('insurance_id','user_id')->where('user_id',$user_id)->get();
        foreach ($insurances as $key => $insurance) {
            if($insurance){
                $insurance->name = $insurance->insurance->name;
                $insurance->id = $insurance->insurance->id;
                unset($insurance->insurance);
                unset($insurance->insurance_id);
            }
        }
        return $insurances;
    }
	/**
    * User Cards
    * @param 
    */
    public static function familyData($family_id)
    {
    	$family = self::where(['id'=>$family_id])->first();
    	if($family){
    		$family->medical_allergies = null;
    		$family->chronic_diseases = null;
    		$family->previous_surgeries = null;
    		$family->previous_medication = null;
    		$family->country_code = null;
    		$family->phone = null;
    		$family->email = null;
    		$family->patient_type = null;
    		$ins_info = \App\Model\CustomInfo::where([
                'ref_table_id'=>$family->id,
                'ref_table'=>'families',
                'info_type'=>'family_info'
            ])->first();
            $family->insurances = $family->getInsurnceData($family->id);
            $insurance_data = [];
            $insurance_infos = \App\Model\CustomInfo::where([
                    'info_type'=>'user_insurance_info',
                    'ref_table'=>'paient',
                    'ref_table_id'=>$family->id,
                ])->get();
            foreach ($insurance_infos as $key => $insurance_info) {
                $insurance_data [] = json_decode($insurance_info->raw_detail);
            }
            $family->insurance_info = $insurance_data;
            if($ins_info && $ins_info->raw_detail){
            	$raw_detail = json_decode($ins_info->raw_detail,true);
            	$family->medical_allergies = isset($raw_detail['medical_allergies'])?$raw_detail['medical_allergies']:null;
	            $family->chronic_diseases = isset($raw_detail['chronic_diseases'])?$raw_detail['chronic_diseases']:null;
                $family->chronic_diseases_desc = isset($raw_detail['chronic_diseases_desc'])?$raw_detail['chronic_diseases_desc']:null;
	            $family->previous_surgeries = isset($raw_detail['previous_surgeries'])?$raw_detail['previous_surgeries']:null;
	            $family->previous_medication = isset($raw_detail['previous_medication'])?$raw_detail['previous_medication']:null;
	            $family->country_code = isset($raw_detail['country_code'])?$raw_detail['country_code']:null;
            	$family->phone = isset($raw_detail['phone'])?(int)$raw_detail['phone']:null;
            	$family->email = isset($raw_detail['email'])?$raw_detail['email']:null;
            	$family->patient_type = isset($raw_detail['patient_type'])?$raw_detail['patient_type']:null;
            }
    	}
    	return $family;
    }

    public static function getFamiliesByUser($user_id){
    	$detail = [];
    	$family_ids = self::where('user_id',$user_id)->orderBy('id','DESC')->pluck('id')->toArray();
    	foreach ($family_ids as $key => $family_id) {
    		$detail[] = self::familyData($family_id);
    	}
    	return $detail;
    }
}
