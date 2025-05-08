<?php
while (true) {
    echo "[" . date('Y-m-d H:i:s') . "] Running Laravel schedule...\n";
    exec('php artisan schedule:run');
    sleep(60);
}