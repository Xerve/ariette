<?php
namespace Pt;

use \Exception;

class Pt {
    private static $modules = [];

    public static function __callStatic($name, $arguments) {
        if (is_array($arguments)) {
            if (count($arguments === 1)) {
                return self::getComponent($name, $arguments[0]);
            }
        }

        throw new Exception("Improper use of static on Pt!");
    }

    public static function module($name, $deps=null, $callback=null) {
        // Creating a new module
        if (!array_key_exists($name, self::$modules) && $deps !== null && !is_string($deps)) {
            $middleware = [];
            $endware = [];

            foreach ($deps as $dep) {
                if (substr($dep, 0, 1) === '*') {
                    $middleware[] = $dep;
                } else if (substr($dep, -1, 1) === '*') {
                    $endware[] = $dep;
                } else {
                    self::getComponent($dep);
                }
            }

            $m = new Module($name, $middleware, $endware);
            self::$modules[$name] = $m;

            if ($callback !== null) {
                $m->component('__init__', $deps, $callback);
            }

            return $m;
        }

        // Creating a lazily loaded module

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

        if (self::$modules[$mod]->init === false) {
            self::$modules[$mod]->__init__();
        }

        if ($com === null) {
            return self::$modules[$mod];
        } else {
            return self::$modules[$mod]->component($com);
        }
    }

    public static function run($input=null, $silent=null) {
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
                '@status' => 404,
                '@log' => $e->getMessage()
            ]);
        }

        $output = self::handle($component, $input, $silent);

        foreach ($output as $key => $attr) {
            if (substr($key, 0, 1) === '$') {
                unset($output[$key]);
            }
        }

        return json_encode($output);
    }

    public static function handle(Component $component, $input=null, $silent=false) {
        $component_deps = [];
        $middleware = [];
        $endware = [];

        if ($input === null) {
            $input = [];
        }

        if (!array_key_exists('$path', $input)) {
            $input['$path'] = $component->name;
        }

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

            if ($silent === 'EXPLICIT') {
                echo $ware, "\n============\n", json_encode($input), PHP_EOL, PHP_EOL;
            }

            if (array_key_exists('$short', $input)) {
                if ($input['$short']) {
                    return $input;
                }
            }
        }

        if ($silent === 'NOOP') {
            $component($input, $component_deps);
            $output = $input;
        } else {
            $output = $component($input, $component_deps);
        }

        if ($silent === 'EXPLICIT') {
            echo $component, "\n============\n", json_encode($output), PHP_EOL, PHP_EOL;
        }

        foreach ($endware as $ware) {
            $output = self::handle($ware, $output);

            if ($silent === 'EXPLICIT') {
                echo $ware, "\n============\n", json_encode($output), PHP_EOL, PHP_EOL;
            }

            if (array_key_exists('$short', $output)) {
                if ($output['$short']) {
                    return $output;
                }
            }
        }

        if (!array_key_exists('@status', $output)) {
            $output['@status'] = 200;
        }

        return $output;
    }

    public static function printNS() {
        foreach (self::$modules as $m) {
            $m->printComponents();
        }
    }
}

Pt::module('Pt', []);
