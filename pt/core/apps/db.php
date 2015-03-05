<?php

namespace Pt\Core\Apps;

use \Exception;
use \ORM;

class DB extends \Pt\PtApp {
    public static $NAME = "Pt::DB";
    
    private $schemas = [];
    
    public function __construct($options=false) {
        ORM::configure("sqlite:./ptdb.db", null, "pt");
        
        if ($options) {
            if (!array_key_exists("connection_string", $options)) {
                throw new Exception("No connection_string provided for Pt::DB");
            }
    
            ORM::configure($options);            
        } else {
            ORM::configure("sqlite:./ptdb.db");
        }
    }
    
    public function handler($input) {
        return [];
    }
    
    public function schema($name, $schema) {
        $arr = explode("::", $name);
        $db = $arr[0];
        $table = $arr[1];
        
        if (!array_key_exists($db, $this->schemas)) {
            $this->schemas[$db] = [];
        }
        
        $this->schemas[$db][$table] = $schema;
    }
    
    public function getSchema($db) {
        $ret = "";
        
        foreach($this->schemas[$db] as $name => $schema) {
            $ret = $ret . "CREATE TABLE " . $name . "(";
            foreach($schema as $line) {
                $ret = $ret . $line . ",";
            }
            
            $ret = rtrim($ret, ",") . ");";
        }
        
        echo $ret;
    }
}