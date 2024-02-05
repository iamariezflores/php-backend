<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\App;
use App\Controllers\HomeController;
use App\Controllers\SubscriberController;
use App\Routing\Router;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$router = new Router();
$router
    ->get('/', [HomeController::class, 'index'])
    ->get('/subscriber', [SubscriberController::class, 'index'])
    ->post('/subscriber', [SubscriberController::class, 'create'])
    ->get('/subscriber/find', [SubscriberController::class, 'find']);

$config = [
    'host'      => $_ENV['DB_HOST'],
    'user'      => $_ENV['DB_USER'],
    'pass'      => $_ENV['DB_PASS'],
    'database'  => $_ENV['DB_DATABASE'],
    'driver'    => $_ENV['DB_DRIVER'] ?? 'mysql',
];

$routerObj = [
    'uri'       => $_SERVER['REQUEST_URI'],
    'method'    => $_SERVER['REQUEST_METHOD']
];

(new App($router, $routerObj, $config))->init();
