<?php

class Route
{

    private static $include;

    public static function path(string $position = null): ?string
    {
        $parent = explode('/', $_SERVER['SCRIPT_NAME']);
        $parent = array_slice($parent, 0, -1);
        $parent = self::implode('/', $parent) . '/';
        $parent = strlen($parent);
        $path = substr($_SERVER['REQUEST_URI'], $parent);
        if (strstr($path, '?')) {
            $pos = strpos($path, '?');
            $path = substr($path, 0, $pos);
        }
        $path = trim($path, '/');

        if (!$position) {
            return $path;
        }

        if (is_numeric($position)) {
            return @explode('/', $path)[$position - 1] ?: null;
        }

        if (!@self::$include['path']) {
            return null;
        }

        foreach (self::$include['path'] as $key => $value) {
            if (mb_strtolower('$' . $position) === mb_strtolower($value)) {
                return @explode('/', $path)[$key] ?: null;
            }
        }

        return null;
    }

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

    public static function json(?array $array = null, int $code = 200): void
    {
        header('Access-Control-Allow-Headers: *');
        header('Access-Control-Allow-Methods: *');
        header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json; charset=utf-8');
        http_response_code($code);
        if ($array) {
            echo html_entity_decode(json_encode($array)) ?: '{}';
        }
        exit();
    }

    private static function files(string $folder, array &$return = [])
    {
        $files = scandir($folder);
        foreach ($files as $value) {
            $path = realpath($folder . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $return[] = $path;
            } else if ($value != "." && $value != "..") {
                self::files($path, $return);
            }
        }
        return $return;
    }

    public static function execute(string $folder): string
    {
        $scheme = [];
        $files = self::files($folder);
        foreach ($files as &$file) {
            $search = [$folder, '.php', '_', '.', '\\'];
            $replace = ['', '', '/', '/', '/'];
            $clean = str_replace($search, $replace, $file);
            @[$fix, $method] = explode('--', $clean);
            $path = explode('/', trim($fix, '/'));
            $method = mb_strtoupper($method ?: 'ANY');
            $scheme[$method][] = ['path' => $path, 'file' => $file];
        }

        $files = array_merge(
            @$scheme['ANY'] ?: [],
            @$scheme[$_SERVER['REQUEST_METHOD']] ?: []
        );

        $path = self::path();
        $path =  explode('/', $path);
        if (reset($path) == '') {
            $path = ['index'];
        }

        foreach ($files as $key => $file) {
            if (count($file['path']) !== count($path)) {
                unset($files[$key]);
            }
        }

        for ($i = 0; $i < count($path); $i++) {
            foreach ($files as $key => $file) {
                @$compare = $file['path'][$i];
                if ($compare !== $path[$i] && !stristr($compare, '$')) {
                    unset($files[$key]);
                }
            }
        }

        if (count($files) !== 1) {
            http_response_code(404);
            return $folder . DIRECTORY_SEPARATOR . '404.php';
        }

        self::$include = current($files);

        return self::$include['file'];
    }

    // utils -------------------------------------------------------------------

    private static function implode(string $glue, array $array): string
    {
        $return = '';
        foreach ($array as $a) {
            $return .= $a . $glue;
        }
        $return = substr($return, 0, strlen($glue) * -1);
        return $return;
    }
}
