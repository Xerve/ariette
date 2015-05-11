<?php
namespace Pt;

Pt::module('Pt')
->component('redirect', function($component, $settings) {
    static $rules = [];

    // Adding rules
    if (is_string($component) && is_string($settings)) {
        $rules[$component] = $settings;
    }

    // As middleware
    else {
        if (array_key_exists($component['$path'], $rules)) {
            $component['$path'] = $rules[$component['$path']];
            $c = json_decode(Pt::run($component), true);
            $c['$short'] = true;

            return $c;
        } else {
            return $component;
        }
    }
});
