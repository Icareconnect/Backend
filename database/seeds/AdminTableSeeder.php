<?php

use Illuminate\Database\Seeder;
use App\User;
class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleTableSeeder::class);

    	$user = User::firstOrCreate(['email'=>'admin@admin.com']);
		$user->password = bcrypt('admin@123');
		$user->name = 'Admin';
    	if($user->save()){
    		$role = App\Model\Role::where('name','admin')->first();
    		if(!$user->hasRole('admin')){
                $user->roles()->attach($role);
    		}
    	}
        $user = User::firstOrCreate(['email'=>'admin1@admin.com']);
        $user->password = bcrypt('admin@123');
        $user->name = 'Admin1';
        if($user->save()){
            $role = App\Model\Role::where('name','admin')->first();
            if(!$user->hasRole('admin')){
                $user->roles()->attach($role);
            }
        }
        $user = User::firstOrCreate(['email'=>'admin2@admin.com']);
        $user->password = bcrypt('admin@123');
        $user->name = 'Admin2';
        if($user->save()){
            $role = App\Model\Role::where('name','admin')->first();
            if(!$user->hasRole('admin')){
                $user->roles()->attach($role);
            }
        }
        $user = User::firstOrCreate(['email'=>'admin3@admin.com']);
        $user->password = bcrypt('admin@123');
        $user->name = 'Admin3';
        if($user->save()){
            $role = App\Model\Role::where('name','admin')->first();
            if(!$user->hasRole('admin')){
                $user->roles()->attach($role);
            }
        }
    }
}
