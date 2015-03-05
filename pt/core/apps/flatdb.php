<?php

namespace Pt\Core\Apps;

use \Exception;

class FlatDB extends \Pt\PtApp {
    public static $NAME = "Pt::FlatDB";
    public static $DEPENDENCIES = ["Pt::Validator"];
    public static $METHODS = ["query", "update"];
    
    private $path;
    
    public function __construct($options=[]) {
        if (!array_key_exists("path", $options)) {
            throw new \Exception("Path for FlatDB not specified");
        }
        
        $this->path = $options["path"];
    }
    
    public function init($apps, $wares) {
        $wares["Pt::Validator"]->addRule("Pt::FlatDB.query", [
            "query" => true 
        ]);
        
        $wares["Pt::Validator"]->addRule("Pt::FlatDB.update", [
            "query" => true,
        ]);
    }
    
    public function handler($input) {
        $this->query($input, $apps, $wares);
    }
    
    public function query($input) {
        $query = $input["query"];
        $base = $this->path;
        
        foreach(explode("::", $query) as $p) {
            $base = $base . "/$p";
            $last = $p;
        }
        
        if (file_exists($base)) {
            return [
                "result" => file_get_contents($base),
                "meta" => json_decode(file_get_contents(dirname($base)."/$last.meta.json"), true)
            ];
        } else {
            return [
                "error" => 500,
                "message" => "File $query not found"
            ];
        }
    }
    
    public function update($input) {
        if (array_key_exists("mode", $input)) {
            $mode = $input["mode"];
        } else {
            $mode = "w";
        }
        
        $query = $input["query"];
        $base = $this->path;
        
        foreach(explode("::", $query) as $p) {
            $base = $base . "/$p";
            $last = $p;
        }
        
        if (!file_exists(dirname($base))) {
            mkdir(dirname($base), 777, true);
            $meta = fopen(dirname($base)."/$last.meta.json", "w");
            fwrite($meta, "{}");
            fclose($meta);
        }
        
        if (array_key_exists("data", $input)) {
            $data = fopen($base, $mode);
            fwrite($data, $input["data"]);
            fclose($data);
        }
        
        if (array_key_exists("meta", $input)) {
            $meta = json_decode(file_get_contents(dirname($base)."/$last.meta.json"), true);
            foreach($input["meta"] as $key => $val) {
                $meta[$key] = $val;
            }
            
            $mfile = fopen(dirname($base)."/$last.meta.json", "w");
            fwrite($mfile, json_encode($meta));
            fclose($mfile);
        }
        
        return [
            "success" => true  
        ];
    }
}