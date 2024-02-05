<?php
declare(strict_types=1);

namespace App\Routing;

use App\Exceptions\RouteNotFoundException;

class Router
{
    private array $routes = [];

    public function registerRoute(string $requestMethod, string $route, callable|array $action) : self
    {
        $this->routes[$requestMethod][$route] = $action;
        return $this;
    }

    public function get(string $route, callable|array $action): self
    {
        return $this->registerRoute('get', $route, $action);
    }

    public function post(string $route, callable|array $action): self
    {
        return $this->registerRoute('post', $route, $action);
    }

    public function routes(): array
    {
        return $this->routes;
    }

    public function resolveRoute(string $requestURI, string $requestMethod)
    {
        $route = explode('?', $requestURI)[0];
        $action = $this->routes[$requestMethod][$route] ?? null;

        if(!$action){
            throw new RouteNotFoundException();
        }

        if(is_callable($action)){
            return call_user_func($action);
        }

        [$class, $method] = $action;

        if(class_exists($class)){
            $class = new $class();

            if(method_exists($class, $method)){
                return call_user_func_array([$class, $method], []);
            }
        } 

        throw new RouteNotFoundException();
    }
}