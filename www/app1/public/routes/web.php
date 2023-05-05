<?php

/** @var Router $router */

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
use app\Helpers\RabbitMQQueue;
use Laravel\Lumen\Routing\Router;

$router->get('/', function () use ($router) {
//    $rabbitmq = new RabbitMQ('rabbitmq', 5672, 'guest', 'guest', '/');

//    $rabbitmq->publish('my_queue', 'Hello, world! at ' . date('Y-m-d H:i:s') . '');

    return 'Message sent!';
});
$router->get('/send-event-sync', 'Controller@sync');
$router->get('/send-event-async', 'Controller@index');


