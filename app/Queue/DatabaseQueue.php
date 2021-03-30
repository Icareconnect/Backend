<?php
namespace App\Queue;

use App\Queue\DatabaseJob;
use Config;
class DatabaseQueue extends \Illuminate\Queue\DatabaseQueue
{

    protected function buildDatabaseRecord($queue, $payload, $availableAt, $attempts = 0)
    {
        $queueRecord = [
            'queue' => $queue,
            'attempts' => $attempts,
            'reserved_at' => null,
            'available_at' => $availableAt,
            'created_at' => $this->currentTime(),
            'payload' => $payload,
        ];
        if(Config::get('client_connected') && Config::get('client_data')){
            $queueRecord['client_id'] = Config::get('client_data')->id;
        }
        return $queueRecord;
    }

    protected function marshalJob($queue, $job)
    {
        $job = $this->markJobAsReserved($job);
        return new DatabaseJob(
            $this->container, $this, $job, $this->connectionName, $queue
        );
    }
}