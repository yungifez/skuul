<?php

require dirname(__DIR__).'/vendor/autoload.php';

$lockPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'skuul-phpunit.lock';
$lock = fopen($lockPath, 'c');

if (!is_resource($lock) || !flock($lock, LOCK_EX)) {
    throw new RuntimeException('Unable to acquire the Skuul PHPUnit database lock.');
}

register_shutdown_function(static function () use ($lock): void {
    flock($lock, LOCK_UN);
    fclose($lock);
});
