<?php

define('PT_REQUIRE_IT', true);

require __DIR__.'/../pt/bootstrap.php';

require __DIR__.'/../tests/core/config.php';

return It\It::lives();
