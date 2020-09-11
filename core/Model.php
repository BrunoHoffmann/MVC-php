<?php 

namespace core;

use \core\Database;
use \ClanCats\Hydrahon\Builder;
use \ClanCats\Hydrahon\Query\Sql\FetchableInterface;

class Model 
{
    protected static $_h;

    public function __construct() 
    {
        self::_checkH();
    }

    /**
     * Função responsável por retornar a conexão com o banco de dados e executar a query criada
     *
     * @return void
     */
    public static function _checkH()
    {
        if(self::$_h == null) {
            $connection = Database::getInstance();
            self::$_h = new Builder('mysql', function($query, $queryString, $queryParameters) use($connection) {
                $statement = $connection->prepare($queryString);
                $statement->execute($queryParameters);

                if ($query instanceof FetchableInterface)
                {
                    return $statement->fetchAll(\PDO::FETCH_ASSOC);
                }
            });
        }

        self::$_h = self::$_h->table(self::getTableName());
    }

    /**
     * Função responsável por pegar a tabela da query e retornado ela em letras minusculas
     *
     * @return string
     */
    public static function getTableName(): string
    {
        $className = explode('\\', get_called_class());
        $className = end($className);
        return strtolower($className).'s';
    }

    /**
     * Função responsável por montar o select da query
     *
     * @param array $fields
     * @return object
     */
    public static function select(array $fields = []): object
    {
        self::_checkH();
        return self::$_h->select($fields);
    }

    /**
     * Função responsável por fazer a inserção no banco de dados
     *
     * @param array $fields
     * @return object
     */
    public static function insert(array $fields = []): object
    {
        self::_checkH();
        return self::$_h->insert($fields);
    }

    /**
     * Função responsável por fazer o update no banco de dados
     *
     * @param array $fields
     * @return object
     */
    public static function update(array $fields = []): object
    {
        self::_checkH();
        return self::$_h->update($fields);
    }

    /**
     * Função responsável por realizar o delete na query
     *
     * @return object
     */
    public static function delete(): object
    {
        self::_checkH();
        return self::$_h->delete();
    }
}