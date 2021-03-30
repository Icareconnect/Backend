<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleTableSeeder::class);
        $this->call(EnableServiceTableData::class);
        $this->call(CreateCurrencyTable::class);
        $this->call(ServiceTableSeeder::class);
        $this->call(CreateStateCitySeeder::class);
    }
}
