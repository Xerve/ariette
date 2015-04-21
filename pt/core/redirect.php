<?php
namespace Pt;

use \Exception;

$rules = [];

Pt::module('Pt')
->component('redirect', function($component, $settings) use (&$rules) {
    // Adding rules
    if (is_string($component) && is_string($settings)) {
        $rules[$component] = $settings;
    }

    // As middleware
    else {
        if (array_key_exists($component['$path'], $rules)) {
            $component['$path'] = $rules[$component['$path']];
        }

        return $component;
    }
});
