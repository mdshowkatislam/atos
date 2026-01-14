<?php
header('Content-Type: text/plain');
echo "max_execution_time: " . ini_get('max_execution_time') . "\n";
echo "max_input_time: " . ini_get('max_input_time') . "\n";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n";

echo 'shell_exec: ' . (function_exists('shell_exec') ? 'Enabled' : 'Disabled') . '<br>';
echo 'exec: ' . (function_exists('exec') ? 'Enabled' : 'Disabled') . '<br>';
echo 'popen: ' . (function_exists('popen') ? 'Enabled' : 'Disabled') . '<br>';


