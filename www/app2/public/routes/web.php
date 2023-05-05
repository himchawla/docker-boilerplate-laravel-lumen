<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use App\Helpers\RabbitMQ;
use App\Providers\RabbitMQServiceProvider;

$router->get('/', function () use ($router) {
     $rabbitmq = new RabbitMQ('rabbitmq', 5672, 'guest', 'guest', '/');

     $rabbitmq->recieve('my_queue');

    return 'Message sent to!';
});

$router->get('startListening', function () use ($router) {
    RabbitMQServiceProvider::consumeMessages();
});
