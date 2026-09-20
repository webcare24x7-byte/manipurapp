<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /**
     * @var array<string, array<int, array>>
     */
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    private array $middleware = [];

    public function __construct()
    {
        $this->middleware = config('middleware');
    }

    public function get(
        string $uri,
        callable|array $handler,
        array $middleware = []
    ): void
    {
        $this->addRoute(
            'GET',
            $uri,
            $handler,
            $middleware
        );
    }

    public function post(
        string $uri,
        callable|array $handler,
        array $middleware = []
    ): void
    {
        $this->addRoute(
            'POST',
            $uri,
            $handler,
            $middleware
        );
    }

    private function addRoute(
        string $method,
        string $uri,
        callable|array $handler,
        array $middleware = []
    ): void
    {
        $this->routes[$method][] = [

            'uri' => $this->normalize($uri),

            'handler' => $handler,

            'middleware' => $middleware,

        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = $this->normalize($uri);

        foreach ($this->routes[$method] ?? [] as $route) {

            $pattern = preg_replace(
                '#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#',
                '([^/]+)',
                $route['uri']
            );

            $pattern = '#^' . $pattern . '$#';

            if (!preg_match($pattern, $uri, $matches)) {
                continue;
            }

            array_shift($matches);

            $handler = $route['handler'];

            foreach ($route['middleware'] as $alias) {

                if (!isset($this->middleware[$alias])) {

                    throw new \RuntimeException(
                        "Middleware [{$alias}] not registered."
                    );
                }

                $class = $this->middleware[$alias];

                $instance = new $class();

                $instance->handle();
            }

            if (is_array($handler)) {

                [$controller, $action] = $handler;

                $instance = new $controller();

                $matches = array_map(
                    static function ($value) {

                        if (ctype_digit($value)) {
                            return (int) $value;
                        }

                        return $value;
                    },
                    $matches
                );

                $instance->$action(...$matches);

                return;
            }

            $handler(...$matches);

            return;
        }

        http_response_code(404);

        echo "<h1>404</h1>";
        echo "<p>Page not found.</p>";
    }

    private function normalize(string $uri): string
    {
        $uri = parse_url($uri, PHP_URL_PATH) ?? '/';

        $basePath = config('app.base_path', '');

        if ($basePath !== '' && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }

        $uri = '/' . trim($uri, '/');

        return $uri === '/' ? '/' : rtrim($uri, '/');
    }
}