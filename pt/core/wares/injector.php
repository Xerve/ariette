<?php

namespace Pt\Core\Wares;

class Injector extends \Pt\PtWare {
    static $NAME = "Pt::Injector";
    
    private $vars = [];
    
    public function __construct($pt, $options=[]) {
        $this->vars = $options;
    }
    
    public function handler($input) {
        return array_merge($input, $this->vars);
    }
    
    public function addRule($key, $value) {
        $this->vars[$key] = $value;
    }
}