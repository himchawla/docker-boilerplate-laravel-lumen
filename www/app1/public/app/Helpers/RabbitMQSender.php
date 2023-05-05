<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitmqSender
{
    private $connection;
    private $channel;
    private $requestQueueName;
    private $responseQueueName;
    private $correlationId;
    private $callbackQueue;
    private $response;

    public function __construct()
    {
        dd(env('RABBITMQ_HOST'), env('RABBITMQ_PORT'), env('RABBITMQ_USERNAME'), env('RABBITMQ_PASSWORD'), env('RABBITMQ_VHOST'));
        $this->connection = new AMQPStreamConnection(env('RABBITMQ_HOST'), env('RABBITMQ_PORT'), env('RABBITMQ_USERNAME'), env('RABBITMQ_PASSWORD'), env('RABBITMQ_VHOST'));
        $this->channel = $this->connection->channel();
        list($this->responseQueueName, ,) = $this->channel->queue_declare("", false, false, true, false);
    }

    public function call($request)
    {
        $this->response = null;
        $this->correlationId = uniqid();
        $this->callbackQueue = $this->responseQueueName;

        $this->channel->basic_consume($this->responseQueueName, '', false, true, false, false, array($this, 'onResponse'));

        $msg = new AMQPMessage(
            $request,
            array('correlation_id' => $this->correlationId, 'reply_to' => $this->callbackQueue)
        );
        $this->channel->basic_publish($msg, '', 'rpc_queue');

        while (!$this->response) {
            $this->channel->wait();
        }

        return $this->response;
    }

    public function onResponse($rep)
    {
        if ($rep->get('correlation_id') == $this->correlationId) {
            $this->response = $rep->body;
        }
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
