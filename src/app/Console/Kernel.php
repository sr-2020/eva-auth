<?php

namespace App\Console;

use App\Console\Commands\HotCache;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        HotCache::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if (true === env('APP_BACKUP', false)) {
            $schedule->command('backup:clean')->daily()->at('00:30');
            $schedule->command('backup:run')->hourly();
        }
    }
}
