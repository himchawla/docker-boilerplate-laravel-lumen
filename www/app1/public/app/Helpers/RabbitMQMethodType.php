<?php

namespace app\Helpers;

enum RabbitMQMethodType: string
{
    case SYNC = 'sync';
    case ASYNC = 'async';
}
