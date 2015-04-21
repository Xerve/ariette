<?php

namespace Pt\Core\Apps;

use \Exception;
use \ORM;

class CMS extends \Pt\PtApp {
    public static $NAME = "Pt::CMS";
    public static $DEPENDENCIES = [
        "Pt::Validator", 
        "Pt::FlatDB",
        "Pt::Auth",
        "Pt::AuthMiddleWare"
    ];
    
    public function init($apps, $wares) {
        $apps["Pt::Validator"]->addRule("Pt::CMS.createBlogPost", [
            "text" => function($text) {
                if (!is_string($text)) {
                    throw new Exception("text is not string!");
                }
            },
            "user" => function($s_token) {
                if (!user) {
                    throw new Exception("user not found!");
                }
            }
        ]);
    }
    
    public function handler($input) {
        return [];
    }
    
    public function createBlogPost($input) {
        
    }
}