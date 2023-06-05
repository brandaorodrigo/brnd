<?php

class Route
{
    private static $routes;

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

        return null;
    }

    private static function fragment(): array
    {
        $path = self::path();
        return explode('/', $path);
    }

    private static function register(string $method, string $uri, callable $callback, ?callable $then = null): void
    {
        self::$routes[mb_strtolower($method)][] = [
            'fragment' => explode('/', trim($uri, '/')),
            'callback' => $callback,
            'then' => $then
        ];
    }

    public static function get(string $uri, callable $callback, ?callable $then = null): void
    {
        self::register('get', $uri, $callback, $then);
    }

    public static function post(string $uri, callable $callback, ?callable $then = null): void
    {
        self::register('post', $uri, $callback, $then);
    }

    public static function put(string $uri, callable $callback, ?callable $then = null): void
    {
        self::register('put', $uri, $callback, $then);
    }

    public static function patch(string $uri, callable $callback, ?callable $then = null): void
    {
        self::register('patch', $uri, $callback, $then);
    }

    public static function delete(string $uri, callable $callback, ?callable $then = null): void
    {
        self::register('delete', $uri, $callback, $then);
    }

    public static function include(string $folder): void
    {
        $path = explode('/', self::path());
        if (reset($path) == '') {
            $path = ['index'];
        }

        $files = [];

        for ($i = count($path); $i > 0; $i--) {
            $current = [];
            for ($j = 0; $j < $i; $j++) {
                $current[] = $path[$j];
            }
            $files[] = self::implode(DIRECTORY_SEPARATOR, $current) . '.php';
            $files[] = self::implode('_', $current) . '.php';
        }

        foreach ($files as $file) {
            $file = $folder . DIRECTORY_SEPARATOR . $file;
            if (file_exists($file)) {
                include $file;
                break;
            }
        }
    }

    public static function execute(): void
    {
        $path = explode('/', self::path());

        $method = mb_strtolower($_SERVER['REQUEST_METHOD']);

        $routes = @self::$routes[$method] ?: [];

        foreach ($routes as $key => $route) {
            if (count($route['fragment']) !== count($path)) {
                unset($routes[$key]);
            }
        }

        for ($i = 0; $i < count($path); $i++) {
            foreach ($routes as $key => $route) {
                @$compare = $route['fragment'][$i];
                if ($compare !== $path[$i] && !stristr($compare, '$')) {
                    unset($routes[$key]);
                }
            }
        }

        if (count($routes) !== 1) {
            http_response_code(404);
            return;
        }

        $include = current($routes);

        $vars = [];
        foreach ($include['fragment'] as $key => $path) {
            if (stristr($path, '$')) {
                $vars[] = self::path($key + 1);
            }
        }

        $include['callback'](...$vars);

        exit();
    }

    // utils -------------------------------------------------------------------

    private static function implode(string $glue, array $array): string
    {
        $return = '';
        foreach ($array as $value) {
            $return .= $value . $glue;
        }
        $return = substr($return, 0, strlen($glue) * -1);
        return $return;
    }
}
