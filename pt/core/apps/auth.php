<?php

namespace Pt\Core\Apps;

use \Exception;
use \ORM;

class Auth extends \Pt\PtApp {
    public static $NAME = "Pt::Auth";
    public static $DEPENDENCIES = ["Pt::Validator", "Pt::DB"];
    public static $METHODS = [
        "register", 
        "login", 
        "addPermissions", 
        "getPermissions"
    ];
    
    public function init($apps, $wares) {
        $apps["Pt::DB"]->schema("auth_users", "username", [
            "username VARCHAR(15) NOT NULL",
            "password VARCHAR(255) NOT NULL",
            "name VARCHAR(30)",
            "permissions VARCHAR(1000) NOT NULL",
            "s_token VARCHAR(32)",
            "PRIMARY KEY (username)"
        ]);
        
        $wares["Pt::Validator"]->addRule("Pt::Auth.register", [
            "username" =>  function($username) {
                if (!is_string($username)) { 
                    throw new Exception("Username is not a string!");
                } else if (strlen($username) > 15) { 
                    throw new Exception("Username is too long!");
                } else if (ORM::for_table('auth_users')
                    ->where('username', $username)
                    ->find_one()) {
                        throw new Exception("Username already exists!");        
                }
            },
            "name" => function($name) {
                if (!is_string($name)) { 
                    throw new Exeption("Name is not a string!"); 
                } else if (strlen($name) > 30) {
                    throw new Exception("Name is too long!");
                }
            },
            "password" => function($password) {
                if (!is_string($password)) { 
                    throw new Expcetion("Password is not a string!");   
                } else if (strlen($password) < 8) {
                    throw new Excpetion("Password is too short!");
                } else if (strlen($password) > 20) {
                    throw new Exception("Password is too long!");
                }
            }
        ]);
        
        $wares["Pt::Validator"]->addRule("Pt::Auth.login", [
            "username" => function($username) {
                if (!is_string($username)) { 
                    throw new Exception("Username is not string!");
                }
            },
            "password" => function($password) {
                if (!is_string($password)) {
                    throw new Exception("Password is not a string!");                    
                }
            }
        ]);
        
        $wares["Pt::Validator"]->addRule("Pt::Auth.addPermissions", [
            "token" => function($token) {
                if (!is_string($token)) {
                    throw new Exception("Token is not string!");
                }
            },
            "username" => function($user) {
                if (!is_string($token)) {
                    throw new Exception("User is not a string!");
                }  
            },
            "permissions" => function($permissions) {
                if (!is_array($permissions)) {
                    throw new Excpetion("Permissions is not array");
                }
            }
        ]);
        
        $wares["Pt::Validator"]->addRule("Pt::Auth.getPermissions", [
            "username" => function($user) {
                if (!is_string($user)) {
                    throw new Exception("User is not a string!");
                }
            }
        ]);
    }
    
    public function handler($input) {
        return [];
    }
    
    public function login($input) {
        $user = ORM::for_table('auth_users')
            ->where('username', $input["username"])
            ->find_one();
            
        if (!password_verify($input["password"], $user->password)) {
            return [
                "success" => false
            ];            
        }
        
        $token = bin2hex(openssl_random_pseudo_bytes(16));
        $user->s_token = token;
        $user->save();
        
        return [
            "success" => true,
            "token" => $token
        ];
    }
    
    public function register($input) {
        $user = ORM::for_table('auth_users')
            ->create();
            
        $user->username = $input["username"];
        $user->password = password_hash($input["password"], \PASSWORD_DEFAULT);
        $user->name = $input["name"];
        $user->permissions = "";
        $user->save();
        
        return [
            "success" => true    
        ];
    }
     
    public function addPermissions($input) {
        $auth_user = ORM::for_table('auth_users')
            ->where('token', $input["token"])
            ->find_one();
            
        if (!in_array("ADMIN", explode("|", $auth_user->permissions))) {
            throw new Exception("Current user cannot add permissions!");
        }
        
        $user = ORM::for_table('auth_users')
            ->where('username', $input["username"])
            ->find_one();
        
        $user_permissions = explode("|", $user->permissions);
        array_push($user_permissions, $input["permissions"]);
        $user->permissions = implode("|", $user_permissions);
        $user->save();
        
        return [
            "success" => true  
        ];
    }
    
    public function getPermissions($input) {
        $user = ORM::for_table('auth_users')
            ->where('username', $input["username"])
            ->find_one();
            
        return [
            "permissions" => explode("|", $user->permissions)  
        ];
    }
}
