<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(array('middleware'=>['log_after_request']), function(){
    Route::post('send-sms', 'SmsController@store');
    Route::post('send-email-otp', 'SmsController@sendEmailOtp');
    Route::post('email-verify', 'SmsController@verifyEmail');
    Route::any('webhook/razorpay', 'WebHookController@getHandleRazorPayWebhook');
    Route::any('webhook/stripe', 'WebHookController@getHandleStripeWebhook');
    Route::any('webhook/hyperpay', 'WebHookController@getHyperWebhook');
    Route::group(["namespace"=>"API"],function() {
    Route::post('test-notification','UserController@testNotification');
    Route::post('test-call', 'CallerController@makeCallTestToken');
        /*app version */
        Route::post('appversion', 'DataController@appVersion');
        Route::get('clientdetail', 'DataController@getClientDetail');
        Route::get('countrydata', 'DataController@getCountryStateCity');
        Route::get('clientdetail_panel', 'DataController@getClientDetailForPanel');
        /* User Controller */ 
        Route::post('login', 'UserController@socialLogin');
        Route::get('user-check', 'UserController@checkUserExit');
        Route::post('verify-check-answer', 'UserController@checkVerifyAnswer');
        Route::get('security-questions', 'UserController@getSecurityQuestion');
        Route::post('reset-password', 'UserController@postResetPassword');
        Route::post('login2', 'UserController@socialLogin2');
        Route::post('forgot_password', 'UserController@forgot_password');
        Route::post('register', 'UserController@register');
        Route::post('register2', 'UserController@register2');
        /* Auth Routes */
        Route::group(['middleware' => 'auth:api'], function() {
            Route::post('start-request','CallerController@startRequest');
            Route::post('change_password', 'UserController@change_password');
            Route::post('password-change', 'UserController@passwordChange');
            Route::post('update-security-question', 'UserController@updateSecurityQuestion');
            Route::post('profile-update', 'UserController@profieUpdate');
            Route::post('save-address', 'UserController@addAddress');
            Route::get('get-address', 'UserController@getAddress');
            Route::get('profile', 'UserController@getUserProfile');
            Route::post('update-phone', 'UserController@changePhoneNumber');
            Route::post('app_logout', 'UserController@app_logout');
            Route::get('getuser', 'UserController@GetUser');
            Route::post('update-fcm-id', 'UserController@updateFcmId');
            Route::get('getreferencelist', 'UserController@getReferenceByList');
            Route::get('getslotstime', 'UserController@getSlotsTime');
            Route::get('gethealthcarevisit', 'UserController@getHealthCareVisit');
            Route::get('gettypeofrecords', 'UserController@getTypeOfRecords');
            Route::get('getinvoicelist', 'UserController@getInvoiceList');
            Route::post('save_health_records', 'UserController@saveHealthRecords');
            Route::post('upload_profile_image', 'UserController@upload_profile_image');
            Route::get('gethealthrecordslist', 'UserController@getHealthRecordsList');
            Route::get('gethealthrecordsdetail', 'UserController@getHealthRecordsDetail');
            Route::post('edithealthrecords', 'UserController@editHealthRecords');
            Route::delete('deletehtrecords/{id}', 'UserController@destroy');
            Route::delete('removeImage/{id}', 'UserController@removeImage');
            Route::post('saveMultipleImages', 'UserController@saveMultipleImages');
            
            // For My Path
            Route::post('manual-available', 'UserController@postMannualAvailable');
            Route::get('online-flags', 'UserController@getOnlineFlags');
            Route::get('dates-slots', 'ServiceController@getSlotsByMultipleDates');
            Route::get('recent-view', 'ServiceController@getRecentList');
            Route::post('insurance-info', 'UserController@saveInsuranceInfo');
            /* askSupportQuestion  */
            Route::post('ask-questions', 'FeedController@askSupportQuestion');
            Route::post('reply-question', 'FeedController@replySupportQuestion');
            Route::get('ask-questions', 'FeedController@getaskSupportQuestion');
            Route::get('ask-question-detail', 'FeedController@getaskSupportQuestionDetail');

            Route::get('water-limit', 'FeedController@getWaterLimit');
            Route::post('water-limit', 'FeedController@postSetWaterLimit');
            Route::get('daily-usage', 'FeedController@getDailyUsage');
            Route::post('drink-water', 'FeedController@postDrinkWater');

            Route::get('protein-limit', 'ProteinController@getProteinLimit');
            Route::post('drink-protein', 'ProteinController@postDrinkProtein');
            Route::post('protein-limit', 'ProteinController@postSetProteinLimit');
            Route::get('daily-usage-protein', 'ProteinController@getDailyUsageProtein');

            Route::get('insurance-info', 'UserController@getUserInsuranceDetail');
            Route::post('add-family', 'UserController@addFamilyMember');
            /* Service Provider Controller */
            Route::get('wallet-sp', 'ServiceController@getWalletBalance');
            Route::get('advertisement', 'ServiceController@getAdvertiseMent');
            Route::post('set-filters', 'ServiceController@setFiltersForServiceProvider');
            Route::post('block-dates', 'ServiceController@postBlockDates');
            Route::get('block-dates', 'ServiceController@getBlockDates');
            Route::get('wallet-history-sp', 'ServiceController@getWalletHistory');
            Route::get('requests', 'ServiceController@getRequests');
            Route::get('pending-request-by-date', 'ServiceController@getPendingRequestByDate');
            Route::post('accept-request', 'ServiceController@postAcceptRequest');
            Route::get('bank-accounts', 'ServiceController@getBankAccountsListing');
            Route::get('revenue', 'ServiceController@getRevenue');
            Route::post('subscribe-service', 'ServiceController@postSubscribe');
            Route::post('update-services', 'ServiceController@postSubscribeServiceOrFilters'); 
            Route::post('update-sp-categories', 'ServiceController@postServiceOrFilters'); 
            Route::post('manual-update-services', 'ServiceController@postMannualSubscribeService');
            Route::post('create-banner', 'DataController@addBanner');
            Route::get('master/selected/preferences', 'DataController@getSelectedMasterPreferences');
            Route::post('master/preferences/custom', 'DataController@postCustomMasterPreferences');
            Route::post('start-chat', 'ServiceController@postStartChat');
            Route::post('pre_screptions', 'ServiceController@postAddPreScriptions');
            Route::get('patient-list', 'ServiceController@getPatientList');
            Route::get('request-detail', 'ServiceController@getRequestDetailById');
            Route::post('call-status', 'ServiceController@postCallStausChange');
            Route::post('create-package', 'PackageController@createPackage');
            Route::get('get-physio-slots', 'ServiceController@getCustomSlots');
            Route::post('post-physio-slots', 'ServiceController@postCustomSlots');
            Route::get('appointment-dates', 'ServiceController@getAppointmentByMonthDates');

            /* Customer Controller */
            Route::get('wallet', 'CustomerController@getWalletBalance');
            Route::post('extra-payment', 'CustomerController@appointmentExtraPayment');
            Route::post('pay-extra-payment', 'CustomerController@acceptAppointmentExtraPayment');
            Route::get('wallet-history', 'CustomerController@getWalletHistory');
            Route::get('requests-cs', 'CustomerController@getRequestByCustomer');
            Route::get('cards', 'CustomerController@getPaymentCardListing');
            Route::post('create-request', 'CustomerController@postCreateRequest');
            Route::post('update-request-symptoms', 'CustomerController@updateRequestSymptoms');
            Route::post('update-request-prefrences', 'CustomerController@updateRequestPrefrences');
            Route::post('request-user-approve', 'CustomerController@updateUserRequestStatus');
            Route::get('request-check', 'CustomerController@checkRequestCreated');
            Route::post('auto-allocate', 'CustomerController@postAutoAllocateRequest');
            Route::post('confirm-auto-allocate', 'CustomerController@postConfirmAutoAllocateRequest');
            Route::post('cancel-request', 'CustomerController@postCancelRequest');
            Route::post('confirm-request', 'CustomerController@postConfirmRequest');
            Route::post('add-review', 'CustomerController@postAddReview');

            /* Payment Controller */
            Route::post('add-money', 'PaymentController@postAddMoney');
            Route::post('purchase-package', 'PaymentController@postPurchasePackage');
            Route::post('add-card', 'PaymentController@postAddCard');
            Route::post('update-card', 'PaymentController@updateCard');
            Route::post('delete-card', 'PaymentController@deleteCard');
            Route::post('complete-chat', 'PaymentController@postCompleteChat');
            Route::post('add-bank', 'PaymentController@postAddBankAccount');
            Route::post('payouts', 'PaymentController@payoutWalletToBankAccount');
            Route::post('enroll-user','PaymentController@postPayEnroll');
            Route::post('order/create','PaymentController@postAddOrder');


            /* Chat Controller */
            Route::post('message', 'ChatController@sendMessage');
            Route::get('chat-listing', 'ChatController@getChatListing');
            Route::get('chat-messages', 'ChatController@getMessages');

            /* Caller Controller */
            Route::post('make-call','CallerController@makeCallRequest');

            Route::get('notifications','NotificationController@getNotificationList');
            Route::post('feeds', 'FeedController@store');
            Route::get('support-packages', 'PackageController@getSupportPackage');
            Route::post('feeds/update/{feed_id}', 'FeedController@postUpdateFeed');
            Route::post('feeds/delete/{feed_id}', 'FeedController@postDeleteFeed');
            Route::get('feeds/view', 'FeedController@postViewFeed');
            Route::get('faqs', 'FeedController@getFAQs');
            Route::post('feeds/add-favorite/{feed_id}', 'FeedController@addToFavorite');
            Route::post('feeds/add-comment/{feed_id}', 'FeedController@addToComment');
            Route::post('feeds/add-like/{feed_id}', 'FeedController@addToLike');
            Route::get('feeds/view/{feed_id}', 'FeedController@postViewFeed');
            /* Category Controller */
            Route::post('add-class','CategoryController@postCreateClass');
            Route::get('classes','CategoryController@getClasses');
            Route::post('class/status','CategoryController@putClassStatusChange');
            Route::post('class/join','CategoryController@joinClassByUser');
            Route::get('class/detail','CategoryController@getClassDetail');
            Route::post('additional-detail-data','CategoryController@postAdditionalFields');
            Route::post('sub-pack','CategoryController@postPackages');
            Route::get('pack-detail','CategoryController@getPackageDetail');
            Route::get('get-user-slots', 'CustomerController@getSlotsByDates');
            /* Subscribe Controller */
            Route::post('subscribe-plan','SubscribeController@postSubscribePlan');

            Route::post('group/assign', 'GroupController@assignVendorToGroup');
            Route::post('verification/insurance', 'DataController@verifyEligibility');
            Route::post('master/custom/masterfields', 'DataController@postCustomMasterFields');
            Route::get('master/custom/masterfields', 'DataController@getCustomMasterFields');

            Route::post('sp-course','CourseController@postspcourses');
        });
        Route::post('razor-pay-webhook','PaymentController@postWebhookRazorPay');
        Route::get('feeds', 'FeedController@index');
        Route::get('feeds/comments/{feed_id}', 'FeedController@getComments');
        Route::get('tips', 'FeedController@getTips');

        Route::get('groups', 'GroupController@groupsListing');
        Route::get('group-doctors', 'GroupController@groupsDoctorListing');
        Route::post('group/create', 'GroupController@createGroup');


        

        Route::get('pack-sub','CategoryController@getPackages');
        Route::get('pandemic','DataController@getPandemicList');
        Route::get('review-list', 'ServiceController@getDoctorReviewList');
        Route::get('home', 'DataController@getHomePageData');
        Route::get('plans', 'DataController@getPlans');
        Route::get('doctor-detail', 'ServiceController@getDoctorDetailById');
        Route::get('get-slots', 'ServiceController@getSlotsByDates');
        Route::get('get-date-slots', 'ServiceController@getSlotsByDatesdoctor');
        Route::get('coupons','CouponController@getCoupons');
        Route::get('categories','CategoryController@getCategories');
        Route::get('additional-details','CategoryController@getAdditionalFields');
        Route::get('additional-documents','CategoryController@getAdditionalDocuments');
        
        Route::get('sp-categories','CategoryController@getCategoriesViaServiceProvider');
        Route::get('doctor-list', 'ServiceController@getDoctorList');
        Route::get('sp-list', 'ServiceController@getSPList');
        Route::get('auto-allocate', 'ServiceController@getDoctorData');
        Route::get('services', 'ServiceController@getServiceList');
        Route::get('banners', 'ServiceController@getBannerList');
        Route::get('clusters', 'ServiceController@getClusterList');
        Route::post('callback_exotel','CallerController@callbackExotel');
        Route::any('call','CallerController@callTwillio');
        Route::any('call1','CallerController@callTwillio');
        Route::any('placeCall','CallerController@placeCall');
        Route::any('incoming','CallerController@incoming');
        Route::any('accessToken','CallerController@accessTokenTwillio');
        Route::any('callback','CallerController@twillioCallback');
        Route::get('get-filters', 'ServiceController@getFiltersForServiceProvider');
        Route::get('pages', 'DataController@getPageContent');
        Route::get('master/preferences', 'DataController@getMasterPreferences');
        Route::get('master/duty', 'DataController@getMasterPreferencesDuty');
        Route::get('symptoms', 'DataController@getMasterSymptoms');
        Route::post('upload-image', 'UserController@uploadImage');

        /* Course Controller */
        Route::get('courses','CourseController@getcourses');

    });
});

