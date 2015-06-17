<?php

define('PHPCS', __DIR__.'/../vendor/bin/phpcs');
define('PHPUNIT', __DIR__.'/../vendor/bin/phpunit');

function CLI($command) {
    $success = null;

    passthru($command, $success);

    if ($success !== 0) {
        exit(1);
    }
}

function testAriette() {
    // CLI(PHPCS.
    //     ' ariette');

    CLI(PHPUNIT.
        ' --bootstrap ariette/bootstrap.php '.
        ' -d ARIETTE_REQUIRE_CORE=no'.
        ' tests/ariette');
}

function testIt() {
    // CLI(PHPCS.
    //     ' it', $success);
}

function testCore() {
    define('ARIETTE_REQUIRE_IT', true);

    require __DIR__.'/../ariette/bootstrap.php';
    require __DIR__.'/../tests/core/config.php';

    if (!It\It::lives()) {
        exit(1);
    }
}

$options = getopt('', ['ariette', 'it', 'core']);

if (array_key_exists('ariette', $options)) {
    testAriette();
} elseif (array_key_exists('it', $options)) {
    testIt();
} elseif (array_key_exists('core', $options)) {
    testCore();
} else {
    echo "Usage: \n".
         "  --ariette: Test ariette's internals\n".
         "  --it: Test ariette's testing framework's internals\n".
         "  --core: Test ariette's core module with It\n";
}
