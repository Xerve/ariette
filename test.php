<?php

define('PT_REQUIRE_IT', true);

require_once "vendor/autoload.php";

use Pt\Pt;
use It\It;

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

It::is(Pt::Test('test'), function() {
    return It::should('not be a poop', function() {
        99;
    })

    ->itShould('be a poop', function() {
        throw new Exception("im a poop");
    });
});

It::is(Pt::Test('lol'), function() {
    return It::should('be cool');
});

It::is(Pt::Test('me'));

It::lives();
