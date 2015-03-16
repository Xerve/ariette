<?php

namespace Pt;

use \Exception;

abstract class PtWare {
    public static $NAME;
    public static $DEPENDENCIES = [];
    
    abstract public function handler($input);
    public function init($apps, $wares) {}
}

abstract class PtApp {
    public static $NAME;
    public static $DEPENDENCIES = [];
    public static $METHODS = [];
    
    abstract public function handler($input);
    public function init($apps, $wares) {}
}

class Pt {
    public $apps = [];
    public $wares = [];
    
    public function route($route, $func) {
        $this->apps[$route] = $func;
    }
    
    public function apply($name, $func) {
        $this->wares[$name] = $func;
    }
    
    public function register(PtApp $app) {
        if (!strpos($app::$NAME, "::")) {
            throw new Exception("Illegal name for app: ".$app::$NAME);    
        }
        
        foreach($app::$DEPENDENCIES as $dep) {
            if (!array_key_exists($dep, $this->apps) && !array_key_exists($dep, $this->wares)) {
                throw new Exception("Dependency $dep not met for ".$app::$NAME);
            }
        }

        $app->init($this->apps, $this->wares);        
        $this->apps[$app::$NAME] = $app;
    }
    
    public function middleware(PtWare $ware) {
        if (!strpos($ware::$NAME, "::")) {
            throw new Exception("Illegal name for middleware: ".$ware::$NAME);    
        }
        
        foreach($ware::$DEPENDENCIES as $dep) {
            if (!array_key_exists($dep, $this->apps) && !array_key_exists($dep, $this->wares)) {
                throw new Exception("Dependency $dep not met for ".$ware::$NAME);
            }
        }
        
        $ware->init($this->apps, $this->wares);
        $this->wares[$ware::$NAME] = $ware;
    }
    
    public function handler($options=[]) {
        header("content-type: application/json");
        $input = json_decode(file_get_contents("php://input"), true);
        
        return json_encode($this->run($input));
    }
    
    public function run($input=[]) {
        if (!$input || !array_key_exists("path", $input)) {
            return [
                "error" => 404,
                "message" => "Path not specified!" 
            ];
        }
        
        foreach($this->wares as $n => $w) {
            try {
                if (is_callable($w)) {
                    $input = $w($input);
                } else {
                    $input = $w->handler($input);
                }
            } catch (Exception $e) {
                return [
                   "error" => 500,
                   "log" => $e->getMessage()
                ];
            }
        }
        
        $arr = explode(".", $input["path"]);
        $path = $arr[0];
        $sub = (count($arr) > 1) ? $arr[1] : 'handler';
        
        if (array_key_exists($path, $this->apps)) {
            $app = $this->apps[$path];
            if (is_callable($app)) {
                $res = $app($input);
            } else {
                if (!in_array($sub, $app::$METHODS) && $sub !== 'handler') {  
                    $res = ["error" => 404];
                } else {
                    try {
                        $res = $app->$sub($input);
                    } catch (Exception $e) {
                        $res = [
                           "error" => 500,
                           "log" => $e->getMessage()
                        ];
                    }                
                }    
            }
        } else {
            $res = ["error" => 404];
        }   
        
        return $res;
    }
}