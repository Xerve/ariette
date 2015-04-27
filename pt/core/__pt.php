<?php
namespace Pt;

Pt::module('Pt', []);

// require_once __DIR__.'/config.php';
// require_once __DIR__.'/redirect.php';

Pt::module('Pt')
->component('config', __DIR__.'/config.php')
->component('redirect', __DIR__.'/redirect.php');
