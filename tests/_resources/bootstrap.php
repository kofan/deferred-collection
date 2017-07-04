<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

if (!function_exists('noop')) {
    function noop(): void
    {
    }
}

date_default_timezone_set('UTC');
