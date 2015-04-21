<?php

require_once "vendor/autoload.php";

use Pt\Pt;

Pt::module("Middleware", [])
->component("something", function($input) {
    $input["lol"] = 8;
    return $input;
})

->component("else", function() {
    echo "hey\n";
});


Pt::module("Test", ['config'], function($config) {
    $config->config('Test::test', [
        "hole" => 10
    ]);
})

->component("test",
            ['*Middleware::something',
             '*config::config'],
             function($input) {
    return $input;
});

Pt::module('config', []);

echo Pt::run([
    '$path' => "Test::test",
    "lol" => 5
]), PHP_EOL;
