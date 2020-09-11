<?php

namespace Source\Core;

use Source\Support\Message;

abstract class Model 
{
    protected $data;
    protected $fail;
    protected $message;
    protected $query;
    protected $params;
    protected $order;
    protected $limit;
    protected $offset;
    protected $entity;
    protected $protected;
    protected $required;

    public function __construct(string $entity, array $protected, array $required)
    {   
        $this->entity = $entity;
        $this->protected = array_merge($protected, ['CREATED_AT', 'UPDATED_AT']);
        $this->required = $required;

        $this->message = new Message();
    }

    public function __set($name, $value)
    {
        if (empty($this->data)) {
            $this->data = new \stdClass();
        }

        $this->data->$name = $value;
    }

    public function __isset($name)
    {
        return isset($this->data->$name);
    }

    public function __get($name)
    {
        return ($this->data->$name ?? null);
    }

    public function data(): ?object 
    {
        return $this->data;
    }

    public function fail(): ?\PDOException
    {
        return $this->fail;
    }

    public function message(): ?Message
    {
        return $this->message;
    }

    public function find(?string $terms = null, ?string $params = null, string $columns = "*"): Object
    {
        if ($terms) {
            $this->query = "SELECT {$columns} FROM {$this->entity} WHERE {$terms}";
            parse_str($params, $this->params);
            return $this;
        }

        $this->query = "SELECT {$columns} FROM {$this->entity}";
        return $this;
    }

    public function findById(int $id, string $columns = "*"): ?Model 
    {
        $find = $this->find("ID = :ID", "ID={$id}", $columns);
        return $find->fetch();
    }

    public function order(string $columnOrder): Model 
    {
        $this->order = " ORDER BY {$columnOrder}";
        return $this;
    }

    public function limit(int $limit): Model 
    {
        $this->limit = " LIMIT {$limit}";
        return $this;
    }

    public function offset(int $offset): Model 
    {
        $this->offset = " OFFSET {$offset}";
        return $this;
    }

    public function fetch(bool $all = false): ?object
    {
        try {
            $stmt = Connect::getInstance()->prepare($this->query . $this->order . $this->limit . $this->offset);
            $stmt->execute($this->params);

            if (!$stmt->rowCount()) {
                return null;
            }

            if ($all) {
                return $stmt->fetchAll(\PDO::FETCH_CLASS, static::class);
            }

            return $stmt->fetchObject(static::class);
        }catch(\PDOException $e) {
            $this->fail = $e;
            return null;
        }
    }

    public function count(): int 
    {
        $stmt = Connect::getInstance()->prepare($this->query);
        $stmt->execute($this->params);
        return $stmt->rowCount();
    }

    protected function create(array $data): ?int 
    {
        try {
            $columns = implode(', ', array_keys($data));
            $values = ":" . implode(", :", array_keys($data));
            
            $stmt = Connect::getInstance()->prepare("INSERT INTO {$this->entity} ({$columns}) VALUES ({$values})");
            $stmt->execute($this->filter($data));

            return Connect::getInstance()->lastInsertId();
        } catch(\PDOException $e) {
            $this->fail = $e;
            return null;
        }
    }

    protected function update(array $data, string $terms, string $params): ?int 
    {
        try {
            $dateSet = [];
            foreach($data as $bind => $value) {
                $dateSet[] = "{$bind} = :{$bind}";
            }

            $dateSet = implode(", ", $dateSet);
            parse_str($params, $params);

        $stmt = Connect::getInstance()->prepare("UPDATE {$this->entity} SET {$dateSet} WHERE {$terms}");
        $stmt->execute($this->filter(array_merge($data, $params)));
        return ($stmt->rowCount() ?? 1);
        }catch(\PDOException $e) {
            $this->fail = $e;
            return null;
        }
    }

    public function save(): bool 
    {
        if (!$this->required()) {
            $this->message->warning("Preencha todos os campos para continuar");
            return false;
        }

        /** Update */
        if (!empty($this->id)) {
            $id = $this->id;
            $this->update($this->safe(), "ID = :ID", "ID={$id}");
            if ($this->fail()) {
                $this->message->error("Erro ao atualizar, verifique os dados");
                return false;
            }
        }

        /** Create */
        if (empty($this->id)) {
            $id = $this->create($this->safe());
            if ($this->fail()) {
                $this->message->error("Erro ao cadastrar, verifique os dados");
                return false;
            }
        }

        $this->data = $this->findById($id)->data();
    }

    public function lastId(): int 
    {
        return Connect::getInstance()->query("SELECT MAX(id) AS maxId FROM {$this->entity}")->fetch()->maxId + 1;
    }

    public function delete(string $terms, ?string $params): bool 
    {
        try {
            $stmt = Connect::getInstance()->prepare("DELETE FROM {$this->entity} WHERE {$terms}");
            if ($params) {
                parse_str($params, $params);
                $stmt->execute($params);
                return true;
            }

            $stmt->execute();
            return true;
        }catch(\PDOException $e) {
            $this->fail = $e;
            return false;
        }
    }

    public function destroy(): bool 
    {
        if (empty($this->ID)) {
            return false;
        }

        $destroy = $this->delete("ID = :ID", "ID={$this->id}");
        return $destroy;
    }

    protected function safe(): ?array 
    {
        $safe = (array)$this->data;
        foreach($this->protected as $unset) {
            unset($safe[$unset]);
        }
        return $safe;
    }

    private function filter(array $data): ?array 
    {
        $filter = [];
        foreach($data as $key => $value) {
            $filter[$key] = (is_null($value) ? null : filter_var($value, FILTER_DEFAULT));
        }

        return $filter;
    }

    protected function required(): bool 
    {
        $data = (array)$this->data();
        foreach($this->required as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }
        return true;
    }
}

