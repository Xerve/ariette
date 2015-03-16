<?php

require_once __DIR__.'/vendor/autoload.php';

$pt = new Pt\Pt();

$pt->middleware(new Pt\Core\Wares\Validator());

$pt->register(new Pt\Core\Apps\DB());

$pt->register(new Pt\Core\Apps\FlatDB([
 "path" => __DIR__."/flatdb"
]));

$pt->register(new Pt\Core\Apps\Test());

$pt->register(new Pt\Core\Apps\Auth());

echo $pt->handler();