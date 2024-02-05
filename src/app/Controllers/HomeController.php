<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Response;

class HomeController
{
    public function index()
    {
        // $redis = new \Redis();
        // $redis->connect('aflores_redis', 6379);
        // $msg = "Server is running: " . $redis->ping();

        $res = ['status' => http_response_code(), 'message' => 'API Version 1.0.0'];
        return Response::make($res, http_response_code());
        phpinfo();
    }
}