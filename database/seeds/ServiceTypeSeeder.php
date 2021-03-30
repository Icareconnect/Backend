<?php

use Illuminate\Database\Seeder;
use App\Model\ServiceType;
class ServiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ServiceType::firstOrCreate(['name'=>'audio_call']);
        ServiceType::firstOrCreate(['name'=>'video_call']);
        ServiceType::firstOrCreate(['name'=>'chat']);
        ServiceType::firstOrCreate(['name'=>'home_visit']);
        ServiceType::firstOrCreate(['name'=>'clinic_visit']);
        ServiceType::firstOrCreate(['name'=>'other']);
    }
}
