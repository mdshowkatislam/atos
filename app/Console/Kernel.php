<?php

namespace App\Console;

use App\Console\Commands\SyncAccessToMySQL;
use App\Models\ScheduledSetting;
use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\PushSelectedColumn;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        SyncAccessToMySQL::class,
    ];

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }

    protected function schedule(Schedule $schedule): void
    {
        $syncMethodMap = [
            1 => 'everyMinute',
            2 => 'everyThirtyMinutes',
            3 => 'hourly',
            4 => 'everyTwoHours',
            5 => ['dailyAt', ['13:00']],
            6 => ['twiceDaily', [1, 13]],
            7 => ['between', ['9:00', '17:00']],
        ];

        $syncTime = ScheduledSetting::where('key', 'sync_time')->value('value') ?? 1;

        if (isset($syncMethodMap[$syncTime])) {
            $method = $syncMethodMap[$syncTime];

            if (is_array($method)) {
                $schedule->command('access:sync')->{$method[0]}(...$method[1]);
            } else {
                $schedule->command('access:sync')->$method();
            }
        } else {
            $schedule->command('access:sync')->everyMinute();
        }
            $schedule
            ->job(new PushSelectedColumn('orders', ['id', 'total']))
            ->everyThreeMinutes();

        // Optional logging task
        $schedule->call(function () {
            \Log::info('Scheduled task runner hit at ' . now());
        })->everyMinute();
    
    }
}
