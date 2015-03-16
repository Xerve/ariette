<?php

namespace Pt\Core\Apps;

class CMS extends \Pt\PtApp {
    public static $NAME = "Pt::CMS";
    public static $DEPENDENCIES = [
        "Pt::Validator", 
        "Pt::FlatDB",
        "Pt::Auth",
        "Pt::AuthMiddleWare"
    ];
    
    public function handler($input) {
        return [];
    }
}