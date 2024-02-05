<?php
declare(strict_types=1);

namespace App\Database;

use PDO;

class Database
{
    private PDO $pdo;

    public function __construct(array $config)
    {
        $defaults = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        ];

        try {
            $this->pdo = new PDO(
                $config['driver'] . ':host=' . $config['host'] . ';dbname=' . $config['database'],
                $config['user'], $config['pass'], $config['options'] ?? $defaults
            );
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }
    
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->pdo, $name], $arguments);
    }
}