<?php
namespace Ariette;

Ariette::module('Ariette', [])
->component('config', __DIR__.'/config.php')
->component('redirect', __DIR__.'/redirect.php')
->component('injector', __DIR__.'/injector.php')
->lock();
