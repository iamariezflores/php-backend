<?php
declare(strict_types=1);

namespace App\Core;

class Response
{
    public static function make(string|array $message, ?int $httpCode = 200): string|array
    {
        header('Content-type: text/json');
        http_response_code($httpCode);
        return json_encode($message, JSON_PRETTY_PRINT);
    }
}