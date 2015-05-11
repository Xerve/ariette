<?php
namespace Pt;

if (!defined('PT_REQUIRE_STDLIB')) {
    define('PT_REQUIRE_STDLIB', true);
}

if (!defined('PT_REQUIRE_IT')) {
    define('PT_REQUIRE_IT', false);
}

require __DIR__."/PtException.php";
require __DIR__."/Component.php";
require __DIR__."/Module.php";
require __DIR__."/Pt.php";

if (constant('PT_REQUIRE_STDLIB')) {
    Pt::module('Pt', __DIR__.'/core/Pt.php');
}

if (constant('PT_REQUIRE_IT')) {
    require __DIR__.'/../it/bootstrap.php';
}
