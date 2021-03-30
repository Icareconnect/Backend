<?php

namespace App;
use Illuminate\Support\Str;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Helpers\Helper;
use App\Model\Role;
use App\Model\EnableService;
use App\Model\Card;
use App\Model\SpCourse;
use App\Model\BankAccount;
use App\Model\Wallet;
use App\Model\Feedback,App\Model\FilterType,App\Model\SpServiceType;
use App\Model\CategoryServiceType;
use App\Model\CategoryServiceProvider;
use Cartalyst\Stripe\Stripe;
use Exception;
use App\Model\AdditionalDetail;
use App\Model\Profile;
use App\Model\UserInsurance;
use App\Model\SpAdditionalDetail;
use App\Model\CustomUserField;
use Config;
use DateTime,DateTimeZone;
use Carbon\Carbon;
// use NotificationChannels\WebPush\HasPushSubscriptions;
class User extends Authenticatable
{
    use HasApiTokens,Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email','user_name', 'password','phone','fcm_id','profile_image','email_verified_at','country_code','insurance_enable','manual_available','npi_id','source'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function account()
    {
        return $this->hasOne('App\Model\BankAccount','user_id');
    }

    // public function reference()
    // {
    //     return $this->hasOne('App\Model\User','reference_by');
    // }

    public function roles(){
      return $this->belongsToMany(Role::class);
    }

    public function getAccountVerifiedAttribute($value) {
        if($value){
            return true;
        }else{
            return false;
        }
    }
    public function getAccountRejectedAttribute($value) {
        if($value){
            return true;
        }else{
            return false;
        }
    }
    public function getAccountActiveAttribute($value) {
        if($value){
            return true;
        }else{
            return false;
        }
    }
    public function getInsuranceVerifiedAttribute($value) {
        if($value){
            return true;
        }else{
            return false;
        }
    }

    public function getManualAvailableAttribute($value) {
        if($value){
            return true;
        }else{
            return false;
        }
    }

    public function getNotificationEnableAttribute($value) {
        if($value){
            return true;
        }else{
            return false;
        }
    }

    public function getPremiumEnableAttribute($value) {
        if($value){
            return true;
        }else{
            return false;
        }
    }

    // public function getInsuranceEnableAttribute($value) {
    //     if($value=="1"){
    //         return true;
    //     }else{
    //         return false;
    //     }
    // }

    /**
    * @param string|array $roles
    */
    public function authorizeRoles($roles){
      if (is_array($roles)) {
          return $this->hasAnyRole($roles) || 
                 abort(401, 'This action is unauthorized.');
      }
      return $this->hasRole($roles) || 
             abort(401, 'This action is unauthorized.');
    }
    /**
    * Check multiple roles
    * @param array $roles
    */
    public function hasAnyRole($roles){
      return null !== $this->roles()->whereIn('name', $roles)->first();
    }
    /**
    * Check one role
    * @param string $role
    */
    public function hasRole($role){
      return null !== $this->roles()->where('name', $role)->first();
    }

    /**
    * Check one role
    * @param string $userid
    */
    public function isDoctor($userid){
      return null !== $this->roles()->where(['name'=>'service_provider','user_id'=>$userid])->first();
    }

    /**
    * User Profile
    * @param 
    */
    public function profile()
    {
        return $this->hasOne('App\Model\Profile','user_id')->select(['*','country as country_id','state as state_id','city as city_id']);
    }
    /**
    * User Wallet
    * @param 
    */
    public function wallet()
    {
        return $this->hasOne('App\Model\Wallet','user_id');
    }

    /**
    * User Cards
    * @param 
    */
    public function cards()
    {
        return $this->hasMany('App\Model\Card','user_id');
    }

    /**
    * User Subscription
    * @param 
    */


    public function categoryserviceProvider(){

        return $this->hasOne('App\Model\CategoryServiceProvider','sp_id');
    }


    public function Customuserfield(){

        return $this->hasOne('App\Model\CustomUserField','user_id');

    }
    public function userinsurances(){

        return $this->hasMany('App\Model\UserInsurance','user_id');

    }

    public function getSubscription($user){
        $services = [];
        $subscriptions = \App\Model\Subscription::select('service_id','charges','duration')->with('service_data')->where('consultant_id',$user->id)->get();
        foreach ($subscriptions as $key => $subscription) {
            $subscription->type = $subscription->service_data->type;
            unset($subscription->service_data);
        }
        return $subscriptions;
    }

    /**
    * User Subscription
    * @param 
    */
    public function getServices($sp_id){
        $subscriptions = [];
        $user = self::where('id',$sp_id)->first();
        $cat = \App\Model\CategoryServiceProvider::where('sp_id',$sp_id)->first();
        if(!$cat){
            return $subscriptions;
        }
        $categoryservicetype = CategoryServiceType::where('category_id',$cat->category_id)->pluck('id')->toArray();
        if(count($categoryservicetype)<1){
            return $subscriptions;
        }
        $subscriptions = \App\Model\SpServiceType::where([
            'sp_id'=>$sp_id,
            'available'=>'1'
        ])->whereIn('category_service_id',$categoryservicetype)->get();
        foreach ($subscriptions as $key => $subscription) {
            if($subscription){
                $raw_detail = \App\Model\CustomInfo::where([
                    'info_type'=>'service_address',
                    'ref_table'=>'sp_service_types',
                    'ref_table_id'=>$subscription->id,
                ])->first();
                $subscription->clinic_address = null;
                if($raw_detail){
                    $subscription->clinic_address = json_decode($raw_detail->raw_detail);
                }
               $unit_price = EnableService::where('type','unit_price')->first();
               $slot_duration = EnableService::where('type','slot_duration')->first();
               $subscription->unit_price = $unit_price->value * 60; 
               if(Config::get('client_connected') && Config::get("client_data")->domain_name=="healtcaremydoctor"){
                    $subscription->fixed_price = false; 
                    if($subscription->category_service_type->price_fixed){
                        $subscription->fixed_price = true; 
                        $subscription->price = $subscription->category_service_type->price_fixed; 
                        $subscription->unit_price = $slot_duration->value * 60; 
                    }
               }
               if(Config::get('client_connected') && Config::get("client_data")->domain_name=="intely"){
                    $subscription->fixed_price = false;
                    $user->selected_filter_options = $user->getSelectedFiltersByCategory($user->id); 
                    if(isset($user->selected_filter_options[0]) && $user->selected_filter_options[0]['price']){
                        $subscription->fixed_price = true; 
                        $subscription->price = $user->selected_filter_options[0]['price'];
                    }else{
                        if($subscription->category_service_type->price_fixed){
                            $subscription->fixed_price = true; 
                            $subscription->price = $subscription->category_service_type->price_fixed; 
                        }
                    }
               }
               // $subscription->unit_price = $unit_price->value * 60; 
               $subscription->is_active = $subscription->category_service_type->is_active; 
               $subscription->gap_duration = $subscription->category_service_type->gap_duration;
               if(isset($subscription->category_service_type->category)){
                    $subscription->category_name = $subscription->category_service_type->category->name;
                    $subscription->category_id = $subscription->category_service_type->category->id; 
               }else{
                    $subscription->category_name = null;
                    $subscription->category_id = null; 
                }
               if(isset($subscription->category_service_type->service)){
                   $subscription->service_name = $subscription->category_service_type->service->type; 
                   $subscription->main_service_type = $subscription->category_service_type->service->service_type; 
                   $subscription->service_id = $subscription->category_service_type->service->id; 
                   $subscription->need_availability = $subscription->category_service_type->service->need_availability; 
                   $subscription->color_code = $subscription->category_service_type->service->color_code;
               }else{
                    $subscription->service_name = null; 
                   $subscription->main_service_type = null; 
                   $subscription->service_id = null; 
                   $subscription->need_availability = null; 
                   $subscription->color_code = null;
               } 
               unset($subscription->category_service_type); 
            }
        }
        return $subscriptions;
    }

    /**
    * User Subscription
    * @param 
    */
    public static function getServiceObjectBySp($sp_service_id,$sp_id){
        $user = self::where('id',$sp_id)->first();
        $subscription = null;
        $subscription = \App\Model\SpServiceType::where([
            'id'=>$sp_service_id,
        ])->first();
        if($subscription){
                $raw_detail = \App\Model\CustomInfo::where([
                    'info_type'=>'service_address',
                    'ref_table'=>'sp_service_types',
                    'ref_table_id'=>$subscription->id,
                ])->first();
                $subscription->clinic_address = null;
                if($raw_detail){
                    $subscription->clinic_address = json_decode($raw_detail->raw_detail);
                }
               $unit_price = EnableService::where('type','unit_price')->first();
               $slot_duration = EnableService::where('type','slot_duration')->first();
               $subscription->unit_price = $unit_price->value * 60; 
               if(Config::get('client_connected') && Config::get("client_data")->domain_name=="healtcaremydoctor"){
                    $subscription->fixed_price = false; 
                    if($subscription->category_service_type->price_fixed){
                        $subscription->fixed_price = true; 
                        $subscription->price = $subscription->category_service_type->price_fixed; 
                        $subscription->unit_price = $slot_duration->value * 60; 
                    }
               }
               if(Config::get('client_connected') && Config::get("client_data")->domain_name=="intely"){
                    $subscription->fixed_price = false;
                    $user->selected_filter_options = $user->getSelectedFiltersByCategory($user->id); 
                    if(isset($user->selected_filter_options[0]) && $user->selected_filter_options[0]['price']){
                        $subscription->fixed_price = true; 
                        $subscription->price = $user->selected_filter_options[0]['price'];
                    }else{
                        if($subscription->category_service_type->price_fixed){
                            $subscription->fixed_price = true; 
                            $subscription->price = $subscription->category_service_type->price_fixed; 
                        }
                    }
               }
               // $subscription->unit_price = $unit_price->value * 60; 
               $subscription->is_active = $subscription->category_service_type->is_active; 
               $subscription->gap_duration = $subscription->category_service_type->gap_duration; 
               $subscription->category_name = $subscription->category_service_type->category->name; 
               $subscription->category_id = $subscription->category_service_type->category->id; 
               $subscription->service_name = $subscription->category_service_type->service->type; 
               $subscription->main_service_type = $subscription->category_service_type->service->service_type; 
               $subscription->service_id = $subscription->category_service_type->service->id; 
               $subscription->need_availability = $subscription->category_service_type->service->need_availability; 
               $subscription->color_code = $subscription->category_service_type->service->color_code;
               unset($subscription->category_service_type); 
        }
        return $subscription;
    }

    public static function getSessionUser($category_id){
        $cat = CategoryServiceProvider::where('category_id',$category_id)->first();
        if($cat){
            return self::where('id',$cat->sp_id)->first();
        }
        return null;
    }

    

    public static function getSessionDoctorDetail($category_id){
        $cat = CategoryServiceProvider::where('category_id',$category_id)->first();
        if($cat){
            return self::getDoctorDetail($cat->sp_id);
        }
        return null;
    }

    public static function createSessionUser($row){
            if(isset($row['user']) && isset($row['update'])){
                $user = \App\User::where('id',$row['user']->id)->first();
            }else{
                $user = \App\User::where('email',$row['email'])->first();
                if(!$user){
                    $user = new \App\User();
                    $user->password = \Hash::make('password');
                    $dateznow = new DateTime("now", new DateTimeZone('UTC'));
                    $datenow = $dateznow->format('Y-m-d H:i:s');
                    $user->account_verified = $datenow;
                    $user->save();
                    $wallet = new Wallet();
                    $wallet->balance = 0;
                    $wallet->user_id = $user->id;
                    $wallet->save();
                    $role = Role::where('name','service_provider')->first();
                    if($role){
                        $user->roles()->attach($role);
                    }
                }
            }
            $user->email = $row['email'];
            $user->name = $row['name'];
            $user->permission = json_encode([
                'view_admin'=>true,
                'module'=>'category',
                'module_id'=>$row['category_id']
            ]);
            $user->profile_image = isset($row['image'])?$row['image']:null;
            $user->save();
            if($user){
                $profile = Profile::where('user_id',$user->id)->first();
                if(!$profile){
                    $profile = New Profile();
                    $profile->user_id = $user->id;
                    $profile->dob = '0000-00-00';
                }
                if(isset($row['lat'])){
                    $profile->lat = $row['lat'];
                }
                if(isset($row['long'])){
                    $profile->long = $row['long'];
                }
                if(isset($row['address'])){
                    $profile->location_name = $row['address'];
                }
                $profile->address = isset($row['address'])? $row['address']:null;
                $profile->save();

                $category = \App\Model\Category::where('id', $row['category_id'])->first();
                if($category){
                    $category_service = \App\Model\CategoryServiceProvider::where(['sp_id' => $user->id])->first();
                    if (!$category_service) {
                        $category_service =  new \App\Model\CategoryServiceProvider();
                        $category_service->sp_id = $user->id;
                    }
                    $category_service->category_id = $category->id;
                    $category_service->save();
                    $current_category = $category->id;
                    $service_id = $row['service_id'];
                    $duration = '60';
                    $unit_price = \App\Model\EnableService::where('type','unit_price')->first();
                    if($unit_price){
                        $duration = $unit_price->value * 60;
                    }
                    $service = \App\Model\CategoryServiceType::where([
                        'category_id'=>$current_category,
                        'service_id'=>$service_id
                    ])->first();
                    $input['category_services_type'] = [['id'=>$service->id,'available'=>'1','price'=>$service->price,'minimmum_heads_up'=>5,'availability'=>['applyoption'=>'weekdays','days'=>[1,2,3,4,5],'slots'=>[['start_time'=>'09:00','end_time'=>'18:30']]]]];
                    $timezone = 'Asia/Kolkata';
                    $input['slots'] = [['start_time'=>'09:00','end_time'=>'18:30']];
                    $feature = Helper::getClientFeatureExistWithFeatureType('Dynamic Sections','Master Interval');
                    if($feature){
                        $slots =  Helper::getMasterSlots($timezone);
                        if(count($slots) > 0){
                            $input['slots'] = $slots->toArray();
                        }
                    }
                    $duration = '60';
                    $unit_price = \App\Model\EnableService::where('type','unit_price')->first();
                    if($unit_price){
                        $duration = $unit_price->value * 60;
                    }
                    foreach ($input['category_services_type'] as $category_service_type) {
                        $spservicetype = \App\Model\SpServiceType::firstOrCreate([
                            'sp_id'=>$user->id,
                            'category_service_id'=>$category_service_type['id']
                        ]);
                        if($spservicetype){
                            $spservicetype->available = $category_service_type['available'];
                            $spservicetype->minimmum_heads_up = $category_service_type['minimmum_heads_up'];
                            $spservicetype->price = $service->price_fixed;
                            $spservicetype->duration = $duration;
                            $spservicetype->save();
                            if($service){//monday-to-friday
                                $weekdays = [1,2,3,4,5];
                                \App\Model\ServiceProviderSlot::where([
                                    'service_provider_id'=>$user->id,
                                    'service_id'=>$service->service_id,
                                    'category_id'=>$row['category_id'],
                                ])->whereIn('day',$weekdays)->delete();
                                foreach ($weekdays as $day) {
                                   foreach ($input['slots'] as $slot) {
                                        $start_time = Carbon::parse($slot['start_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                                        $end_time = Carbon::parse($slot['end_time'],$timezone)->setTimezone('UTC')->format('H:i:s');
                                        $spavailability = new \App\Model\ServiceProviderSlot();
                                        $spavailability->service_provider_id = $user->id;
                                        $spavailability->service_id = $service->service_id;
                                        $spavailability->category_id = $row['category_id'];
                                        $spavailability->start_time = $start_time;
                                        $spavailability->end_time = $end_time;
                                        $spavailability->day = $day;
                                        $spavailability->save();
                                   }
                                }
                            }
                        }
                    }
                }
                $user->account_step = 5;
                $user->save();
            }
    }



    /**
    * User Subscription
    * @param 
    */
    public static function getEnableServicesData($sp_id){
        $subscriptions = [];
        $services = [];
        $cat = \App\Model\CategoryServiceProvider::where('sp_id',$sp_id)->first();
        if(!$cat){
            return $subscriptions;
        }
        $categoryservicetype = CategoryServiceType::where('category_id',$cat->category_id)->pluck('id')->toArray();
        if(count($categoryservicetype)<1){
            return $subscriptions;
        }
        $subscriptions = \App\Model\SpServiceType::where([
            'sp_id'=>$sp_id,
        ])->whereIn('category_service_id',$categoryservicetype)->get();
        foreach ($subscriptions as $key => $subscription) {
            if($subscription){
               $services[] = array(
                'service_name' => $subscription->category_service_type->service->type,
                'main_service_type' => $subscription->category_service_type->service->service_type,
                'service_id' => $subscription->category_service_type->service->id,
                'color_code' => $subscription->category_service_type->service->color_code,
                'count' => 0,
               );
            }
        }
        return $services;
    }

    /**
    * Service Provider Detail
    * @param 
    */

    public static function getDoctorDetail($id){
        $user = User::select('*')->where('id',$id)->with('profile')->first();
        if($user && $user->hasRole('service_provider')){
            $user->categoryData = $user->getCategoryData($user->id);
            $user->additionals = $user->getAdditionals($user->id);
            $user->insurances = $user->getInsurnceData($user->id);
            $user->custom_fields = $user->getCustomFields($user->id);
            $user->patientCount = self::getTotalRequestDone($id);
            $user->reviewCount = Feedback::reviewCountByConsulatant($id);
            $user->filters = $user->getFilters($user->id);
            $user->services = $user->getServices($user->id);
            $user->account_verified = ($user->account_verified)?true:false;
            $user->totalRating = 0;
            if($user->profile){
                $user->totalRating = $user->profile->rating;
                $user->profile->bio = $user->profile->about;
                $user->profile->location = ["name"=>$user->profile->location_name,"lat"=>$user->profile->lat,"long"=>$user->profile->long];
            }
            $user->subscriptions = $user->getSubscription($user);
            $user = Helper::getMoreData($user);
        }
        return $user;
    }

    /**
    * Add User on Stripe
    * @param 
    */

    public function createStripeCustomer($user){
        try{
            $name = $user->name;
            if(!$user->name){
               $name = $user->phone;
            }
            $live = false;
            $test_key = env('STRIPE_TEST_KEY');
            $live_key = env('STRIPE_LIVE_KEY');
            $keys = Helper::getClientFeatureKeys('Payment Gateway','Stripe');
              // if(isset($keys['STRIPE_MODE'])){
            $test_key = isset($keys['STRIPE_TEST_KEY'])?$keys['STRIPE_TEST_KEY']:$test_key;
            $live_key = isset($keys['STRIPE_LIVE_KEY'])?$keys['STRIPE_LIVE_KEY']:$live_key;
            // }
            if($test_key){
                $stripe = new Stripe($test_key);
                $customer = $stripe->customers()->create([
                    'email' => $user->email,
                    'name' => $name,
                    'phone' => $user->phone,
                ]);
                if(isset($customer)){
                    $user->stripe_id = $customer['id'];
                    $user->save();
                }
            }
            if($live_key){
                $stripe = new Stripe($live_key);
                $customer = $stripe->customers()->create([
                    'email' => $user->email,
                    'name' => $name,
                    'phone' => $user->phone,
                ]);
                if(isset($customer)){
                    $user->stripe_live_id = $customer['id'];
                    $user->save();
                }
            }
            return $user;
        }catch(Exception $ex){
            \Log::channel('custom')->info('createStripeCustomer', ['error_log'=>$ex->getMessage(),'line' => $ex->getLine()]);
        }
    }

    /**
    * Attach Card to User
    * @param 
    */

    public function attachCard($user,$card){
        $default = '0';
        if($user->cards->count()<=0){
            $default = '1';
            $user->card_id = $card['id'];
        }
        $card_insert = new Card();
        $card_insert->card_last_four = $card['last4'];
        $card_insert->user_id = $user->id;
        $card_insert->card_id = $card['id'];
        $card_insert->fingerprint = $card['fingerprint'];
        $card_insert->card_brand = $card['brand'];
        $card_insert->name = $card['name'];
        $card_insert->default = $default;
        $card_insert->save();
        return $card_insert->id;
    } 


    /**
    * Attach Card to User
    * @param 
    */

    public function attachedNewCard($user,$card){
        $default = '0';
        if($user->cards->count()<=0){
            $default = '1';
            $user->card_id = $card['id'];
        }
        $card_insert = new Card();
        $card_insert->card_last_four = $card['last4'];
        $card_insert->user_id = $user->id;
        $card_insert->card_id = $card['id'];
        $card_insert->fingerprint = $card['fingerprint'];
        $card_insert->card_brand = $card['brand'];
        $card_insert->name = $card['name'];
        $card_insert->default = $default;
        $card_insert->save();
        return $card_insert;
    }

    /**
    * Attach Card to User
    * @param 
    */

    public function deAattachCard($user_id,$card_id){
        $deleted = Card::where(['id'=>$card_id,'user_id'=>$user_id])->delete();
        return;
    }

    /**
    * Attach Card to User
    * @param 
    */

    public function attachBankAccount($user,$card){
        $card_insert = new Card();
        $card_insert->card_last_four = $card['last4'];
        $card_insert->user_id = $user->id;
        $card_insert->card_id = $card['id'];
        $card_insert->fingerprint = $card['fingerprint'];
        $card_insert->card_brand = $card['bank_name'];
        $card_insert->card_type = 'bank_account';
        $card_insert->save();
        return $card_insert->id;
    }

    /**
    * Attach Card to User
    * @param 
    */

    public function getAttachedCards($user){
        $cards = Card::select('id','name','card_brand','card_last_four as last_four_digit','created_at','default')
        ->where(['user_id'=>$user->id,
            'card_type'=>'card'
        ])->orderBy('id', 'desc')
        ->get();
        return $cards;
    }

    /**
    * Attach Card to User
    * @param 
    */

    public function getAttachedBanks($user){
        $cards = BankAccount::select('id','holder_name as name','bank_name','account_number','account_number as last_four_digit','ifc_code','account_type as account_holder_type','country','currency','created_at','institution_number','transit_number','customer_type','address','city','province','postal_code')
        ->where(['user_id'=>$user->id])->orderBy('id', 'desc')
        ->get();
        return $cards;
    }

    public function addUserRequiredDetail($user,$user_type){
        $user = $user->createStripeCustomer($user);
        $wallet = new Wallet();
        $wallet->balance = 0;
        $wallet->points = 5;
        $wallet->user_id = $user->id;
        $wallet->save();
        $role = Role::where('name',$user_type)->first();
        if($role){
            $user->roles()->attach($role);
        }
        $random = Str::random(10);
        $user->reference_code = $random.$user->id;
        $user->save();
        return $user;
    }

    public static function  getTotalRequestDone($sr_pro_id){
        $count = \App\Model\Request::where('to_user', $sr_pro_id)
        ->whereHas('requesthistory', function($query){
                return $query->where('status','completed');
        })
        ->groupBy('from_user')
        ->get();
        return $count->count();
    }

    public static function  getTotalRequestHours($sr_pro_id,$start=null,$end=null){
        if($start)
            $start = $start.' 00:00:00';
        if($end)
            $end = $end.' 00:00:00';
        $count = \App\Model\Request::where('to_user', $sr_pro_id)
        ->whereHas('requesthistory', function($query){
                return $query->where('status','completed');
        })->where(function($query) use($start,$end){
            if($start && $end){
                $query->where('booking_date','>=',$start)->where('booking_end_date','<=',$end);
            }
        })
        ->sum('total_hours');
        return $count;
    }

    public function requestDone($sr_pro_id){
        $where = "to_user";
        $count = \App\Model\Request::select('from_user')
            ->where($where, $sr_pro_id)
            ->whereHas('requesthistory', function($query){
                    return $query->where('status','completed');
            })
            ->get();
        return $count->count();
    }

    public function requestCompleted($user_id){
        $where = "from_user";
        if($this->isDoctor($user_id)){
            $where = "to_user";
        }
        $count = \App\Model\Request::select('from_user')
            ->where($where, $user_id)
            ->whereHas('requesthistory', function($query){
                    return $query->where('status','completed');
            })
            ->get();
        return $count->count();
    }

    public function getReqAnaliticsByCustomer($user_id){
       $reqs =  \App\Model\Request::where('from_user',$user_id)->get();
       $total_request = $reqs->count();
       $total_chats = 0;
       $total_calls = 0;
       $total_completed_request = 0;
       $total_unsuccess_request = 0;
       foreach ($reqs as $key => $req) {
            if($req->requesthistory && $req->requesthistory->status=='completed'){
                $total_completed_request++;
            }
            if($req->requesthistory && $req->requesthistory->status=='failed'){
                $total_unsuccess_request++;
            }
            if($req->servicetype->type=='chat'){
                $total_chats++;
            }
            if($req->servicetype->type=='call'){
                $total_calls++;
            }
       }
       return (object) array(
        'totalRequest'=>$total_request,
        'totalChat'=>$total_chats,
        'totalCall'=>$total_calls,
        'completedRequest'=>$total_completed_request,
        'unSuccesfullRequest'=>$total_unsuccess_request,
        );
    }

    public static function  categoryReq($sr_pro_id){
        $count = \App\Model\Request::select('from_user')
        ->where('to_user', $sr_pro_id)
        ->whereHas('requesthistory', function($query){
                return $query->where('status','completed');
        })
        ->groupBy('from_user')
        ->get();
        return $count->count();
    }

    public function givenReviewByUser($user_id){
       return  Feedback::where('from_user',$user_id)->orderBy('id','desc')->get()->take(5);
    }

   /*
    This function is get the detail of reviews of
    particullar user
    @param $userid i.e get the review of user.
   */
    public static function getUserReview($user_id){
        $reviews =   Feedback::where('consultant_id',$user_id)->orderBy('id','desc')->get()->take(5);
            foreach($reviews as $review){
                $user = User::find($review->from_user);
                $review->user = $user;
            }
            $reviews = isset($reviews) ? $reviews : array();
            return $reviews;

     }

    public function getCategoryData($sp_id){
        $cat = \App\Model\CategoryServiceProvider::where('sp_id',$sp_id)->first();
        if($cat){
            return $cat->getCategoryData($cat->category_id);
             if($category && isset($category->additionals)){
            foreach ($category->additionals as $key => $additional) {
                $additional->documents = SpAdditionalDetail::select('id','title','description','file_name','status','comment','type')->where([
                         "additional_detail_id"=>$additional->id,
                        "sp_id"=>$sp_id
                    ])->get(); 
                }
                return $category;
            }
        }
    }

    public function getCategorysData($sp_id){

        $category = \App\Model\CategoryServiceProvider::where('sp_id',$sp_id)->get();

        if($category){
            $data = [];
            foreach ($category as $key => $cat) {
                   
                
                $data[]= $cat->getCategorysData($cat->category_id);
                if($cat && isset($cat->additionals)){
                foreach ($cat->additionals as $key => $additional) {
                    $additional->documents = SpAdditionalDetail::select('id','title','description','file_name','status','comment','type')->where([
                            "additional_detail_id"=>$additional->id,
                            "sp_id"=>$sp_id
                        ])->get(); 
                    }
                    return $cat;
                }
            }

            return $data;
        }
    }

    public function getAdditionals($sp_id){
        if(Config::get('client_connected') && Config::get("client_data")->domain_name=="iedu"){
            $additionals = AdditionalDetail::where(['is_enable'=>'1'])->get();
            foreach ($additionals as $key => $additional) {
                $additional->documents = SpAdditionalDetail::select('id','title','description','file_name','status','comment','type')->where([
                            "additional_detail_id"=>$additional->id,
                            "sp_id"=>$sp_id
                        ])->get();
            }
            return $additionals;
        }else{
            $cat = \App\Model\CategoryServiceProvider::where('sp_id',$sp_id)->first();
            if($cat){
                $category = $cat->getCategoryData($cat->category_id);
                if($category && isset($category->additionals)){
                $data = [];
                foreach ($category->additionals as $key => $additional) {

                    $additional->documents = SpAdditionalDetail::select('id','title','description','file_name','status','comment','type')->where([
                            "additional_detail_id"=>$additional->id,
                            "sp_id"=>$sp_id
                        ])->get();
                        if($additional->documents->count()>0){
                            $data[] = $additional;
                        } 
                    }
                    return $data;
                }
            }
        }
    }


    public function getAdditionalsDocument($sp_id){
        
        if(\Config('client_connected') && Config::get("client_data")->domain_name=="iedu"){
           
         $documents = SpAdditionalDetail::select('id','title','description','file_name','status','comment','type')->where(["sp_id"=>$sp_id])->get();
            return $documents;
     }else{
         $cat = \App\Model\CategoryServiceProvider::where('sp_id',$sp_id)->first();
         if($cat){
            $data = [];
            $category = $cat->getCategoryData($cat->category_id);
            if($category && isset($category->additionals)){
            foreach ($category->additionals as $key => $additional) {
                $additional->documents = SpAdditionalDetail::select('id','title','description','file_name','status','comment','type')->where([
                        "additional_detail_id"=>$additional->id,
                        "sp_id"=>$sp_id
                    ])->get();
                    if($additional->documents->count()>0){
                        $data[] = $additional;
                    }
                }
            }
            return $data;
        }
    }
     
    }

    public  function getcourseSP($sp_id){

        $course = SpCourse::where('sp_id',$sp_id)->groupBy('course_id')->get();
        
        if($course){
            $data = [];
            foreach ($course as $key => $courses) {
                   
                
                $data[]= $courses->getcourseUser($courses->course_id);

                $data[$key]->course_id=$courses->course_id;

                
            }

            return $data;
        }
    }

    public function getSpPrice($sp_id){
        $cat = \App\Model\SpServiceType::where('sp_id',$sp_id)->first();

        return $cat;
        
    }

    public function getInsurnceData($user_id){
        $insurances = \App\Model\UserInsurance::select('insurance_id','user_id')->where('user_id',$user_id)->get();
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

    public function getCustomFields($user_id){
        $fields = \App\Model\CustomUserField::select('custom_field_id as id','custom_field_id','user_id','field_value')->where('user_id',$user_id)->get();
        foreach ($fields as $key => $field) {
            if($field){
                $field->field_name = $field->customfield->field_name;
                $field->field_type = $field->customfield->field_type;
                $field->required_sign_up = $field->customfield->required_sign_up;
                unset($field->customfield);
                unset($field->custom_field_id);
            }
        }
        return $fields;
    }

    public function getFilters($sp_id){
        $filters = [];
        $cat = \App\Model\CategoryServiceProvider::where('sp_id',$sp_id)->first();
        if($cat){
            $filters = FilterType::getFiltersByCategory($cat->category_id,$sp_id);
        }
        return $filters;
    }

    public function getSelectedFiltersByCategory($sp_id){
        $filters = [];
        $cat = \App\Model\CategoryServiceProvider::where('sp_id',$sp_id)->first();
        if($cat){
            $filters = FilterType::getSelectedFiltersByCategory($cat->category_id,$sp_id);
        }
        return $filters;
    }


    /*
        function for update user profile
    */

    public static function updateUserProfile($request, $userId){
        //dd($request->all());
        $user = User::find($userId);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        // $user->manual_available =  $request->manual_available == 'on' ? 1 : 0;
        $user->save();
        $user_profile = Profile::where('user_id',$userId)->first();
        $user_profile->city =  $request->city;
        $user_profile->state =  $request->state;
        $user_profile->about =  $request->about;
        $user_profile->address =  $request->address;
        $user_profile->qualification =  $request->qualification;
        $user_profile->save();
        $custom_field_id=$request->custom_field_id;
        //dd($custom_field_id);
        $user_zip=CustomUserField::with('customfield')->where('user_id',$userId)->where('custom_field_id',$custom_field_id)->first();
        if(isset($user_zip) && !empty($user_zip)){
            if(isset($request->zip)){
                $user_zip->field_value=$request->zip;
                $user_zip->save();
            }
        }else{
            if(isset($request->zip)){
                $user_zip = new CustomUserField();
                $user_zip->custom_field_id = $custom_field_id;
                $user_zip->field_value= $request->zip;
                $user_zip->user_id = $userId;
                $user_zip->save();
            }
        }
        $user_insurance = UserInsurance::where('user_id',$userId)->first();
       
        if(isset($user_insurance) && !empty($user_insurance)){
            UserInsurance::where('user_id', $userId)->delete();
            if(!empty($request['insurance'])){

                foreach ($request['insurance'] as $key => $insurance_id) {
                    if ($insurance_id) {
                        $userinsurance = new UserInsurance();
                        $userinsurance->insurance_id = $insurance_id;
                        $userinsurance->user_id = $userId;
                        $userinsurance->save();
                    }
                }
            }
           
        }else{
            
            if (isset($request['insurance'])) {

                UserInsurance::where('user_id', $userId)->delete();
               // dd($request['insurance']);
                if(!empty($request['insurance'])){
                    foreach ($request['insurance'] as $key => $insurance_id) {
                        if ($insurance_id) {
                            $userinsurance = new UserInsurance();
                            $userinsurance->insurance_id = $insurance_id;
                            $userinsurance->user_id = $userId;
                            $userinsurance->save();
                        }
                    }
                }
            }
            
        }

        if(isset($request->answer1) && isset($request->question1) && isset($request->answer2) && isset($request->question2) && isset($request->answer3) && isset($request->question3)){
            \App\Model\UserSecurityAnswer::where('user_id',$userId)->delete();
            $UserSecurityAnswer1 = new \App\Model\UserSecurityAnswer();
            $UserSecurityAnswer1->security_question_id = $request->question1;
            $UserSecurityAnswer1->user_id = $userId;
            $UserSecurityAnswer1->answer = $request->answer1;
            $UserSecurityAnswer1->save();

            $UserSecurityAnswer2 = new \App\Model\UserSecurityAnswer();
            $UserSecurityAnswer2->security_question_id = $request->question2;
            $UserSecurityAnswer2->user_id = $userId;
            $UserSecurityAnswer2->answer = $request->answer2;
            $UserSecurityAnswer2->save();

            $UserSecurityAnswer3 = new \App\Model\UserSecurityAnswer();
            $UserSecurityAnswer3->security_question_id = $request->question3;
            $UserSecurityAnswer3->user_id = $userId;
            $UserSecurityAnswer3->answer = $request->answer3;
        }


        $UserSecurityAnswer3->save();

        return true;

    }
}
