<?php
namespace Pt;

Pt::module('Pt')
->component('injector', function($input, $inject=null) {
    static $inject_settings = [];

    // Middleware mode
    if (is_array($input)) {
        ECHO "INJECTING...\n";
        foreach ($inject_settings as $name => $key) {
            ECHO "INJECTING $name => $key\n";
            $input["$$name"] = $key;
        }

        return $input;
    }

    else if (is_string($input)) {
        $inject_settings[$input] = $inject;
    }

    return $inject_settings;
});
