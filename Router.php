<?php

class Router {
    private $routes = [];

    public function get($path, $callback) {
        $this->addRoute('GET', $path, $callback);
    }

    public function post($path, $callback) {
        $this->addRoute('POST', $path, $callback);
    }

    private function addRoute($method, $path, $callback) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback
        ];
    }

    public function run() {
        $requestedMethod = $_SERVER['REQUEST_METHOD'];
        $requestedPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            $pathRegex = $this->convertPathToRegex($route['path']);
            if ($route['method'] === $requestedMethod && preg_match($pathRegex, $requestedPath, $matches)) {
                array_shift($matches);

                $queryParams = $_GET;

                call_user_func_array($route['callback'], array_merge($matches, [$queryParams]));
                return;
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }

    private function convertPathToRegex($path) {
        return '#^' . preg_replace('#:([\w]+)#', '(?P<$1>[\w-]+)', $path) . '$#';
    }
}


