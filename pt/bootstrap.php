<?php
namespace Pt;

if (!defined('PT_REQUIRE_CORE')) {
    define('PT_REQUIRE_CORE', true);
}

if (!defined('PT_REQUIRE_IT')) {
    define('PT_REQUIRE_IT', false);
}

if (!defined('PT_REQUIRE_NONE')) {
    define('PT_REQUIRE_NONE', false);
}

if (!constant('PT_REQUIRE_NONE')) {
    require __DIR__."/PtException.php";
    require __DIR__."/Component.php";
    require __DIR__."/Module.php";
    require __DIR__."/Pt.php";

    if (constant('PT_REQUIRE_CORE')) {
        Pt::module('Pt', __DIR__.'/../core/Pt.php');
    }

    if (constant('PT_REQUIRE_IT')) {
        require __DIR__.'/../it/bootstrap.php';
    }
}
