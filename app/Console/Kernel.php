<?php

namespace App\Console;

use App\Console\Commands\SyncAccessToMySQL;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        SyncAccessToMySQL::class,
    ];

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
    }

    protected function schedule(Schedule $schedule): void
    {
        // Process queued access sync jobs every minute
        $schedule->command('queue:work', ['--max-attempts' => 1, '--max-time' => 55])
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground();
    }
