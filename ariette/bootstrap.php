<?php
namespace Ariette;

if (!defined('ARIETTE_REQUIRE_CORE')) {
    define('ARIETTE_REQUIRE_CORE', true);
}

if (!defined('ARIETTE_REQUIRE_IT')) {
    define('ARIETTE_REQUIRE_IT', false);
}

if (!defined('ARIETTE_REQUIRE_NONE')) {
    define('ARIETTE_REQUIRE_NONE', false);
}

if (!constant('ARIETTE_REQUIRE_NONE')) {
    require __DIR__."/ArietteException.php";
    require __DIR__."/Component.php";
    require __DIR__."/Module.php";
    require __DIR__."/Ariette.php";

    if (constant('ARIETTE_REQUIRE_CORE')) {
        Ariette::module('Ariette', __DIR__.'/../core/Ariette.php');
    }

    if (constant('ARIETTE_REQUIRE_IT')) {
        require __DIR__.'/../it/bootstrap.php';
    }
}
