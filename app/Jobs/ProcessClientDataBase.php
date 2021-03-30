<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Model\Client;
use Config;
use Exception;
use Illuminate\Support\Facades\Artisan;
class ProcessClientDataBase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $client_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($client_id)
    {
        $this->client_id = $client_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = Client::where('id',$this->client_id)->first();
        $client->client_status = 'migrating';
        $client->save();
        $database_name = 'db_'.$client->domain_name;
        // print_r($client);die;
        \DB::statement("create database $database_name;");
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
        Artisan::call('migrate', ['--database' => $database_name]);
        Artisan::call('db:seed', ['--class' => 'AdminTableSeeder','--database' => $database_name]);
        \DB::disconnect($database_name);
        \DB::reconnect('godpanel');
        $client->client_status = 'completed';
        $client->client_key = $database_name;
        $client->client_secret =$database_name;
        $client->save();
    }
}
