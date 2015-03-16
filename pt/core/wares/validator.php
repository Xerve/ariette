<?php

namespace Pt\Core\Wares;

use \Exception;

class Validator extends \Pt\PtWare {
    public static $NAME = "Pt::Validator";
    
    private $rules;
    
    public function __construct() {
        $this->rules = [];
    }
    
    public function handler($input) {
        if (!array_key_exists($input["path"], $this->rules)) {
            return $input;
        }
        
        foreach($this->rules[$input["path"]] as $required => $val) {
            if (!array_key_exists($required, $input)) {
                throw new Exception("Schema value $required not found in input!");
            }
            
            if (is_callable($val)) {
                $val($input[$required]);
            }
        }
        
        return $input;
    }
    
    public function addRule($route, $schema) {
        $this->rules[$route] = $schema;
        return $this;
    }
}