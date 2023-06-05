<?php

class Request
{
    private static $request = [];

    public static function headers(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_') {
                continue;
            }
            $header = strtolower(str_replace([' ', '_'], '-', substr($key, 5)));
            $headers[$header] = $value;
        }
        return $headers;
    }

    public static function body(): array
    {
        return (array)@json_decode(file_get_contents('php://input'), true) ?: [];
    }

    public static function all(): array
    {
        if (!self::$request) {
            self::$request = array_merge(self::headers(), self::body(), $_REQUEST);
        }
        return self::$request;
    }

    public static function value(string $key): ?string
    {
        if (!self::$request) {
            self::$request = array_merge(self::headers(), self::body(), $_REQUEST);
        }
        return @self::$request[$key] ?: null;
    }
}
