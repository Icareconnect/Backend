<?php

use Illuminate\Database\Seeder;

class CreateCurrencyTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sql = public_path('currencies.sql');
		//collect contents and pass to DB::unprepared
		\DB::unprepared(file_get_contents($sql));

        $country = public_path('countries.sql');
        //collect contents and pass to DB::unprepared
        \DB::unprepared(file_get_contents($country));
        
        $app_versions = public_path('app_versions.sql');
        //collect contents and pass to DB::unprepared
        \DB::unprepared(file_get_contents($app_versions));

    }
}
