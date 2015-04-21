<?php
namespace Pt;

class Component {
    public $name;
    public $deps;
    public $func;

    public function __construct($parent, $name, $deps, $func) {
        $this->name = "$parent::$name";
        $this->deps = $deps;
        $this->func = $func;
    }

    public function __toString() {
        $buf = "Component $this->name [";
        foreach ($this->deps as $dep) {
            $buf = $buf . "$dep ";
        }
        $buf = trim($buf, ' ') . ']';

        return $buf;
    }

    public function __invoke($input, $deps=null) {
        if ($deps === null) {
            return Pt::handle($this, $input);
        }

        $deps[] = $input;
        $deps[] = $this;

        return call_user_func_array($this->func, $deps);
    }
}
