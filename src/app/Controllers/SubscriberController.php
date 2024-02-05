<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Response;
use App\Models\Subscriber;

class SubscriberController
{
    public function index()
    {
    
        $subscribers = new Subscriber();
        $data = $subscribers->getSubscribers();
        $res = ['status' => 'ok', 'data' => $data];
        return Response::make($res);
    }

    public function create()
    {
        $res = [];
        $httpResponse = 200;
        $data = json_decode(file_get_contents('php://input'));
        if($data){
            $email = $data->email;

            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $res = ['status' => 'error', 'message' => 'Invalid email format'];
                $httpResponse = 422;
            } else {
                $subsriber = new Subscriber();
                $result = $subsriber->checkIfExists($email);

                if($result){
                    $res = ["status" => "ok", "message" => 'Subscriber already exists'];
                } else {       
                    $lastId = $subsriber->create($data->email, $data->name, $data->last_name, $data->status);
    
                    if($lastId){
                        $resultSet = $subsriber->findById($lastId);
                        $res = ["status" => "ok", "data" => $resultSet];
                    } else {
                       $res = ['status' => 'error', 'message' => 'There was an error inserting data'];
                    }

                }
            }

        } else {
            $res = ['status' => 'error', 'message' => 'No Data'];
        }

        return Response::make($res, $httpResponse);
    }

    public function find()
    {
        $id = (int) $_GET['id'];
        $subscriber = new Subscriber();
        $data = $subscriber->findById($id);

        $res = [];
        if($data){
            $res = ['status' => 'data', 'data' => $data];
        } else {
            $res = ['status' => 'error', 'message' => 'No subscriber found'];
        }

        return Response::make($res);
    }
}