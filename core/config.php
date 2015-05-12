<?php
namespace Ariette;

Ariette::module('Ariette')
->component('config', function($component=null, $settings=null) {
    static $config_settings = [];

    // If not being used as middleware
    if (is_string($component) && $settings !== null) {
        if (!array_key_exists($component, $config_settings)) {
            $config_settings[$component] = [];
        }

        $config_settings[$component] = array_merge($config_settings[$component], $settings);
    }

    // Fetching settings
    else if ($settings === null && is_string($component)) {
        if (array_key_exists($component, $config_settings)) {
            return $config_settings[$component];
        } else {
            return [];
        }
    }

    // Applying settings
    else if ($component !== null && isset($settings)) {
        if (array_key_exists('$path', $component)) {
            if (array_key_exists($component['$path'], $config_settings)) {
                $component['$config'] = $config_settings[$component['$path']];
            } else {
                $component['$config'] = [];
            }
        }

        return $component;
    }

    // Default
    return $config_settings;
});
