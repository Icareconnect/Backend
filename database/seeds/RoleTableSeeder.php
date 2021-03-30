<?php

use Illuminate\Database\Seeder;
use App\Model\Role;
class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$admin = Role::firstOrCreate(['name'=>'admin']);
        $customer = Role::firstOrCreate(['name'=>'customer']);
        $service_provider = Role::firstOrCreate(['name'=>'service_provider']);
        $archived_user = Role::firstOrCreate(['name'=>'archived_user']);
    }
}
