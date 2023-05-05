<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQ
{
    private $connection;

    private static $messages = [];

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
        $rand = random_int(1111, 9999);
        $channel = $this->connection->channel();

        $channel->queue_declare($queueName, false, true, false, false);

        $message = null;

        $callback = $_callback ?? function ($msg) use (&$message){
            $message = $msg->body;
        };

        $channel->basic_consume($queueName, '', false, true, false, false, $callback);

        $timeout = 0;
        try {
            if ($channel->is_consuming()) {
                $channel->wait(null, false, 5);
            }
        }
        catch (\Exception $e) {
            return false;
        }


        $channel->close();
        $this->connection->close();
        return $message;
    }

    public function delete($queueName) {
        $channel = $this->connection->channel();
        $channel->queue_delete($queueName);
        $channel->close();
    }
}
