<?php

require dirname(__DIR__).'/vendor/autoload.php';

$parentCommand = @file_get_contents('/proc/'.posix_getppid().'/cmdline');
$isPhpUnitChildProcess = is_string($parentCommand) && str_contains($parentCommand, 'phpunit');
$skipLock = getenv('SKUUL_SKIP_TEST_LOCK') === '1';
$lock = null;

if (!$isPhpUnitChildProcess && !$skipLock) {
    $lockPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'skuul-phpunit.lock';
    $lock = fopen($lockPath, 'c');

    if (!is_resource($lock) || !flock($lock, LOCK_EX)) {
        throw new RuntimeException('Unable to acquire the Skuul PHPUnit database lock.');
    }
}

if (is_resource($lock)) {
    register_shutdown_function(static function () use ($lock): void {
        flock($lock, LOCK_UN);
        fclose($lock);
    });
}
