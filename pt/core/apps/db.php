<?php

namespace Pt\Core\Apps;

use \Exception;
use \ORM;

class DB extends \Pt\PtApp {
    public static $NAME = "Pt::DB";
    
    private $schemas = [];
    private $pks = [];
    
    public function __construct($options=false) {
        if ($options) {
            if (!array_key_exists("connection_string", $options)) {
                throw new Exception("No connection_string provided for Pt::DB");
            }
    
            ORM::configure($options);            
        } else {
            ORM::configure("sqlite:./pt.db");
        }
    }
    
    public function handler($input) {
        return [];
    }
    
    public function schema($table, $pk, $schema) {
        $this->pks[$table] = $pk;
        ORM::configure("id_column_overrides", $this->pks);
        
        $this->schemas[$table] = $schema;
    }
    
    public function getSchema() {
        $ret = "";
        
        foreach($this->schemas as $name => $schema) {
            $ret = $ret . "CREATE TABLE " . $name . "(";
            foreach($schema as $line) {
                $ret = $ret . $line . ",";
            }
            
            $ret = rtrim($ret, ",") . ");";
        }
        
        return $ret;
    }
}