<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Config;
use App\Model\CustomModuleApp;
use App\Model\ClientQueue;
use Exception;
class ClientDataBaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'client:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ClientQueue::where(['status'=>'queue'])
        ->chunk(5, function($clients) {
            foreach ($clients as $key=>$quque) {
                if($quque->status!=='queue'){
                    continue;
                }

                $this->info('Client DataBase Creating...');
                // $quque->status = 'in_progress';
                // $quque->step = 'migration';
                // $quque->percentage = '10%';
                // $quque->save();
                $database_name = 'db_'.$quque->client->domain_name.'_'.$quque->client->id;
                try{
                    \DB::statement("drop database $database_name;");
                }catch(Exception $ex){
                    $this->info('Client DataBase Exception...'.$ex->getMessage());
                }
                \DB::statement("create database $database_name;");
                $default = [
                    'driver' => 'mysql',
                    'url' => '',
                    'host' => '127.0.0.1',
                    'port' => '3306',
                    'database' => $database_name,
                    'username' => 'root',
                    'password' => 'codebrew',
                    'unix_socket' =>'',
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'prefix_indexes' => true,
                    'strict' => false,
                    'engine' => null,
                    // 'options' => extension_loaded('pdo_mysql') ? array_filter([
                    //     \PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
                    // ]) : [],
                ];
                Config::set("database.connections.$database_name", $default);
                Artisan::call('migrate', ['--database' => $database_name]);
                Artisan::call('db:seed', ['--class' => 'AdminTableSeeder','--database' => $database_name]);
                $this->info('Client DataBase Created...'.$database_name);
            }
        });
    }
}
