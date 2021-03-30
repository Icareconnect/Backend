<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Exception;
use DateTime,DateTimeZone;
use Carbon\Carbon;
use App\Notification;
use Config,DB,App\Helpers\Helper;
class ChatCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Chat Request Check Active or not';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::info("Chat Cron Starting...");
        $this->info('Chat Cron Starting...');
        $clients = \DB::connection('godpanel')->table('clients')->orderBy('id','ASC')->get();
        foreach ($clients as $key=>$client) {
            try{
                $database_name = 'db_'.$client->domain_name;
                $default = [
                    'driver' => env('DB_CONNECTION','mysql'),
                    'host' => env('DB_HOST'),
                    'port' => env('DB_PORT'),
                    'database' => $database_name,
                    'username' => env('DB_USERNAME'),
                    'password' => env('DB_PASSWORD'),
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'prefix_indexes' => true,
                    'strict' => false,
                    'engine' => null
                ];
                Config::set("database.connections.$database_name", $default);
                Config::set("client_id", $client->id);
                Config::set("client_connected",true);
                Config::set("client_data",$client);
                DB::setDefaultConnection($database_name);
                DB::purge($database_name);
                $this->info("client requests $client->domain_name");
                \App\Model\Request::whereHas('requesthistory', function($query){
                            return $query->whereIn('status',['in-progress','accept','pending']);
                    })
                ->chunk(10, function($requests) use($client){
                    foreach ($requests as $key=>$request_data) {
                        try{
                            // print_r($client->domain_name);die('djjd');
                            $this->info("client request status ". $request_data->requesthistory->status);
                            $dateznow = new DateTime("now", new DateTimeZone('UTC'));
                            $datenow = $dateznow->format('Y-m-d H:i:s');
                            $slot_duration = \App\Model\EnableService::where('type','slot_duration')->first();
                            $seconds = $slot_duration->value*60;
                            $next_hour_time = strtotime($request_data->booking_date) + $seconds;
                            $this->info("client pending reqid ". $request_data->id);
                            if(strtotime($datenow)>=$next_hour_time){
                                if($request_data->requesthistory->status=='pending'){
                                    if($request_data->requesthistory->total_charges){
                                        $this->info("client Charges true or false " . Helper::chargeFromSP());
                                        if(!Helper::chargeFromSP()){
                                            $deposit_to = array(
                                                'balance'=>$request_data->requesthistory->total_charges,
                                                'user'=>$request_data->cus_info,
                                                'from_id'=>$request_data->sr_info->id,
                                                'request_id'=>$request_data->id,
                                                'status'=>'succeeded'
                                            );
                                            \App\Model\Transaction::createRefund($deposit_to);
                                        }
                                    }
                                    $request_data->requesthistory->status = 'failed';
                                    $request_data->requesthistory->save();

                                    $notification = new Notification();
                                    $notification->sender_id = $request_data->from_user;
                                    $notification->receiver_id = $request_data->to_user;
                                    $notification->module_id = $request_data->id;
                                    $notification->module ='request';
                                    $notification->notification_type ='REQUEST_FAILED';
                                    $notification->message =__('notification.req_failed_text');
                                    $notification->save();

                                    $notification = new Notification();
                                    $notification->sender_id = $request_data->to_user;
                                    $notification->receiver_id = $request_data->from_user;
                                    $notification->module_id = $request_data->id;
                                    $notification->module ='request';
                                    $notification->notification_type ='REQUEST_FAILED';
                                    $notification->message =__('notification.req_failed_text');
                                    $notification->save();
                                    $notification->push_notification(array($request_data->to_user,$request_data->from_user),array(
                                        'pushType'=>'REQUEST_FAILED',
                                        'request_id'=>$request_data->id,
                                        'message'=>__('notification.req_failed_text')));
                                }else{
                                    if($client->domain_name!=='intely'){
                                        $request_data->requesthistory->status = 'completed';
                                        $request_data->requesthistory->save();
                                        $this->info('Request completed #'.$request_data->id. $request_data->requesthistory->status);
                                        if(!Helper::chargeFromSP()){
                                            $deposit_to = array(
                                                'user'=>$request_data->sr_info,
                                                'from_id'=>$request_data->cus_info->id,
                                                'request_id'=>$request_data->id,
                                                'status'=>'succeeded'
                                            );
                                            \App\Model\Transaction::updateDeposit($deposit_to);
                                        }
                                        $notification = new Notification();
                                        $notification->sender_id = $request_data->from_user;
                                        $notification->receiver_id = $request_data->to_user;
                                        $notification->module_id = $request_data->id;
                                        $notification->module ='request';
                                        $notification->notification_type ='REQUEST_COMPLETED';
                                        $notification->message =__('notification.req_completed_text');
                                        $notification->save();

                                        $notification = new Notification();
                                        $notification->sender_id = $request_data->to_user;
                                        $notification->receiver_id = $request_data->from_user;
                                        $notification->module_id = $request_data->id;
                                        $notification->module ='request';
                                        $notification->notification_type ='REQUEST_COMPLETED';
                                        $notification->message =__('notification.req_completed_text');
                                        $notification->save();
                                        $notification->push_notification(array($request_data->to_user,$request_data->from_user),array(
                                            'pushType'=>'REQUEST_COMPLETED',
                                            'message'=>__('notification.req_completed_text'),
                                            'request_id'=>$request_data->id,
                                        ));
                                    }
                                }
                                \Log::info("Request done #".$request_data->id);
                                $this->info('Request done #'.$request_data->id);
                            }
                        }catch(Exception $ex){
                            \Log::info("Error ".$ex->getMessage());
                            $this->error('Error '.$ex->getMessage());
                        }
                    }
                });

            }catch(Exception $ex){
                $this->info($ex->getMessage());
                continue;
            }
        }
        \Log::info("Chat Cron End...");
        $this->info('Chat Cron End...');

    }
}
