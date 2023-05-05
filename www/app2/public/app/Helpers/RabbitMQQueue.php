<?php

namespace App\Helpers;

enum RabbitMQQueue: string
{
    case UserService = 'user_queue';
    case PayoutService = 'payout_queue';

    case Default = 'default';
}
