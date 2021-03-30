<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\User;
use App\Notification;
use App\Model\Request as RequestData; 
use App\Model\RequestHistory; 
class RequestReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $history = RequestHistory::where([
        'request_id'=>$this->data["request_id"]
       ])->whereIn('status',['accept','pending'])->first();
      if($history){
          $notification = new Notification();
          $notification->push_notification(
                array($this->data["to"]),
                    array(
                    'pushType'=>'UPCOMING_APPOINTMENT',
                    'request_id'=>$this->data["request_id"],
                    'message'=>__($this->data["message"])
                )
            );
        }
    }
}
