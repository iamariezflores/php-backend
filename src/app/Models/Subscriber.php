<?php
declare(strict_types=1);

namespace App\Models;

class Subscriber extends Model
{
    public function create(string $email, string $name, string $last_name, int $status = 1) : int
    {
        $sql = $this->db->prepare(
            'INSERT INTO subscribers(email, name, last_name, status)
            VALUES(?, ?, ?, ?)'
        );

        $sql->execute([$email, $name, $last_name, $status]);
        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): mixed
    {
        $sql = $this->db->prepare(
            'SELECT * FROM subscribers WHERE id=?'
        );

        $sql->execute([$id]);
        $subsriber = $sql->fetchAll();

        return $subsriber;
    }

    public function getSubscribers(): array
    {
        $redis = new \Redis();
        $redis->connect('aflores_redis', 6379);

        $key = 'subscribers';
        $result = [];
        if(!$redis->get($key)){
            $sql = $this->db->prepare(
                'SELECT * FROM subscribers'
            );
    
            $sql->execute();
            $r = $sql->fetchAll();

            $redis->set($key, serialize($r));
            $redis->expire($key, 10);
            $result = $r;
        } else {
            $result = unserialize($redis->get($key));
        }

        return $result;
    }

    public function checkIfExists(string $email): mixed
    {
        $sql = 'SELECT id, email FROM subscribers WHERE email=? LIMIT 1';
        $query = $this->db->prepare($sql);
        $query->execute([$email]);
        $result = $query->fetch();

        return $result;
    }
}