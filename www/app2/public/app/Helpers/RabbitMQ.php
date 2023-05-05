<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQ
{
    private $connection;

    public function __construct($host, $port, $username, $password, $vhost)
    {
        $this->connection = new AMQPStreamConnection($host, $port, $username, $password, $vhost);
    }


    public function publish($queueName, $message)
    {
        Log::info('Trying to publish ' . json_encode($message));
        $channel = $this->connection->channel();

        $channel->queue_declare($queueName, false, true, false, false);

        $msg = new \PhpAmqpLib\Message\AMQPMessage($message);

        $channel->basic_publish($msg, '', $queueName);

        $channel->close();
        $this->connection->close();
    }

    public function recieve($queueName, $_callback = null)
    {
        $channel = $this->connection->channel();

        $channel->queue_declare($queueName, false, true, false, false);

        // dd(' [*] Waiting for messages. To exit press CTRL+C');
        $callback = $_callback ?? function ($msg) {
            echo " [x] Received ", $msg->body, "\n";
        };

        $channel->basic_consume($queueName, '', false, true, false, false, $callback);

        $timeout = 0;
        if ($channel->is_consuming()) {
          $channel->wait(null, true , 5000);
        }

        echo " [x] Done\n";

        $channel->close();
        $this->connection->close();
    }
}
