<?php

namespace app\Console\Commands;

use App\Providers\RabbitMQServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
class RabbitMQConsumeCommand extends Command
{
    protected $signature = 'rabbitmq:consume';

    public function handle()
    {
        Log::info('RabbitMQConsumeCommand');
        RabbitMQServiceProvider::consumeMessages();
    }
}
