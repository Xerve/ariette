<?php

require_once "vendor/autoload.php";

use Pt\Pt;

Pt::module("Test", ['Pt::config',
                    'Pt::redirect',
                    '*Pt::redirect'], function($config, $redirect) {
    $config('Test::me', [
        "hole" => 10
    ]);

    $redirect('Test::me', 'Test::test');
})

->component('me', ['*Pt::config'], function($input) {
    $input['lol'] = 9;
    return $input;
})

->component("test", function($input) {
    return $input;
});

Pt::printNS();

echo Pt::Pt([
    '$path' => "Test::me",
    "lol" => 5
]), PHP_EOL;
