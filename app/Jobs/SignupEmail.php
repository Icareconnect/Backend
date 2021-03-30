<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\User;
use Config; 
class SignupEmail implements ShouldQueue
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
        $user = User::where('id',$this->data['id'])->first();
        $current_role = ucwords(str_replace('_', ' ', $user->roles[0]['name']));
        $to_email = 'service@nurselynx.com';
        $email = isset($user->email)?$user->email:$user->phone;
        $subject = 'New Signup From '.$email;
        $name = $user->name;
        $from_name = 'no-reply';
        \Mail::raw("New User Signup as $current_role \nName:$name \nFrom email: $email", function ($message) use($subject,$to_email,$from_name) {
          $message->from($to_email,$from_name)->to($to_email)->subject($subject);
        });
    }
}
