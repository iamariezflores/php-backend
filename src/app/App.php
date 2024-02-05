<?php
declare(strict_types=1);

namespace App;

use App\Database\Database;
use App\Exceptions\RouteNotFoundException;
use App\Routing\Router;

class App
{
    private static Database $db;

    public function __construct(protected Router $router, protected array $request, protected array  $config)
    {
        static::$db = new Database($config);
    }

    public static function db(): Database
    {
        return static::$db;
    }

    public function init()
    {
        try {
            echo $this->router->resolveRoute($this->request['uri'], strtolower($this->request['method']));
        } catch (RouteNotFoundException) {
            http_response_code(404);
        }
    }
}