<?php

use Illuminate\Database\Seeder;

class EnableServiceTableData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $twilio = App\Model\ServiceCredential::where('name','twilio')->first();
         if(!$twilio){
        	$servicecredential = new App\Model\ServiceCredential();
        		$servicecredential->name = 'twilio';
        	$servicecredential->save();
         }
         $exotel = App\Model\ServiceCredential::where('name','exotel')->first();
         if(!$exotel){
        	$servicecredential = new App\Model\ServiceCredential();
        		$servicecredential->name = 'exotel';
        	$servicecredential->save();
         }
        $charges = App\Model\EnableService::where('type','charges')->first();
        if(!$charges){
        	$enableservice = new App\Model\EnableService();
        	$enableservice->type = 'charges';
        	$enableservice->key_name = 'amount';
        	$enableservice->value = '10';
        	$enableservice->save();
        }

        $charges = App\Model\EnableService::where('type','audio/video')->first();
        if(!$charges){
        	$enableservice = new App\Model\EnableService();
        	$enableservice->type = 'audio/video';
        	$enableservice->key_name = 'NA';
        	$enableservice->save();
        }

        $charges = App\Model\EnableService::where('type','class_calling')->first();
        if(!$charges){
            $enableservice = new App\Model\EnableService();
            $enableservice->type = 'class_calling';
            $enableservice->key_name = 'NA';
            $enableservice->save();
        }

        $insurance = App\Model\EnableService::where('type','insurance')->first();
        if(!$insurance){
            $enableservice = new App\Model\EnableService();
            $enableservice->type = 'insurance';
            $enableservice->key_name = 'NA';
            $enableservice->value = 'no';
            $enableservice->save();
        }

        $unit_price = App\Model\EnableService::where('type','unit_price')->first();
        if(!$unit_price){
            $enableservice = new App\Model\EnableService();
            $enableservice->type = 'unit_price';
            $enableservice->key_name = 'minute';
            $enableservice->value = '1';
            $enableservice->save();
        }

        $slot_duration = App\Model\EnableService::where('type','slot_duration')->first();
        if(!$slot_duration){
            $enableservice = new App\Model\EnableService();
            $enableservice->type = 'slot_duration';
            $enableservice->key_name = 'minute';
            $enableservice->value = '30';
            $enableservice->save();
        }
        $default_approved = App\Model\EnableService::where('type','vendor_approved')->first();
        if(!$default_approved){
            $enableservice = new App\Model\EnableService();
            $enableservice->type = 'vendor_approved';
            $enableservice->key_name = 'auto_approved';
            $enableservice->value = 'yes';
            $enableservice->save();
        }
        $default_approved = App\Model\EnableService::where('type','currency')->first();
        if(!$default_approved){
            $enableservice = new App\Model\EnableService();
            $enableservice->type = 'currency';
            $enableservice->key_name = 'NA';
            $enableservice->value = 'INR';
            $enableservice->save();
        }
    }
}
