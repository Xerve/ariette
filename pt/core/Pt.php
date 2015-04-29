<?php
namespace Pt;

Pt::module('Pt', [])
->component('config', __DIR__.'/config.php')
->component('redirect', __DIR__.'/redirect.php')
->component('injector', __DIR__.'/injector.php');
