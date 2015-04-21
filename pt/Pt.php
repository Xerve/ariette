<?php
namespace Pt;

use \Exception;

class Pt {
    private static $modules = [];

    public static function module($name, $deps=null, $callback=null) {
        // Creating a new module
        if (!array_key_exists($name, self::$modules) && $deps !== null) {
            $middleware = [];
            $endware = [];

            foreach ($deps as $dep) {
                if (substr($dep, 0, 1) === '*') {
                    $middleware[] = $dep;
                } else if (substr($dep, -1, 1) === '*') {
                    $endware[] = $dep;
                } else if (!array_key_exists($dep, self::$modules)) {
                    throw new Exception("Module $dep required by $name is not loaded!");
                }
            }

            $m = new Module($name, $middleware, $endware);
            self::$modules[$name] = $m;

            if ($callback !== null) {
                $m->component('__init__', $deps, $callback);
                self::handle($m->__init__, [
                    '$path' => "$name::__init__"
                ]);
            }

            return $m;
        }

        // Retrieving an existing module
        else if (array_key_exists($name, self::$modules)) {
            if ($deps === null) {
                return self::$modules[$name];
            } else {
                throw new Exception("Cannot redeclare Module $name!");
            }
        }

        // Default
        else {
            throw new Exception("Module $name is not loaded!");
        }
    }

    public function getComponent($mod, $com=null) {
        if ($com === null) {
            $i = explode('::', $mod);
            if (count($i) > 2) {
                throw new Exception("Invalid component string $mod!");
            }

            $mod = $i[0];

            if (count($i) > 1) {
                $com = $i[1];
            }
        }

        if (!array_key_exists($mod, self::$modules)) {
            throw new Exception("No module $mod has been loaded!");
        }

        if ($com === null) {
            return self::$modules[$mod];
        } else {
            return self::$modules[$mod]->component($com);
        }
    }

    public static function run($input=null) {
        if ($input === null) {
            header("content-type: application/json");
            $input = json_decode(file_get_contents("php://input"), true);
        }

        if (!array_key_exists('$path', $input)) {
            throw new Exception('No $path provided!');
        }

        $i = explode('::', $input['$path']);
        if (count($i) != 2) {
            throw new Exception("Invalid path string $path!");
        }

        try {
            $component = self::getComponent($i[0], $i[1]);
        } catch (Exception $e) {
            return json_encode([
                '$status' => 404
            ]);
        }

        return json_encode(self::handle($component, $input));
    }

    public static function handle(Component $component, $input) {
        $component_deps = [];
        $middleware = [];
        $endware = [];

        foreach ($component->deps as $dep) {
            if (substr($dep, 0, 1) === '*') {
                $middleware[] = self::getComponent(trim($dep, '*'));
            } else if (substr($dep, -1, 1) === '*') {
                $endware[] = self::getComponent(trim($dep, '*'));
            } else {
                $c = self::getComponent($dep);
                if (get_class($c) === "Pt\Module") {
                    $component_deps[] = $c;
                } else {
                    $component_deps[] = $c->func;
                }
            }
        }

        foreach ($middleware as $ware) {
            $input = self::handle($ware, $input);
        }

        $output = $component($input, $component_deps);

        foreach ($endware as $ware) {
            $output = self::handle($ware, $output);
        }

        if (!array_key_exists('$status', $output)) {
            $output['$status'] = 200;
        }

        return $output;
    }
}

Pt::module('Pt', []);
