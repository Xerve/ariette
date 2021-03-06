<?php
namespace Ariette;

class Module {
    private $name;
    private $components;
    private $middleware;
    private $endware;
    private $lock;

    public $init;

    public function __construct($name, $middleware, $endware) {
        $this->name = $name;
        $this->components = [];
        $this->middleware = $middleware;
        $this->endware = $endware;

        $this->init = false;
        $this->lock = false;
    }

    public function __toString() {
        return "Module $this->name";
    }

    public function __get($name) {
        if (array_key_exists($name, $this->components)) {
            return $this->components[$name];
        } else if ($name === '__init__') {
            $this->component('__init__');
            return $this->components['__init__'];
        }

        throw new ArietteException("Cannot find Component $name in Module $this->name");
    }

    public function __call($name, $arguments) {
        if ($name == '__init__') {
            $this->init();
        } else if (array_key_exists($name, $this->components)) {
            return call_user_func_array($this->components[$name]->func, $arguments);
        } else {
            throw new ArietteException("Cannot find Component $name in Module $this->name");
        }
    }

    public function __invoke() {
        $this->init();
    }

    public function lock() {
        $this->lock = true;
    }

    public function init() {
        $this->init = true;
        Ariette::handle($this->component('__init__'), [], 'NOOP');
    }

    public function component($name, $deps=null, $func=null) {
        // Getting __init__, if it exists or not
        if ($name === '__init__' && $deps === null && $func === null) {
            if (array_key_exists('__init__', $this->components)) {
                return $this->components['__init__'];
            } else {
                $this->component('__init__', [], function($input) { return $input; });
                return $this->components['__init__'];
            }
        }

        // Component Exists
        if (array_key_exists($name, $this->components) && $deps === null) {
            $c = $this->components[$name];

            if (is_string($c)) {
                $lock = $this->lock;
                $this->lock = false;
                unset($this->components[$name]);
                $__require = function() use ($c) {
                    require_once $c;
                };

                $__require();
                $this->lock = $lock;

                return $this->components[$name];
            } else {
                return $c;
            }
        }

        // Trying to redefine a component
        else if (array_key_exists($name, $this->components)) {
            throw new ArietteException("Cannot redefine Component $name on Module $this->name");
        }

        // Defining laxyily loaded component
        else if (is_string($deps) && $func === null) {
            if ($this->lock && $name !== '__init__') {
                throw new ArietteException("Cannot declare Component $name on locked Module $this->name");
            }

            $this->components[$name] = $deps;

            return $this;
        }

        // Module isn't loaded
        if ($deps === null) {
            throw new ArietteException("Component $this->name::$name is not loaded!");
        }

        if ($func === null) {
            $func = $deps;
            $deps = [];
        }

        // Don't use middleware or endware for the __init__ fucntion
        if ($name !== '__init__') {
            $deps = array_merge($this->middleware, $deps, $this->endware);
        }

        if ($this->lock && $name !== '__init__') {
            throw new ArietteException("Cannot declare Component $name on locked Module $this->name");
        }

        $c = new Component($this->name, $name, $deps, $func);
        $this->components[$name] = $c;

        return $this;
    }

    public function componentExists($name) {
        return array_key_exists($name, $this->components);
    }

    public function printComponents() {
        if ($this->init === false) {
            $self = "0.Module $this->name";
        } else {
            $self = "Module $this->name";
        }

        echo '| ', $self, PHP_EOL;
        foreach ($this->components as $name => $c) {
            if (is_string($c)) {
                echo "|---> 0.Component $this->name::$name\n";
            } else {
                echo '|---> ', $c->printDependencies(), PHP_EOL;
            }
        }
    }
}
