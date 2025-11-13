<?php
use Illuminate\Support\Facades\Log;
while (true) {
      Log::info('from ATOSQL/schedule_runner.php');
    echo '[' . date('Y-m-d H:i:s') . "] Running Laravel schedule...\n";
    exec('php artisan schedule:run');
    sleep(60);  // Run every minute
} 
