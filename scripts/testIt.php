<?php

$success = null;
$phpcs = __DIR__.'/../vendor/bin/phpcs';
$phpunit = __DIR__.'/../vendor/bin/phpunit';

// PHP_codesniffer checks
passthru($phpcs.
         ' it', $success);

if ($success !== 0) { exit(1); }
