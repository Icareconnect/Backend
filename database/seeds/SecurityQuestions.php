<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Model\SecurityQuestion;
class SecurityQuestions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $database_name = 'db_mp2r';
        $default = [
            'driver' => env('DB_CONNECTION','mysql'),
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT'),
            'database' => $database_name,
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null
        ];
        Config::set("database.connections.$database_name", $default);
        DB::setDefaultConnection($database_name);
        SecurityQuestion::firstOrCreate([
			'enable'=>1,
			'type'=>'question1',
			'question'=>"In what town or city did you meet your spouse or partner?"
    	]);
    	SecurityQuestion::firstOrCreate([
			'enable'=>1,
			'type'=>'question1',
			'question'=>"What was the street name you lived on as a child?"
    	]);
    	SecurityQuestion::firstOrCreate([
			'enable'=>1,
			'type'=>'question1',
			'question'=>"What was the make of your first car?"
    	]);
    	SecurityQuestion::firstOrCreate([
			'enable'=>1,
			'type'=>'question1',
			'question'=>"In what city does your nearest sibling live?"
    	]);
    	SecurityQuestion::firstOrCreate([
			'enable'=>1,
			'type'=>'question1',
			'question'=>"What is the name of your favorite childhood friend?"
    	]);

    	SecurityQuestion::firstOrCreate([
			'enable'=>1,
			'type'=>'question2',
			'question'=>"In what city or town was your first job?"
    	]);
    	SecurityQuestion::firstOrCreate([
			'enable'=>1,
			'type'=>'question2',
			'question'=>"Where were you when you had your first kiss?"
    	]);
    	SecurityQuestion::firstOrCreate([
			'enable'=>1,
			'type'=>'question2',
			'question'=>"What is your mother's maiden name?"
    	]);
    	SecurityQuestion::firstOrCreate([
			'enable'=>1,
			'type'=>'question2',
			'question'=>"What is the name of your elementary school?"
    	]);
    	SecurityQuestion::firstOrCreate([
			'enable'=>1,
			'type'=>'question2',
			'question'=>"In what city or town did your mother and father meet?"
    	]);



    	SecurityQuestion::firstOrCreate([
			'enable'=>1,
			'type'=>'question3',
			'question'=>"What was your childhood nickname?"
    	]);
    	SecurityQuestion::firstOrCreate([
			'enable'=>1,
			'type'=>'question3',
			'question'=>"What is your favorite food?"
    	]);
    	SecurityQuestion::firstOrCreate([
			'enable'=>1,
			'type'=>'question3',
			'question'=>"Who was your childhood hero?"
    	]);
    	SecurityQuestion::firstOrCreate([
			'enable'=>1,
			'type'=>'question3',
			'question'=>"What was the first concert you attended?"
    	]);
    	SecurityQuestion::firstOrCreate([
			'enable'=>1,
			'type'=>'question3',
			'question'=>"What is your grandmother's first name?"
    	]);


    }
}
