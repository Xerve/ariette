<?php

namespace Pt\Core\Wares;

use \Exception;
use \ORM;

class AuthMiddleware extends \Pt\PtWare {
    public static $NAME = "Pt::AuthMiddleWare";
    public static $DEPENDENCIES = ["Pt::DB"];

    private $rules;

    public function __construct($rules=[]) {
        $this->rules = [];
        
        foreach ($rules as $path => $permission) {
            $this->rules[$path] = $permission;
        }
    }
    
    public function handler($input) {
        if (!array_key_exists($input["path"], $this->rules)) {
            return $input;
        }
        
        if (!array_key_exists("s_token", $input)) {
            throw new Exception("No security token provided!");
        }
        
        $user = ORM::for_table('auth_users')
            ->where('s_token', $input["s_token"])
            ->find_one();
            
        if (!$user) {
            throw new Exception("user not found!");
        }
            
        $permissions = explode("|", $user->permissions);
        $authorized = in_array($this->rules[$input["path"]], $permissions) ||
                      in_array("ROOT", $permissions);
                                    
        if (!$authorized) {
            throw new Exception("User is not authorized!");
        }
        
        return $input;
    }
    
    public function addRule($path, $permission) {
        $this->rules[$path] = $permission;
        return $this;
    }
}