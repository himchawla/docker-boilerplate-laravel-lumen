<?php

namespace App\Listeners;

use App\Helpers\RabbitMQ;
use app\Helpers\RabbitMQConfig;
use app\Helpers\RabbitMQMethodType;
use Illuminate\Support\Facades\Log;

class RabbitMQListener
{
    /**
     * Handle the event.
     *
     * @param  array  $data
     * @return void
     */
    public function handle(RabbitMQConfig $_config)
    {
        $data = $_config->getData();
        // mkdir(storage_path('logss'), 0777, true);
        Log::info('Received message from RabbitMQ', ['data' => $data]);

        if(is_string($data)) {
            Log::info('Data is' . $data);

            $data = json_decode($data, true);
        }
        else {
            Log::info('Data is' . json_encode($data));
        }

        if($_config->getMethod() == RabbitMQMethodType::ASYNC) {
            return true;
        }
        Log::info('Data is' . json_encode($data));
        $queue = 'Callback' . $data['timestamp'];
        $returnData = [
            'message' => 'Callback received',
            'timestamp' => time()
        ];
        $rabbitMQ = new RabbitMQ('rabbitmq', 5672, 'guest', 'guest', '/');
        $rabbitMQ->publish($queue, json_encode($returnData));
        return true;
        // Do something with the message data
    }
}
