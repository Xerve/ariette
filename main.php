<?php

require_once "vendor/autoload.php";

use Pt\Pt;

Pt::module("Test", ['Pt::config',
                    'Pt::redirect'], function($config, $redirect) {
    $config('Test::test', [
        "hole" => 10
    ]);

    $redirect('Test::me', 'Test::test');
})

->component("test",
            ['*Middleware::something',
             '*Pt::config'],
             function($input) {
    return $input;
});

echo Pt::run([
    '$path' => "Test::test",
    "lol" => 5
]), PHP_EOL;
