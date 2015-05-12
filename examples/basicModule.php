<?php

require __DIR__.'/../vendor/autoload.php';

use Ariette\Ariette;

Ariette::module('MyModule', [])

->component('MyComponent', function($input) {
    $input['ayy'] = 'lmao';
    return $input;
})

->component('TestComponent', function($input) {
    return [
        'This' => 'isnt the input!'
    ];
});

echo Ariette::run([
    '$path' => 'MyModule::MyComponent'
]), PHP_EOL;
