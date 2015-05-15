<?php

const ARIETTE_REQUIRE_IT = true;

require __DIR__.'/../ariette/bootstrap.php';

require __DIR__.'/../tests/core/config.php';

if (It\It::lives()) {
    exit(0);
} else {
    exit(1);
}
