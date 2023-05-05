<?php

namespace app\Helpers;

enum RabbitMQType: string
{
    case Direct = 'direct';
    case Topic = 'topic';
    case Fanout = 'fanout';
    case Headers = 'headers';
    case Queue = 'queue';
    case Exchange = 'exchange';
}
