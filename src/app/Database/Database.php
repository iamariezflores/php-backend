<?php
declare(strict_types=1);

namespace App\Database;

use PDO;

class Database
{
    private PDO $pdo;
    private array $log = [];

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

    public function create(string $table, array $data): int
    {
        $fields = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $stmt = $this->pdo->prepare("INSERT INTO $table ($fields) VALUES ($placeholders");
        $stmt->execute(array_values($data));

        return (int) $this->pdo->lastInsertId();
    }

    public function update(string $table, int $id, array $data): bool
    {
        $set = implode(', ', array_map(fn($k) => "$k = ?", array_keys($data)));
        $values = array_values($data);
        $values[] = $id;

        $stmt = $this->pdo->prepare("UPDATE $table SET $set WHERE id = ?");
        return $stmt->execute($values);
    }

    public function delete(string $table, int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM $table WHERE id = ?");
        return  $stmt->execute([$id]);
    }

    public function findById(string $table, int $id): mixed
    {
        $stmt = $this->pdo->prepare("SELECT * FROM $table WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findBy(string $table, string $field, mixed $value): mixed
    {
        $stmt = $this->pdo->prepare("SELECT * FROM $table WHERE $field = ? LIMIT 1");
        $stmt->execute([$value]);
        return $stmt->fetch();
    }

    public function all(string $table): array
    {
        $stmt = $this->pdo->query("SELECT * FROM $table");
        return $stmt->fetchAll();
    }

    public function exists(string $table, string $field, mixed $value): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
        $stmt->execute([$value]);
        return $stmt->fetchColumn() > 0;
    }

    public function paginate(string $table, int $limit = 10, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM $table LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function count(string $table): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM $table");
        return (int) $stmt->fetch()['count'];
    }

    public function where(string $table, array $conditions): array
    {
        $where = implode(' AND ', array_map(fn($k) => "$k = ?", array_keys($conditions)));
        $stmt = $this->pdo->prepare("SELECT * FROM $table WHERE $where");
        $stmt->execute(array_values($conditions));
        return $stmt->fetchAll();
    }

    public function pluck(string $table, string $column, ?string $key = null): array
    {
        $rows = $this->all($table);
        $result = [];

        foreach ($rows as $row) {
            if ($key && isset($row[$key])) {
                $result[$row[$key]] = $row[$column];
            } else {
                $result[] = $row[$column];
            }
        }

        return $result;
    }

    public function first(string $table): mixed
    {
        $stmt = $this->pdo->query("SELECT * FROM $table LIMIT 1");
        return $stmt->fetch();
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    public function truncate(string $table): void
    {
        $this->pdo->exec("TRUNCATE TABLE $table");
    }

    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollBack(): void
    {
        $this->pdo->rollBack();
    }

    public function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }

    private function logQuery(string $query, array $params = []): void
    {
        $this->log[] = [
            'query' => $query,
            'params' => $params,
            'time' => date('Y-m-d H:i:s')
        ];
    }

    public function getQueryLog(): array
    {
        return $this->log;
    }

    public function clearLog(): void
    {
        $this->log = [];
    }

    public function dumpLog(): void
    {
        foreach ($this->log as $entry) {
            echo "[{$entry['time']}] {$entry['query']} - " . json_encode($entry['params']) . "\n";
        }
    }

    public function getColumns(string $table): array
    {
        $stmt = $this->pdo->query("DESCRIBE $table");
        return $stmt->fetchAll();
    }

    public function tableExists(string $table): bool
    {
        $stmt = $this->pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        return $stmt->rowCount() > 0;
    }
}