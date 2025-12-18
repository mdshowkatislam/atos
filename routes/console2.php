<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;
use App\Models\ScheduledSetting;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduler (Laravel 12 correct way)
|--------------------------------------------------------------------------
*/

Log::info('Scheduler registration started at ' . now());

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

Log::info('sync_time from DB: ' . $syncTime);

$event = Schedule::command('access:sync')
    ->withoutOverlapping();

if (isset($syncMethodMap[$syncTime])) {
    $method = $syncMethodMap[$syncTime];

    if (is_array($method)) {
        $event->{$method[0]}(...$method[1]);
        $usedMethod = $method[0];
    } else {
        $event->$method();
        $usedMethod = $method;
    }
} else {
    $event->everyMinute();
    $usedMethod = 'everyMinute (fallback)';
    Log::warning('Invalid sync_time, fallback applied');
}

Log::info('access:sync scheduled using: ' . $usedMethod);
