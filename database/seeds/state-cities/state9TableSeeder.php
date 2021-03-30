<?php

use Illuminate\Database\Seeder;

class state9TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Create Cities for the state of DE - Delaware.
        //If the table 'cities' exists, insert the data to the table.
        if (DB::table('cities')->get()->count() >= 0) {
            DB::table('cities')->insert([
                ['name' => 'Dover', 'state_id' => 3929],
                ['name' => 'Dover Afb', 'state_id' => 3929],
                ['name' => 'Camden Wyoming', 'state_id' => 3929],
                ['name' => 'Cheswold', 'state_id' => 3929],
                ['name' => 'Clayton', 'state_id' => 3929],
                ['name' => 'Felton', 'state_id' => 3929],
                ['name' => 'Frederica', 'state_id' => 3929],
                ['name' => 'Harrington', 'state_id' => 3929],
                ['name' => 'Hartly', 'state_id' => 3929],
                ['name' => 'Houston', 'state_id' => 3929],
                ['name' => 'Kenton', 'state_id' => 3929],
                ['name' => 'Little Creek', 'state_id' => 3929],
                ['name' => 'Magnolia', 'state_id' => 3929],
                ['name' => 'Marydel', 'state_id' => 3929],
                ['name' => 'Smyrna', 'state_id' => 3929],
                ['name' => 'Viola', 'state_id' => 3929],
                ['name' => 'Woodside', 'state_id' => 3929],
                ['name' => 'Bear', 'state_id' => 3929],
                ['name' => 'Newark', 'state_id' => 3929],
                ['name' => 'Claymont', 'state_id' => 3929],
                ['name' => 'Delaware City', 'state_id' => 3929],
                ['name' => 'Hockessin', 'state_id' => 3929],
                ['name' => 'Kirkwood', 'state_id' => 3929],
                ['name' => 'Middletown', 'state_id' => 3929],
                ['name' => 'Montchanin', 'state_id' => 3929],
                ['name' => 'New Castle', 'state_id' => 3929],
                ['name' => 'Odessa', 'state_id' => 3929],
                ['name' => 'Port Penn', 'state_id' => 3929],
                ['name' => 'Rockland', 'state_id' => 3929],
                ['name' => 'Saint Georges', 'state_id' => 3929],
                ['name' => 'Townsend', 'state_id' => 3929],
                ['name' => 'Winterthur', 'state_id' => 3929],
                ['name' => 'Yorklyn', 'state_id' => 3929],
                ['name' => 'Wilmington', 'state_id' => 3929],
                ['name' => 'Bethany Beach', 'state_id' => 3929],
                ['name' => 'Bethel', 'state_id' => 3929],
                ['name' => 'Bridgeville', 'state_id' => 3929],
                ['name' => 'Dagsboro', 'state_id' => 3929],
                ['name' => 'Delmar', 'state_id' => 3929],
                ['name' => 'Ellendale', 'state_id' => 3929],
                ['name' => 'Fenwick Island', 'state_id' => 3929],
                ['name' => 'Frankford', 'state_id' => 3929],
                ['name' => 'Georgetown', 'state_id' => 3929],
                ['name' => 'Greenwood', 'state_id' => 3929],
                ['name' => 'Harbeson', 'state_id' => 3929],
                ['name' => 'Laurel', 'state_id' => 3929],
                ['name' => 'Lewes', 'state_id' => 3929],
                ['name' => 'Lincoln', 'state_id' => 3929],
                ['name' => 'Milford', 'state_id' => 3929],
                ['name' => 'Millsboro', 'state_id' => 3929],
                ['name' => 'Millville', 'state_id' => 3929],
                ['name' => 'Milton', 'state_id' => 3929],
                ['name' => 'Nassau', 'state_id' => 3929],
                ['name' => 'Ocean View', 'state_id' => 3929],
                ['name' => 'Rehoboth Beach', 'state_id' => 3929],
                ['name' => 'Seaford', 'state_id' => 3929],
                ['name' => 'Selbyville', 'state_id' => 3929]
            ]);
        }
    }
}
