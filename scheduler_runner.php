<?php
while (true) {
      \Log::info('kkk ');
    echo '[' . date('Y-m-d H:i:s') . "] Running Laravel schedule...\n";
    exec('php artisan schedule:run');
    sleep(60);  // Run every minute
} 
