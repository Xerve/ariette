<?php

require_once "vendor/autoload.php";

use Pt\Pt;

Pt::module("Test", ['*Pt::injector', 'Pt::injector'], function($injector) {
    $injector('wow', 0);
})

->component('me', function($input) {
    print_r($input);
    $input['lol'] = $input['$wow'];
    return $input;
})

->component("test", function($input) {
    return $input;
});

echo Pt::run([
    '$path' => "Test::me",
    "lol" => 5
]), PHP_EOL;
