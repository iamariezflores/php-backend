<?php
declare(strict_types=1);

namespace App\Models;

class Subscriber extends Model
{
    protected $table = "subscribers";

    public function create(string $email, string $name, string $last_name, int $status = 1) : int
    {
        return $this->db->create($this->table, [
            'email'     => $email,
            'name'      => $name,
            'last_name' => $last_name,
            'status'    => $status,
        ]);
    }

    public function findById(int $id): mixed
    {
        return $this->db->findById($this->table, $id);
    }

    public function getSubscribers(): array
    {
        $redis = new \Redis();
        $redis->connect('aflores_redis', 6379);

        $key = 'subscribers';
        if (!$redis->get($key)) {
            $subscribers = $this->db->all($this->table);
            $redis->set($key, serialize($subscribers));
            $redis->expire($key, 10);
            return $subscribers;
        }

        return unserialize($redis->get($key));
    }

    public function checkIfExists(string $email): mixed
    {
        return $this->db->findBy($this->table, 'email', $email);
    }
}