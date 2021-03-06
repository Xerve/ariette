<?php
namespace Ariette;

class Ariette {
    private static $modules = [];

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
        else if (!array_key_exists($name, self::$modules) && is_string($deps) && $callback === null) {
            self::$modules[$name] = $deps;
        }

        // Retrieving an existing module
        else if (array_key_exists($name, self::$modules)) {
            if ($deps === null) {
                $m = self::$modules[$name];

                if (is_string($m)) {
                    unset(self::$modules[$name]);
                    $__require = function() use ($m) {
                        require_once $m;
                    };

                    $__require();

                    return self::$modules[$name];
                } else {
                    return $m;
                }
            } else {
                $m = self::$modules[$name];

                // Module is already loaded
                if (!is_string($m)) {
                    throw new ArietteException("Cannot redeclare Module $name!");
                } else {
                    throw new ArietteException("Cannot declare Module $mod as file!");
                }
            }
        }

        // Default
        else {
            throw new ArietteException("Module $name is not loaded!");
        }
    }

    public function getComponent($mod, $com=null) {
        if ($com === null) {
            $i = explode('::', $mod);
            if (count($i) > 2) {
                throw new ArietteException("Invalid component string $mod!");
            }

            $mod = $i[0];

            if (count($i) > 1) {
                $com = $i[1];
            }
        }

        if (!array_key_exists($mod, self::$modules)) {
            throw new ArietteException("No module $mod has been loaded!");
        }

        if (!is_string(self::$modules[$mod]) && self::$modules[$mod]->init === false) {
            self::$modules[$mod]->__init__();
        }

        if ($com === null) {
            return self::module($mod);
        } else {
            return self::module($mod)->component($com);
        }
    }

    public static function run($input=null, $silent=null) {
        if ($input === null) {
            header("content-type: application/json");
            $input = json_decode(file_get_contents("php://input"), true);
        }

        if (!array_key_exists('$path', $input)) {
            throw new ArietteException('No $path provided!');
        }

        $i = explode('::', $input['$path']);
        if (count($i) != 2) {
            throw new ArietteException("Invalid path string $path!");
        }

        if ($silent !== 'NOCATCH') {
            try {
                $component = self::getComponent($i[0], $i[1]);
            } catch (ArietteException $e) {
                return json_encode([
                    '@status' => 404,
                    '@log' => $e->getMessage()
                ]);
            }
        } else {
            $component = self::getComponent($i[0], $i[1]);
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

            if ($silent === 'SCREAM') {
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

        if ($silent === 'SCREAM') {
            echo $component, "\n============\n", json_encode($output), PHP_EOL, PHP_EOL;
        }

        foreach ($endware as $ware) {
            $output = self::handle($ware, $output);

            if ($silent === 'SCREAM') {
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
        foreach (self::$modules as $name => $m) {
            if (is_string($m)) {
                echo "| 0.Module $name\n";
            } else {
                $m->printComponents();
            }
        }
    }

    public static function __callStatic($name, $args) {
        if ($args) {
            return self::module($name)->component($args[0]);
        } else {
            return self::module($name);
        }
    }
}
