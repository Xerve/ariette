<?php

use Ariette\Ariette;
use It\It;

It::is(Ariette::Ariette('config'), function() {
    return It::should('accept configurations', function($config) {
        $config = $config->func;
        $config('MyModule::test', [
            'RAWR' => 9
        ]);

        It::expects($config('MyModule::test'))->to->not->be->empty();
        It::expects($config('MyModule::test'))->to->have('RAWR');
        It::expects($config('MyModule::test')['RAWR'])->to->equal(9);
    })

    ->itShould('return configurations', function($config) {
        $config = $config->func;
        $config = $config('MyModule::test');

        It::expects($config)->to->not->be->empty();
        It::expects($config)->to->have('RAWR');
        It::expects($config['RAWR'])->to->equal(9);
    })

    ->itShould('return nothing for bad paths', function($config) {
        $config = $config->func;
        $config = $config('MyModule::notest');

        It::expects($config)->to->be->empty();
    })

    ->itShould('act as middleware', function($config) {
        $config = $config([
            '$path' => 'MyModule::test'
        ]);

        It::expects($config)->to->have('$config');

        $config = $config['$config'];

        It::expects($config)->to->not->be->empty();
        It::expects($config)->to->have('RAWR');
        It::expects($config['RAWR'])->to->equal(9);
    });
});
