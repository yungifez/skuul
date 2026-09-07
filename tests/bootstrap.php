<?php

require dirname(__DIR__).'/vendor/autoload.php';

/*
 * A test marked #[RunInSeparateProcess] runs in a child PHP process that
 * PHPUnit builds from a template. The template loads the autoloader from
 * PHPUNIT_COMPOSER_INSTALL, and loads nothing at all when that constant is
 * missing, so every PHPUnit class in the child is undefined. The phpunit
 * binary defines it; the pest binary does not.
 */
if (!defined('PHPUNIT_COMPOSER_INSTALL')) {
    define('PHPUNIT_COMPOSER_INSTALL', dirname(__DIR__).'/vendor/autoload.php');
}

$parentCommand = @file_get_contents('/proc/'.posix_getppid().'/cmdline');
$isChildProcess = is_string($parentCommand)
    && (str_contains($parentCommand, 'phpunit') || str_contains($parentCommand, 'pest'));
$skipLock = getenv('SKUUL_SKIP_TEST_LOCK') === '1';
$lock = null;

if (!$isChildProcess && !$skipLock) {
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
