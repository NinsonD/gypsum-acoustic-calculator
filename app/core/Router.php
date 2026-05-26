<?php

declare(strict_types=1);

final class Router
{
    private array $routes = [];

    public function __construct(private array $config)
    {
    }

    public function get(string $path, array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, array $handler): void
    {
        $this->routes[$method][] = [
            'path' => $this->normalize($path),
            'handler' => $handler,
            'pattern' => $this->compile($path),
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $basePath = parse_url((string) ($this->config['url'] ?? ''), PHP_URL_PATH);

        if (is_string($basePath) && $basePath !== '' && $basePath !== '/' && str_starts_with($path, rtrim($basePath, '/'))) {
            $path = substr($path, strlen(rtrim($basePath, '/'))) ?: '/';
        }

        $path = $this->normalize($path);
        $routes = $this->routes[$method] ?? [];

        foreach ($routes as $route) {
            if (preg_match($route['pattern'], $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $this->call($route['handler'], $params);
                return;
            }
        }

        http_response_code(404);
        (new PageController($this->config))->notFound();
    }

    private function call(array $handler, array $params = []): void
    {
        [$class, $method] = $handler;
        $controller = new $class($this->config);
        $controller->$method(...array_values($params));
    }

    private function normalize(string $path): string
    {
        $path = '/' . trim($path, '/');
        return $path === '/' ? '/' : rtrim($path, '/');
    }

    private function compile(string $path): string
    {
        $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[a-zA-Z0-9-]+)', $this->normalize($path));
        return '#^' . $pattern . '$#';
    }
}
