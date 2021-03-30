<?php

use Illuminate\Database\Seeder;

class state40TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Create Cities for the state of RI - Rhode Island.
        //If the table 'cities' exists, insert the data to the table.
        if (DB::table('cities')->get()->count() >= 0) {
            DB::table('cities')->insert([
                ['name' => 'Barrington', 'state_id' => 3966],
                ['name' => 'Bristol', 'state_id' => 3966],
                ['name' => 'Prudence Island', 'state_id' => 3966],
                ['name' => 'Warren', 'state_id' => 3966],
                ['name' => 'Coventry', 'state_id' => 3966],
                ['name' => 'West Greenwich', 'state_id' => 3966],
                ['name' => 'East Greenwich', 'state_id' => 3966],
                ['name' => 'Greene', 'state_id' => 3966],
                ['name' => 'Warwick', 'state_id' => 3966],
                ['name' => 'West Warwick', 'state_id' => 3966],
                ['name' => 'Adamsville', 'state_id' => 3966],
                ['name' => 'Jamestown', 'state_id' => 3966],
                ['name' => 'Little Compton', 'state_id' => 3966],
                ['name' => 'Newport', 'state_id' => 3966],
                ['name' => 'Middletown', 'state_id' => 3966],
                ['name' => 'Portsmouth', 'state_id' => 3966],
                ['name' => 'Tiverton', 'state_id' => 3966],
                ['name' => 'Albion', 'state_id' => 3966],
                ['name' => 'Chepachet', 'state_id' => 3966],
                ['name' => 'Clayville', 'state_id' => 3966],
                ['name' => 'Fiskeville', 'state_id' => 3966],
                ['name' => 'Forestdale', 'state_id' => 3966],
                ['name' => 'Foster', 'state_id' => 3966],
                ['name' => 'Glendale', 'state_id' => 3966],
                ['name' => 'Greenville', 'state_id' => 3966],
                ['name' => 'Harmony', 'state_id' => 3966],
                ['name' => 'Harrisville', 'state_id' => 3966],
                ['name' => 'Hope', 'state_id' => 3966],
                ['name' => 'Manville', 'state_id' => 3966],
                ['name' => 'Mapleville', 'state_id' => 3966],
                ['name' => 'North Scituate', 'state_id' => 3966],
                ['name' => 'Oakland', 'state_id' => 3966],
                ['name' => 'Pascoag', 'state_id' => 3966],
                ['name' => 'Pawtucket', 'state_id' => 3966],
                ['name' => 'Central Falls', 'state_id' => 3966],
                ['name' => 'Cumberland', 'state_id' => 3966],
                ['name' => 'Lincoln', 'state_id' => 3966],
                ['name' => 'Slatersville', 'state_id' => 3966],
                ['name' => 'Woonsocket', 'state_id' => 3966],
                ['name' => 'North Smithfield', 'state_id' => 3966],
                ['name' => 'Providence', 'state_id' => 3966],
                ['name' => 'Cranston', 'state_id' => 3966],
                ['name' => 'North Providence', 'state_id' => 3966],
                ['name' => 'East Providence', 'state_id' => 3966],
                ['name' => 'Riverside', 'state_id' => 3966],
                ['name' => 'Rumford', 'state_id' => 3966],
                ['name' => 'Smithfield', 'state_id' => 3966],
                ['name' => 'Johnston', 'state_id' => 3966],
                ['name' => 'Ashaway', 'state_id' => 3966],
                ['name' => 'Block Island', 'state_id' => 3966],
                ['name' => 'Bradford', 'state_id' => 3966],
                ['name' => 'Carolina', 'state_id' => 3966],
                ['name' => 'Charlestown', 'state_id' => 3966],
                ['name' => 'Exeter', 'state_id' => 3966],
                ['name' => 'Hope Valley', 'state_id' => 3966],
                ['name' => 'Hopkinton', 'state_id' => 3966],
                ['name' => 'Kenyon', 'state_id' => 3966],
                ['name' => 'North Kingstown', 'state_id' => 3966],
                ['name' => 'Rockville', 'state_id' => 3966],
                ['name' => 'Saunderstown', 'state_id' => 3966],
                ['name' => 'Shannock', 'state_id' => 3966],
                ['name' => 'Slocum', 'state_id' => 3966],
                ['name' => 'Wakefield', 'state_id' => 3966],
                ['name' => 'Kingston', 'state_id' => 3966],
                ['name' => 'Narragansett', 'state_id' => 3966],
                ['name' => 'Peace Dale', 'state_id' => 3966],
                ['name' => 'Westerly', 'state_id' => 3966],
                ['name' => 'West Kingston', 'state_id' => 3966],
                ['name' => 'Wood River Junction', 'state_id' => 3966],
                ['name' => 'Wyoming', 'state_id' => 3966]
            ]);
        }
    }
}
