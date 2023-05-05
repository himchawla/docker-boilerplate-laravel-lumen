<?php

namespace app\Helpers;

class RabbitMQConfig
{
    private RabbitMQQueue $queue;
    private RabbitMQType $type;
    private RabbitMQMethodType $method;

    private $data;


    public function __construct(RabbitMQQueue $_queue, RabbitMQType $_type, RabbitMQMethodType $_method, $data) {
        $this->type = $_type;
        $this->queue = $_queue;
        $this->method = $_method;
        $this->data = $data;
    }

    public function getData() {
        return $this->data;
    }

    public function getMethod(): RabbitMQMethodType
    {
        return $this->method;
    }
    public static function getQueue(RabbitMQQueue $_queue): RabbitMQConfig
    {
        return new RabbitMQConfig($_queue, RabbitMQType::Queue, RabbitMQMethodType::SYNC);
    }

    public static function getDirect(RabbitMQQueue $_queue): RabbitMQConfig
    {
        return new RabbitMQConfig($_queue, RabbitMQType::Direct, RabbitMQMethodType::SYNC);
    }

    public static function getDirectAsync(RabbitMQQueue $_queue): RabbitMQConfig
    {
        return new RabbitMQConfig($_queue, RabbitMQType::Direct, RabbitMQMethodType::ASYNC);
    }
    public static function getExchange(RabbitMQQueue $_queue): RabbitMQConfig
    {
        return new RabbitMQConfig($_queue, RabbitMQType::Exchange, RabbitMQMethodType::SYNC);
    }
}
