<?php

require_once "vendor/autoload.php";

use Pt\Pt;

Pt::module("Test", ['Pt::config',
                    'Pt::redirect',
                    '*Pt::redirect'], function($config, $redirect) {
    $config('Test::me', [
        "hole" => 10
    ]);
})

->component('me', ['*Pt::config'], function($input) {
    $input['lol'] = $input['$config'];
    return $input;
})

->component("test", function($input) {
    return $input;
});

Pt::printNS();

echo Pt::run([
    '$path' => "Test::me",
    "lol" => 5
]), PHP_EOL;
