<?php
namespace Ariette;

if (!defined('ARIETTE_REQUIRE_CORE')) {
    switch (ini_get('ARIETTE_REQUIRE_CORE')) {
        case false:
        case 'require':
            define('ARIETTE_REQUIRE_CORE', true);
            break;
        default:
            define('ARIETTE_REQUIRE_CORE', false);
    }
}

if (!defined('ARIETTE_REQUIRE_IT')) {
    switch (ini_get('ARIETTE_REQUIRE_IT')) {
        case false:
        case 'require':
            define('ARIETTE_REQUIRE_IT', true);
            break;
        default:
            define('ARIETTE_REQUIRE_IT', false);
    }
}

if (!defined('ARIETTE_REQUIRE_NONE')) {
    switch (ini_get('ARIETTE_REQUIRE_NONE')) {
        case false:
            define('ARIETTE_REQUIRE_NONE', false);
            break;
        default:
            define('ARIETTE_REQUIRE_NONE', true);
    }
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
