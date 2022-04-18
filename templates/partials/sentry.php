<?php

use function Sentry\init;

if (ENV === ENV_PRODUCTION && defined('SENTRY_DSN') && SENTRY_DSN != '') {
    init(['dsn' => SENTRY_DSN]);
}

