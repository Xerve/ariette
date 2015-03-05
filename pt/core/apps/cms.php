<?php

namespace Pt\Core\Apps;

class CMS extends \Pt\PtApp {
    public static $NAME = "Pt::CMS";
    public static $DEPENDENCIES = ["Pt::FlatDB"];
    
    public function __construct($options=[]) {
        
    }
    
    public function handler($input) {
        return [];
    }
}