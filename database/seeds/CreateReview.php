<?php

use Illuminate\Database\Seeder;
use App\Model\Feedback;
use App\User;
use Faker\Factory as Faker;
class CreateReview extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $database_name = 'db_education';
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
        // Config::set("client_id", $client->id);
        // Config::set("client_connected",true);
        // Config::set("client_data",$client);
        DB::setDefaultConnection($database_name);
        DB::purge($database_name);
    	$consultants = User::whereHas('roles', function ($query) {
                           $query->where('name','service_provider');
                        })->pluck('id','name');
		$customers = User::whereHas('roles', function ($query) {
                           $query->where('name','customer');
                        })->pluck('id')->toArray();
    	foreach ($consultants as $name => $vendor_id) {
    		$faker = Faker::create();
    		$random_keys=array_rand($customers);
    		$from_user = $customers[$random_keys];
    		Feedback::create([
    			'consultant_id'=>$vendor_id,
    			'from_user'=>$from_user,
    			'rating'=>$faker->numberBetween(3,5),
    			'comment'=>"Hands down the best teacher I have worked with, he understands what are some important pitfalls that people experience in this subject and always knows the best way to get around it."
    		]);

    		$faker = Faker::create();
    		$random_keys=array_rand($customers);
    		$from_user = $customers[$random_keys];
    		Feedback::create([
    			'consultant_id'=>$vendor_id,
    			'from_user'=>$from_user,
    			'rating'=>$faker->numberBetween(3,5),
    			'comment'=>"I have studied under 5 different teachers for the same subject this year, and I can guarantee the fact no one comes even close to the experience and learnings that you can get from here. She has a very hands on approach and you never feel discouraged to ask a question."
    		]);


    		$faker = Faker::create();
    		$random_keys=array_rand($customers);
    		$from_user = $customers[$random_keys];
    		Feedback::create([
    			'consultant_id'=>$vendor_id,
    			'from_user'=>$from_user,
    			'rating'=>$faker->numberBetween(3,5),
    			'comment'=>"Attended 2 classroom session and a personal session here, I never thought that I would understand this subject so well. The fact the reasoning behind everything is explained so well, you never get the intention cram anything."
    		]);

    		$faker = Faker::create();
    		$random_keys=array_rand($customers);
    		$from_user = $customers[$random_keys];
    		Feedback::create([
    			'consultant_id'=>$vendor_id,
    			'from_user'=>$from_user,
    			'rating'=>$faker->numberBetween(3,5),
    			'comment'=>"Friendly, Intelligent, informative and probably the best teacher I have ever had. Thank you for your time and effort, I wish I could have found out about you earlier, and thanks to Royo Education to put teachers like you in the same platform."
    		]);

    		$faker = Faker::create();
    		$random_keys=array_rand($customers);
    		$from_user = $customers[$random_keys];
    		Feedback::create([
    			'consultant_id'=>$vendor_id,
    			'from_user'=>$from_user,
    			'rating'=>$faker->numberBetween(3,5),
    			'comment'=>"Attending my 5th session here, and finally the subject feels crystal clear. It is true every subject is easy when you have great teachers and it definitely shows. I avoided my fundamentals for so long until my session here, where I realised that if you know fundamentals everything else is easy. "
    		]);

            $faker = Faker::create();
            $random_keys=array_rand($customers);
            $from_user = $customers[$random_keys];
            Feedback::create([
                'consultant_id'=>$vendor_id,
                'from_user'=>$from_user,
                'rating'=>$faker->numberBetween(3,5),
                'comment'=>"Ran from this subject all my life, not anymore thanks to sir. Great approach, best methods, I will never ever run away from subjects or hard topics from now. It is not just the teaching but the mindset that you acquire in these sessions which will help me all my life."
            ]);

            $faker = Faker::create();
            $random_keys=array_rand($customers);
            $from_user = $customers[$random_keys];
            Feedback::create([
                'consultant_id'=>$vendor_id,
                'from_user'=>$from_user,
                'rating'=>$faker->numberBetween(3,5),
                'comment'=>"The sessions are good, there is nothing surprising about that, but the best part is that if you have any doubts or confusion you can always connect through chat and you will be given a good amount of time and attention no matter what time of day it is."
            ]);

            $faker = Faker::create();
            $random_keys=array_rand($customers);
            $from_user = $customers[$random_keys];
            Feedback::create([
                'consultant_id'=>$vendor_id,
                'from_user'=>$from_user,
                'rating'=>$faker->numberBetween(3,5),
                'comment'=>"Thank you so much for helping me clear my exam for this subject. I was sure that I was going to fail here. Never have been my fundamentals been so clear, I believe I can even help out my fellow students for the same concepts. "
            ]);

            $faker = Faker::create();
            $random_keys=array_rand($customers);
            $from_user = $customers[$random_keys];
            Feedback::create([
                'consultant_id'=>$vendor_id,
                'from_user'=>$from_user,
                'rating'=>$faker->numberBetween(3,5),
                'comment'=>"I dare you to find a better teacher on this subject, past 7 sessions have been eye openers for me. There are so many ways to grasp a single concept and you have to find the way that works best for you. Words that I will never forget. Thank you."
            ]);

            $faker = Faker::create();
            $random_keys=array_rand($customers);
            $from_user = $customers[$random_keys];
            Feedback::create([
                'consultant_id'=>$vendor_id,
                'from_user'=>$from_user,
                'rating'=>$faker->numberBetween(3,5),
                'comment'=>"Though you never ask for reviews, I believe you deserve the best ones. I have never seen anyone who has such an experimental approach to teaching and knows that every student need to be taught in a different way."
            ]);


            $faker = Faker::create();
            $random_keys=array_rand($customers);
            $from_user = $customers[$random_keys];
            Feedback::create([
                'consultant_id'=>$vendor_id,
                'from_user'=>$from_user,
                'rating'=>$faker->numberBetween(3,5),
                'comment'=>"The teaching methodologies, language and the lively classes are turning into sessions that my child looks forward to. Never did i think of singing to him while teaching him how dangerous strangers can be, Stranger- danger is a lot more fun & easy to learn now. Also, has helped him retain things better."
            ]);


            $faker = Faker::create();
            $random_keys=array_rand($customers);
            $from_user = $customers[$random_keys];
            Feedback::create([
                'consultant_id'=>$vendor_id,
                'from_user'=>$from_user,
                'rating'=>$faker->numberBetween(3,5),
                'comment'=>"Being from India, I have always wanted my kid to carry a bit of my culture and language with him. Gives me great satisfaction when I see him trying to initiate a conversation in Hindi and kudos to him and the teacher, they are not doing too bad, considering they only started like 15 days ago ."
            ]);

    		Feedback::updateReview($vendor_id);
    	}

    }
}
