<?php

define('ARIETTE_REQUIRE_IT', true);

require __DIR__.'/../ariette/bootstrap.php';

require __DIR__.'/../tests/core/config.php';

It\It::lives();
