<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');



Schedule::command('access:sync')->everyMinute();

// Optional: log to confirm scheduler runs but not running every minute 
Schedule::call(function () {
    \Log::info('Scheduled task runner hit at ' . now());
})->everyMinute();

