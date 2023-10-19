<?php

namespace Entersis;

class Router
{
    private $routes = [];
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function addRoute($method, $pathPattern, $handler)
    {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $this->convertPatternToRegex($pathPattern),
            'handler' => $handler,
        ];
    }

    private function convertPatternToRegex($pathPattern)
    {
        // Converte o padrão de caminho em uma expressão regular
        $regex = preg_replace('/\//', '\\/', $pathPattern);
        $regex = '/^' . $regex . '$/';
        return $regex;
    }

    public function run()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && preg_match($route['pattern'], $requestUri, $matches)) {
                array_shift($matches);
                $handler = $route['handler'];

                if (is_callable($handler)) {
                    call_user_func_array($handler, $matches);
                    return;
                } else {
                    list($class, $method) = explode('@', $handler);

                    if (class_exists($class)) {
                        $obj = new $class($this->db);
                        if (method_exists($obj, $method)) {
                            call_user_func_array([$obj, $method], $matches);
                            return;
                        }
                    }
                }
            }
        }

        $this->respond(405, 'Method Not Allowed');
    }

    private function respond($status, $data)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
