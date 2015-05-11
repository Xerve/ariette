<?php

require_once "vendor/autoload.php";
require __DIR__.'/it/bootstrap.php';

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
//
// echo Pt::run([
//     '$path' => "Test::lol",
//     "lol" => 5
// ]), PHP_EOL;

It::is(Pt::Test('test'), function() {
    return It::should('lololol',
        'RAWR'
    )

    ->itShould('rawfl',
        'POPOP'
    );
});

It::is(Pt::Test('lol'), function() {
    return It::should('be cool', 9);
});

It::is(Pt::Test('me'));

It::lives();
