<?php

namespace Pt\Core\Apps;

class Test extends \Pt\PtApp {
    public static $NAME = "Pt::Test";
    
    public function handler($input) {
        return ['pt' => true];
    }
}
