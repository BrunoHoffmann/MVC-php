<?php

namespace Source\Core;

class Connect 
{
    private static $instance;

    public static function getInstance(): ?\PDO 
    {
        if (empty(self::$instance)) {
            try {
                self::$instance = new \PDO(
                      CONF_DB_DRIVER . ':host=' . CONF_DB_HOST . ';dbname=' . CONF_DB_NAME
                    , CONF_DB_USER
                    , CONF_DB_PASS 
                );

            } catch (\PDOException $exception) {
                echo '<pre>';
                print_r($exception);
                echo '</pre>';
            }
        }

        return self::$instance;
    }
}