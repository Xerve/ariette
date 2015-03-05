<?php

namespace Pt\Core\Apps;

use \Exception;
use \ORM;

class Auth extends \Pt\PtApp {
    public static $NAME = "Pt::Auth";
    public static $DEPENDENCIES = ["Pt::Validator", "Pt::DB"];
    public static $METHODS = ["register"];
    
    public function init($apps, $wares) {
        $apps["Pt::DB"]->schema("pt::auth", [
            "id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY",
            "username VARCHAR(15) NOT NULL",
            "name VARCHAR(30)",
            "permissions VARCHAR(1000) NOT NULL"
        ]);
        
        $apps["Pt::Validator"]->addRule("Pt::Auth.register", [
            "username" =>  function($username) {
                if (!is_string($username)) { return false; }
                if (strlen($username) > 15) { return false; }
                return true;
            },
            "name" => function($name) {
                if (!is_string($name)) { return false; }
                if (strlen($name) > 30) { return false; }
                return true;
            }
        ]);
    }
    
    public function handler($input) {
        return [];
    }
    
    public function register($input) {
        $user = ORM::for_table('auth', 'pt')
            ->create();
            
        $user->username = $input["username"];
        $user->name = $input["name"];
        $user->permissions = "";
        $user.save();
        
        return [
            "success" => true    
        ];
    }
}