<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\RabbitMQConsumeCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
//        run every minute without overlap
//        $schedule->command('rabbitmq:consume')->everyMinute()->withoutOverlapping();
//        run it in background dev/null 2>&1
//        $schedule->command('rabbitmq:consume')->everyMinute()->withoutOverlapping()->runInBackground();
//        run it in background dev/null 2>&1 and log it
        $schedule->command('rabbitmq:consume')->everyMinute()->withoutOverlapping()->runInBackground()->sendOutputTo(storage_path('logs/rabbitmq.log'));
    }
}
