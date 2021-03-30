<?php

namespace App\Queue;

use App\Queue\DatabaseQueue;

class DatabaseConnector extends \Illuminate\Queue\Connectors\DatabaseConnector
{

    public function connect(array $config)
    {
        return new DatabaseQueue(
            $this->connections->connection($config['connection'] ?? null),
            $config['table'],
            $config['queue'],
            $config['retry_after'] ?? 60
        );
    }
}