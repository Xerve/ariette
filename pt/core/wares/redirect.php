<?php

namespace Pt\Core\Wares;

class Redirect extends \Pt\PtWare {
    public static $NAME = "Pt::Redirect";
    
    private $routes;
    
    public function __construct($options=[]) {
        $this->routes = $options;
    }
    
    public function handler($input) {
        if (array_key_exists($input["path"], $this->routes)) {
            $input["path"] = $this->routes[$input["path"]];
        }
        
        return $input;
    }
    
    public function addRule($source, $destination) {
        $this->routes[$source] = $destination;   
        return $this;
    }
}