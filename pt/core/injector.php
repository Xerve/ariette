<?php
namespace Pt;

Pt::module('Pt')
->component('injector', function($input, $inject=null) {
    static $inject_settings = [];

    // Middleware mode
    if (is_array($input)) {
        return array_merge($input, $inject_settings);
    }

    // Giving new settings with $inject(key, val);
    else if (is_string($input)) {
        $inject_settings["$$input"] = $inject;
    }

    return $inject_settings;
});
