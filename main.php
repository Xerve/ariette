<?php

require_once "vendor/autoload.php";

use Pt\Pt;

Pt::module("Test", ['*Pt::injector', 'Pt::injector'], function($injector) {
    $injector('wow', 'ayy lmao');
})

->component('lol', ['*Test::me'], function($input) {
    return $input;
})

->component('me', function($input) {
    $input['wow'] = $input['$wow'];
    return $input;
})

->component("test", ['*Test::lol'], function($input) {
    return $input;
});

Pt::run([
    '$path' => "Test::lol",
    "lol" => 5
]);

Pt::printNS();
