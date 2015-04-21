<?php
namespace Pt;

use \Exception;

class Module {
    private $name;
    private $components;
    private $middleware;
    private $endware;

    public function __construct($name, $middleware, $endware) {
        $this->name = $name;
        $this->components = [];
        $this->middleware = $middleware;
        $this->endware = $endware;
    }

    public function __toString() {
        return "Module $this->name";
    }

    public function __get($name) {
        if (array_key_exists($name, $this->components)) {
            return $this->components[$name];
        }

        throw new Exception("Cannot find Component $name in Module $this->name");
    }

    public function __call($name, $arguments) {
        if (array_key_exists($name, $this->components)) {
            return call_user_func_array($this->components[$name]->func, $arguments);
        }

        throw new Exception("Cannot find Component $name in Module $this->name");
    }

    public function component($name, $deps=null, $func=null) {
        if (array_key_exists($name, $this->components) && $deps === null) {
            return $this->components[$name];
        }

        if ($deps === null) {
            throw new Exception("Illegal declaration of Component!");
        }

        if ($func === null) {
            $func = $deps;
            $deps = [];
        }

        if ($name !== '__init__') {
            $deps = array_merge($this->middleware, $deps, $this->endware);
        }

        $c = new Component($this->name, $name, $deps, $func);
        $this->components[$name] = $c;

        return $this;
    }

    public function printComponents() {
        echo '| ', $this, PHP_EOL;
        foreach ($this->components as $c) {
            echo '|---> ', $c, PHP_EOL;
        }
    }
}
