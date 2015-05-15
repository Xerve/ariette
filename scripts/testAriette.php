<?php

$success = null;
$phpcs = __DIR__.'/../vendor/bin/phpcs';
$phpunit = __DIR__.'/../vendor/bin/phpunit';

// PHP_codesniffer checks
passthru($phpcs.
         ' ariette', $success);

if ($success !== 0) { exit(1); }

// PHPUnit
passthru($phpunit.
         ' --bootstrap ariette/bootstrap.php '.
         ' tests/ariette', $success);

if ($success !== 0) { exit(1); }
