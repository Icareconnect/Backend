<?php

namespace App\Imports;

use App\User;
use App\Model\Role;
use App\Model\City;
use App\Model\Plan;
use App\Model\State;
use App\Model\Wallet;
use App\Model\Profile;
use App\Model\Country;
use App\Model\Category;
use App\Model\Insurance;
use App\Model\CustomField;
use App\Model\EnableService;
use App\Model\SubscribePlan;
use App\Model\UserInsurance;
use App\Model\ServiceProviderSlot;
use App\Model\CustomUserField;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use App\Model\CategoryServiceProvider;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DateTime,DateTimeZone;

class UsersImport implements ToCollection,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $user = User::where('email', $row['email'])->first();
            if(!$user){
                $user =User::create([
                       'name'     => isset($row['name'])?$row['name']:'unknown',
                       'email'    => $row['email'], 
                       'password' => Hash::make(isset($row['password']) ? $row['password'] :'password')
                ]);
                $wallet = new Wallet();
                $wallet->balance = 0;
                $wallet->user_id = $user->id;
                $wallet->save();
                $role = Role::where('name','service_provider')->first();
                if($role){
                    $user->roles()->attach($role);
                }
            }
            if($user){
                $dateznow = new DateTime("now", new DateTimeZone('UTC'));
                $datenow = $dateznow->format('Y-m-d H:i:s');
                $user->account_verified = $datenow;
                if(isset($row['manual_available'])){
                    $user->manual_available = $row['manual_available'];
                }
                if(isset($row['npi_id'])){
                    $user->npi_id = $row['npi_id'];
                }
                $user->save();
                $city_detail= City::where('name', $row['city'])->first();
                $state_detail= State::where('name', $row['state'])->first();
                $country_detail= Country::where('sortname', 'US')->first();
                if(!$user->profile){
                    $profile = New Profile();
                    $profile->user_id = $user->id;
                    $profile->dob = '0000-00-00';
                    $profile->save();
                }else{
                    $profile = Profile::where('user_id',$user->id)->first();
                }
                if(isset($row['dob'])){
                    $profile->dob = $row['dob'];
                }
                if(isset($row['lat'])){
                    $profile->lat = $row['lat'];
                }
                if(isset($row['long'])){
                    $profile->long = $row['long'];
                }
                if(isset($row['dob'])){
                    $profile->dob = $row['dob'];
                }
                $profile->address = isset($row['address'])? $row['address']:null;
                $profile->city = $city_detail ? $city_detail->id : $row['city'];
                $profile->state = $state_detail ? $state_detail->id : $row['state'];
                $profile->country = $country_detail? $country_detail->id : $row['country'];
                $profile->save();
                if(isset($row['subscription'])){
                    $expired_on = \Carbon\Carbon::now()->addMonth(1)->format('Y-m-d H:i:s');
                    $plan_detail = Plan::where('name', $row['subscription'])->first();
                    if($plan_detail){
                        $new_subscribe = SubscribePlan::firstOrCreate([
                            'plan_id' => $plan_detail->id,
                            'user_id' => $user->id
                        ]);
                        $new_subscribe->expired_on = $expired_on;
                        $new_subscribe->save();
                    }
                }
                $zip_code = CustomField::where(['field_name' => 'Zip Code', 'user_type' => 2])->first();
                if ($zip_code) {
                    CustomUserField::where('user_id', $user->id)->where('custom_field_id', $zip_code->id)->delete();
                    $CustomUserField = new CustomUserField();
                    $CustomUserField->user_id = $user->id;
                    $CustomUserField->field_value = $row['zip_code'];
                    $CustomUserField->custom_field_id = $zip_code->id;
                    $CustomUserField->save();
                }
                if (isset($row['insurance']) && $row['insurance']) {
                    UserInsurance::where('user_id', $user->id)->delete();
                    $insurances = explode(',', $row['insurance']);
                    foreach ($insurances as $insurance) {
                        $result = Insurance::where('name', $insurance)->first();
                        if($result){
                            $userinsurance = new UserInsurance();
                            $userinsurance->user_id = $user->id;
                            $userinsurance->insurance_id = $result->id;
                            $userinsurance->save();
                        }
                    }
                }

                $category = Category::where('name', $row['category'])->first();
                if($category){
                    $category_service = CategoryServiceProvider::where(['sp_id' => $user->id])->first();
                    if (!$category_service) {
                        $category_service =  new CategoryServiceProvider();
                        $category_service->sp_id = $user->id;
                    }
                    $category_service->category_id = $category->id;
                    $category_service->save();
                    $current_category = $category->id;
                    $service_id = 1;
                    $duration = '60';
                    $unit_price = EnableService::where('type','unit_price')->first();
                    if($unit_price){
                        $duration = $unit_price->value * 60;
                    }
                    $service = \App\Model\CategoryServiceType::where([
                        'category_id'=>$current_category,
                        'service_id'=>$service_id
                    ])->first();
                    if($service){
                        $spservicetype = \App\Model\SpServiceType::firstOrCreate([
                            'sp_id'=>$user->id,
                            'category_service_id'=>$service->id
                        ]);
                        $spservicetype->available = "1";
                        $spservicetype->minimmum_heads_up = "5";
                        if($service->price_fixed!==null){
                            $spservicetype->price = $service->price_fixed;
                        }else{
                            $spservicetype->price = 0;
                        }
                        $spservicetype->duration = $duration;
                        $spservicetype->save();
                    }
                    $weekdays = [1, 2, 3, 4, 5];
                    ServiceProviderSlot::where([
                        'service_provider_id' => $user->id,
                        'service_id' => $service_id,
                        'category_id' => $current_category,
                    ])->whereIn('day', $weekdays)->delete();
                    foreach ($weekdays as $day) {
                        $spavailability = new ServiceProviderSlot();
                        $spavailability->service_provider_id = $user->id;
                        $spavailability->service_id = $service_id;
                        $spavailability->category_id = $current_category;
                        $spavailability->start_time = '00:00:00';
                        $spavailability->end_time = '23:59:59';
                        $spavailability->day = $day;
                        $spavailability->save();
                    }
                }

                $user->account_step = 6;
                $user->save();
            }
        }
        return true;
    }
}
