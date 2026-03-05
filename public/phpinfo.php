<?php
echo "SAPI: " . php_sapi_name() . PHP_EOL;
echo "PHP: " . PHP_VERSION . PHP_EOL;
echo "INI: " . php_ini_loaded_file() . PHP_EOL;
echo "CURL: " . (function_exists('curl_init') ? 'YES' : 'NO') . PHP_EOL;