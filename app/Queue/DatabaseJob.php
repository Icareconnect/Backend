<?php
namespace App\Queue;

use App\Model\Client;
use App\Helpers\Helper;

class DatabaseJob extends \Illuminate\Queue\Jobs\DatabaseJob
{
    public function fire()
    {
        if ($this->job->client_id) {
            $res = Helper::connectByClient($this->job->client_id);
        }
        parent::fire();
    }
}