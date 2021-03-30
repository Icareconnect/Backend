<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User,App\Model\SubscribePlan;
use DB,Config;
use Carbon\Carbon;
use App\Notification;
use App\Model\Plan;
class DailyPlanCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mypath:plancheck';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    {}
}
