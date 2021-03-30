<?php

use Illuminate\Database\Seeder;
use App\Model\Service;
class ServiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $call = Service::firstOrCreate(['type'=>'Call']);
        $call->service_type ='call';
        $call->save();
        $chat = Service::firstOrCreate(['type'=>'Chat']);
        $chat->service_type = 'chat';
        $chat->save();
    }
}
