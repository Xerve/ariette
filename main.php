<?php

require_once "vendor/autoload.php";

use Pt\Pt;

Pt::module("Test", [
                    'Pt::redirect',
                    '*Pt::redirect'], function($redirect) {
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
    '$path' => "Test::test",
    "lol" => 5
], 'NOCATCH'), PHP_EOL;

Pt::printNS();
