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
        "addPermission", 
        "getPermissions",
        "hasPermission",
        "createRoot"
    ];
    
    private $secret;
    
    public function __construct($options=[]) {
        if (!array_key_exists("secret_token", $options)) {
            throw new Exception("No secret_token provided!");
        }
        
        $this->secret = $options["secret_token"];
    }
    
    public function init($apps, $wares) {
        $apps["Pt::DB"]->schema("auth_users", "username", [
            "username VARCHAR(15) NOT NULL",
            "password VARCHAR(255) NOT NULL",
            "name VARCHAR(30)",
            "permissions VARCHAR(1000) NOT NULL",
            "s_token VARCHAR(100)",
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
                } else if ($username === "root") {
                    throw new Exception("Invalid username");
                } else if (strpos($username, ",") || strpos($username, ".") ||
                           strpos($username, "<") || strpos($username, ">") ||
                           strpos($username, ";") || strpos($username, ":") ||
                           strpos($username, "!") || strpos($username, "?") ||
                           strpos($username, "\"") || strpos($username, "'") ||
                           strpos($username, "\\") || strpos($username, "/") ||
                           strpos($username, "@") || strpos($username, "#") ||
                           strpos($username, "$") || strpos($username, "%") ||
                           strpos($username, "^") || strpos($username, "&") ||
                           strpos($username, "*") || strpos($username, "|") ||
                           strpos($username, "=") || strpos($username, "+") ||
                           strpos($username, "`") || strpos($username, "~") ||
                           strpos($username, "(") || strpos($username, ")")) {
                    throw new Exception("Invalid character(s) in username!");
                }
            },
            "name" => function($name) {
                if (!is_string($name)) { 
                    throw new Exception("Name is not a string!"); 
                } else if (strlen($name) > 30) {
                    throw new Exception("Name is too long!");
                }
            },
            "password" => function($password) {
                if (!is_string($password)) { 
                    throw new Exception("Password is not a string!");   
                } else if (strlen($password) < 8) {
                    throw new Exception("Password is too short!");
                } else if (strlen($password) > 20) {
                    throw new Exception("Password is too long!");
                }
            }
        ])
        ->addRule("Pt::Auth.login", [
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
        ])
        ->addRule("Pt::Auth.addPermission", [
            "s_token" => function($token) {
                if (!is_string($token)) {
                    throw new Exception("Token is not string!");
                }
            },
            "username" => function($username) {
                if (!is_string($username)) {
                    throw new Exception("User is not a string!");
                }  
            },
            "permission" => function($permission) {
                if (!is_string($permission)) {
                    throw new Exception("Permission is not string");
                }
                
                if (strpos($permission, "|") || strpos($permission, " ")) {
                    throw new Exception("Invalid permission name!");
                }
            }
        ])
        ->addRule("Pt::Auth.getPermissions", [
            "username" => function($user) {
                if (!is_string($user)) {
                    throw new Exception("User is not a string!");
                }
            }
        ])
        ->addRule("Pt::Auth.hasPermission", [
            "s_token" => function($s_token) {
                if (!is_string($s_token)) {
                    throw new Exception("s_token is not a string!");
                }
            },
            "permission" => function($permission) {
                if (!is_string("permission")) {
                    throw new Exception("permission is not a string!");
                }
            }
        ])
        ->addRule("Pt::Auth.createRoot", [
            "secret_token" => function($secret) {
                if (!is_string($secret)) {
                    throw new Exception("secret_token is not a string!");
                } else if ($secret !== $this->secret) {
                    throw new Exception("Incorrect secret_token!");   
                }
            },
            "password" => function($password) {
                if (!is_string($password)) {
                    throw new Exception("password is not a string!");
                }
            }
        ]);
    }
    
    public function handler($input) {
        return $this->login($input);
    }
    
    public function login($input) {
        $user = ORM::for_table('auth_users')
            ->where('username', $input["username"])
            ->find_one();
            
        if (!$user) {
            return [
                "success" => false  
            ];
        }
            
        if (!password_verify($input["password"], $user->password)) {
            return [
                "success" => false
            ];            
        }
        
        $token = bin2hex(openssl_random_pseudo_bytes(16));
        $user->s_token = $token;
        $user->save();
        
        return [
            "success" => true,
            "s_token" => $token
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
     
    public function addPermission($input) {
        $auth_user = ORM::for_table('auth_users')
            ->where('s_token', $input["s_token"])
            ->find_one();
            
        if (!in_array("ROOT", explode("|", $auth_user->permissions))) {
            throw new Exception("Current user cannot add permissions!");
        }
        
        $user = ORM::for_table('auth_users')
            ->where('username', $input["username"])
            ->find_one();
            
        if (!$user) {
            throw new Exception("user not found!");
        }
        
        $user_permissions = explode("|", $user->permissions);
        array_push($user_permissions, $input["permission"]);
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
            
        if (!$user) {
            throw new Exception("user not found!");
        }
            
        return [
            "permissions" => explode("|", $user->permissions)  
        ];
    }
    
    public function hasPermission($input) {
        $user = ORM::for_table('auth_users')
            ->where('s_token', $input["s_token"])
            ->find_one();

        if (!$user) {
            throw new Exception("user not found");    
        }
        
        $permissions = explode("|", $user->permissions);
        $authorized = in_array($input["permission"], $permissions) ||
                      in_array("ROOT", $permissions);
                      
        return [
            "authorized" => $authorized  
        ];
    }
    
    public function createRoot($input) {
        $user = ORM::for_table('auth_users')
            ->create();
            
        $user->username = "root";
        $user->password = password_hash($input["password"], \PASSWORD_DEFAULT);
        $user->name = "root";
        $user->permissions = "ROOT";
        $user->save();
        
        return [
            "success" => true  
        ];
    }
}
