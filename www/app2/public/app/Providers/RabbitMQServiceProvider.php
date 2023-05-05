<?php

namespace App\Providers;

use App\Helpers\RabbitMQConfig;
use App\Helpers\RabbitMQMethodType;
use App\Helpers\RabbitMQQueue;
use App\Helpers\RabbitMQType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Enum;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Illuminate\Support\Facades\App;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQServiceProvider extends ServiceProvider
{
    public static $instance;

    public function register()
    {
        Log::info('RabbitMQServiceProvider::register()');
        self::$instance = $this;
        if (App::runningInConsole()) {
            self::consumeMessages();
        }
    }

    public static function consumeMessages()
    {
        try {
            Log::info('RabbitMQServiceProvider::consumeMessages()');
            $connection = new AMQPStreamConnection(
                env('RABBITMQ_HOST'),
                env('RABBITMQ_PORT'),
                env('RABBITMQ_USERNAME'),
                env('RABBITMQ_PASSWORD'),
                env('RABBITMQ_VHOST')
            );

            $channel = $connection->channel();

            $channel->queue_declare(env('RABBITMQ_QUEUE'), false, true, false, false);

            $callback = function ($message) {
                $data = $message->body;
                $body = json_decode($data, true);

                Log::info('RabbitMQServiceProvider::consumeMessages() - ' . json_encode($body));
                $validator  = Validator::make($body, [
//                    queue is required
                    'queue' => [new Enum(RabbitMQQueue::class), 'required'],
                    'type' => [new Enum(RabbitMQType::class), 'required'],
                    'method' => [new Enum(RabbitMQMethodType::class), 'required'],
                    'data' => '',
                ]);

                if($validator->fails()) {
                    Log::error('Error Occurred: ' . $validator->errors()->toJson());
                }


                $queue = RabbitMQQueue::tryFrom($body['queue']) ?? RabbitMQQueue::Default;
                $type = RabbitMQType::tryFrom($body['type']) ?? RabbitMQType::Direct;
                $method = RabbitMQMethodType::tryFrom($body['method']) ?? RabbitMQMethodType::ASYNC;
                $data = $body['data'];
                $config = new RabbitMQConfig($queue, $type, $method, $data);

                $listener = self::$instance->app->make(\App\Listeners\RabbitMQListener::class);
                $listener->handle($config);
            };

            $channel->basic_consume(env('RABBITMQ_QUEUE'), '', false, true, false, false, $callback);

            while ($channel->is_consuming()) {
                $channel->wait(null, true, 1000);
            }

            $channel->close();
            $connection->close();
        } catch (\Exception $e) {
            Log::info('Error Occurred: ' . $e->getMessage());
            return [
                'message' => $e->getMessage(),
                'timestamp' => time(),

            ];
        }
    }
}
