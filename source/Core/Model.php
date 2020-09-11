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

    /**
     * Função responsável por setar a data
     *
     * @return object|null
     */
    public function data(): ?object 
    {
        return $this->data;
    }

    /**
     * Função responsável por retornar o metodo fail
     *
     * @return \PDOException|null
     */
    public function fail(): ?\PDOException
    {
        return $this->fail;
    }

    /**
     * Função responsável por retornar o metodo de mensagem
     *
     * @return Message|null
     */
    public function message(): ?Message
    {
        return $this->message;
    }

    /**
     * Função responsável por encontrar um resultado em determinada tabela com determinada condição
     *
     * @param string|null $terms
     * @param string|null $params
     * @param string $columns
     * 
     * @return object
     */
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

    /**
     * Função responsável por encontrar um resultado pelo seu id
     *
     * @param integer $id
     * @param string $columns
     * 
     * @return Model|null
     */
    public function findById(int $id, string $columns = "*"): ?Model 
    {
        $find = $this->find("ID = :ID", "ID={$id}", $columns);
        return $find->fetch();
    }

    /**
     * Função responsável por ordernar uma consulta no banco de dados
     *
     * @param string $columnOrder
     * 
     * @return Model
     */
    public function order(string $columnOrder): Model 
    {
        $this->order = " ORDER BY {$columnOrder}";
        return $this;
    }

    /**
     * Função responsável por limitar a consulta feita 
     *
     * @param integer $limit
     * 
     * @return Model
     */
    public function limit(int $limit): Model 
    {
        $this->limit = " LIMIT {$limit}";
        return $this;
    }

    /**
     * Função responsável por fazer o offset da consulta
     *
     * @param integer $offset
     * 
     * @return Model
     */
    public function offset(int $offset): Model 
    {
        $this->offset = " OFFSET {$offset}";
        return $this;
    }

    /**
     * Função responsável por executar a query
     *
     * @param boolean $all
     * 
     * @return null|object
     */
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

    /**
     * Função responsável por contar os resultados
     *
     * @return integer
     */
    public function count(): int 
    {
        $stmt = Connect::getInstance()->prepare($this->query);
        $stmt->execute($this->params);
        return $stmt->rowCount();
    }

    /**
     * Função responsável por inserir uma query 
     *
     * @param array $data
     * 
     * @return integer|null
     */
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

    /**
     * Função responsável por atualizar uma informação
     *
     * @param array $data
     * @param string $terms
     * @param string $params
     * 
     * @return integer|null
     */
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

    /**
     * Funçao responsável por executar o metodo save de um insert, onde ele vai criar ou atualizar
     *
     * @return boolean
     */
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

    /**
     * Função responsável por retornar o ultimo id inserido 
     *
     * @return integer
     */
    public function lastId(): int 
    {
        return Connect::getInstance()->query("SELECT MAX(id) AS maxId FROM {$this->entity}")->fetch()->maxId + 1;
    }

    /**
     * Função responsável por deletar um registro
     *
     * @param string $terms
     * @param string|null $params
     * 
     * @return boolean
     */
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

    /**
     * Função responsável por executar o metodo delete 
     *
     * @return boolean
     */
    public function destroy(): bool 
    {
        if (empty($this->ID)) {
            return false;
        }

        $destroy = $this->delete("ID = :ID", "ID={$this->id}");
        return $destroy;
    }

    /**
     * 
     *
     * @return array|null
     */
    protected function safe(): ?array 
    {
        $safe = (array)$this->data;
        foreach($this->protected as $unset) {
            unset($safe[$unset]);
        }
        return $safe;
    }

    /**
     * Função responsável por filtrar um array de dados
     *
     * @param array $data
     * 
     * @return array|null
     */
    private function filter(array $data): ?array 
    {
        $filter = [];
        foreach($data as $key => $value) {
            $filter[$key] = (is_null($value) ? null : filter_var($value, FILTER_DEFAULT));
        }

        return $filter;
    }

    /**
     * Função responsável por definir se foi alimentado as colunas obrigatorias
     *
     * @return boolean
     */
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

