<?php
declare(strict_types=1);

namespace App;

class Router
{
    private array $routes = [];

    public function add(string $method, string $pattern, callable $handler, array $middleware = []): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'regex' => $this->toRegex($pattern),
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function get(string $pattern, callable $h, array $mw = []): void { $this->add('GET', $pattern, $h, $mw); }
    public function post(string $pattern, callable $h, array $mw = []): void { $this->add('POST', $pattern, $h, $mw); }
    public function put(string $pattern, callable $h, array $mw = []): void { $this->add('PUT', $pattern, $h, $mw); }
    public function delete(string $pattern, callable $h, array $mw = []): void { $this->add('DELETE', $pattern, $h, $mw); }

    private function toRegex(string $pattern): string
    {
        // Replace {param} and {param:regex} with capture groups.
        $pattern = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_]*)(?::([^}]+))?\}/', function ($m) {
            $name = $m[1];
            $regex = $m[2] ?? '([^/]+)';
            // Use named group for readability.
            return '(?P<' . $name . '>' . $regex . ')';
        }, $pattern);
        return '#^' . $pattern . '$#u';
    }

    public function dispatch(string $method, string $path): mixed
    {
        $path = rtrim($path, '/');
        if ($path === '') $path = '/';
        foreach ($this->routes as $route) {
            if ($route['method'] !== strtoupper($method)) continue;
            if (preg_match($route['regex'], $path, $m)) {
                $params = [];
                foreach ($m as $k => $v) {
                    if (!is_numeric($k)) $params[$k] = urldecode($v);
                }
                // Run middleware
                foreach ($route['middleware'] as $mw) {
                    $result = $mw($params);
                    if ($result !== null) return $result;
                }
                call_user_func($route['handler'], $params);
                return true;
            }
        }
        return false;
    }

    public function routeExists(string $method, string $path): bool
    {
        return $this->dispatch($method, $path) !== null;
    }
}
