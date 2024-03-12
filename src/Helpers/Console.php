<?php

namespace App\Helpers;

final class Console
{
    public static function log(mixed $message): void
    {
        ob_start();
        var_dump($message);
        error_log((string) ob_get_clean(), 4);
    }
}
