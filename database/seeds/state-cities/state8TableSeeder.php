<?php

use Illuminate\Database\Seeder;

class state8TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Create Cities for the state of DC - District of Columbia.
        //If the table 'cities' exists, insert the data to the table.
        if (DB::table('cities')->get()->count() >= 0) {
            DB::table('cities')->insert([
                ['name' => 'Washington', 'state_id' =>3930],
                ['name' => 'Naval Anacost Annex', 'state_id' => 3930],
                ['name' => 'Washington Navy Yard', 'state_id' => 3930]
            ]);
        }
    }
}
