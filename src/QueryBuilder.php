<?php

namespace App;

use PDO;
use\Exception;

class QueryBuilder
{
    private $from;
    private $order = [];
    private $limit;
    private $offset;
    private $page;
    private $where;
    private $fields = ["*"];
    private $params = [];
    private $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    //From is a fluent method. Returns instance of QB so that toSQL can get it
    public function from(string $table, string $alias = null): self
    {

        $this->from = $alias === null ? "$table" : "$table $alias";

        return $this;
    }


    public function orderBy(string $key, string $direction): self
    {
        $direction = strtoupper($direction);
        if (!in_array($direction, ['ASC', 'DESC'])) {
            $this->order[] = "$key";
        } else {
            $this->order[] = "$key $direction";
        }
        return $this;
    }

    public function limit(int $limit): self
    {

        $this->limit = $limit;

        return $this;
    }

    public function offset(int $offset): self
    {
        if ($this->limit === null ) {
            throw new Exception('Impossible to define offset if no limit has been defined.');
        }
        $this->offset = $offset;

        return $this;
    }

    public function page(int $page): self
    {

        return $this->offset($this->limit * ($page - 1));
    }

    public function where(string $where): self
    {

        $this->where = $where;
        return $this;
    }

    public function setParam(string $key, $value): self
    {
        $this->params[$key] = $value;
        return $this;
    }

    public function select(...$fields): self
    {
        if (is_array($fields[0])) {
            $fields = $fields[0];
        }
        if ($this->fields === ["*"]) {
            $this->fields = $fields;
        } else {
            $this->fields = array_merge($this->fields, $fields);
        }
        return $this;
    }

    public function fetch(string $field): ?string
    {

        $query = $this->pdo->prepare($this->toSQL());
        $query->execute($this->params);
        $result = $query->fetch();
        if ($result === false) {
            return null;
        }
        return $result[$field] ?? null;
    }

    public function fetchAll(): array
    {
        try {
        $query = $this->pdo->prepare($this->toSQL());
        $query->execute($this->params);
        return $query->fetchAll();
        } catch (\Exception $e) {
            throw new \Exception("Impossible to do this request " . $this->toSQL() . " : " . $e->getMessage());
        }
    }

    public function count(): int
    {
        // Clone is useful with count to avoid changing the initial SELECT
        $query = clone $this;
        return (int)$query->select('COUNT(id) count')->fetch('count');
    }

    public function toSQL(): string
    {
        $fields = implode(', ', $this->fields);
        $sql = "SELECT $fields FROM {$this->from}";

        if ($this->where) {
            $sql .= " WHERE " . $this->where;
        }
        if (!empty($this->order)) {
            $sql .= " ORDER BY " . implode(', ', $this->order);
        }
        if ($this->limit > 0) {
            $sql .= " LIMIT " . $this->limit;
        }
        if ($this->offset !== null) {
            $sql .= " OFFSET " . $this->offset;
        }
        return $sql;
    }
}