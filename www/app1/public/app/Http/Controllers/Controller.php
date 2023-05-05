<?php

namespace App\Http\Controllers;

use App\Helpers\RabbitMQ;
use App\Helpers\RabbitMQQueue;
use Laravel\Lumen\Routing\Controller as BaseController;
class Controller extends BaseController
{
    //

    public function index() {
//        dd(RabbitMQQueue::PayoutService);
        $rabbitmq = new RabbitMQ('rabbitmq', 5672, 'guest', 'guest', '/');
        $timeStamp = time();
        $data = [
            'queue' => \App\Helpers\RabbitMQQueue::PayoutService->value,
            'type' => \App\Helpers\RabbitMQType::Direct->value,
            'method' => \App\Helpers\RabbitMQMethodType::ASYNC->value,
            'data' => [
                'message' => 'Hello, world! at ' . date('Y-m-d H:i:s') . '',
                'timestamp' => $timeStamp
            ]
        ];
        $rabbitmq->publish('my_queue', json_encode($data));
        return 'Message sent!';
    }

    public function sync() {
//        dd(RabbitMQQueue::PayoutService);
        $rabbitmq = new RabbitMQ('rabbitmq', 5672, 'guest', 'guest', '/');
        $timeStamp = time();
        $data = [
            'queue' => \App\Helpers\RabbitMQQueue::PayoutService->value,
            'type' => \App\Helpers\RabbitMQType::Direct->value,
            'method' => \App\Helpers\RabbitMQMethodType::SYNC->value,
            'data' => [
                'message' => 'Hello, world! at ' . date('Y-m-d H:i:s') . '',
                'timestamp' => $timeStamp
            ]
        ];
        $rabbitmq->publish('my_queue', json_encode($data));

        $msg = $rabbitmq->recieve('Callback' . $timeStamp);
        $rabbitmq->delete('Callback' . $timeStamp);

        return $msg;
    }

}
