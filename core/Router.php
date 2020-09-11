<?php 

namespace core;

use \core\RouterBase;

class Router extends RouterBase 
{
    public $routes;

    /**
     * Função responsável por retornar o metodo get 
     *
     * @param string $endpoint
     * @param string $trigger
     * @return void
     */
    public function get(string $endpoint, string $trigger): void
    {
        $this->routes['get'][$endpoint] = $trigger;
    }

    /**
     * Função responsável por retornar o metodo post
     *
     * @param string $endpoint
     * @param string $trigger
     * @return void
     */
    public function post(string $endpoint, string $trigger): void
    {
        $this->routes['post'][$endpoint] = $trigger;
    }

    /**
     * Função responsável por retornar o metodo put
     *
     * @param string $endpoint
     * @param string $trigger
     * @return void
     */
    public function put(string $endpoint, string $trigger)
    {
        $this->routes['put'][$endpoint] = $trigger;
    }

    /**
     * Função responsável por retornar o metodo delete
     *
     * @param string $endpoint
     * @param string $trigger
     * @return void
     */
    public function delete(string $endpoint, string $trigger)
    {
        $this->routes['delete'][$endpoint] = $trigger;
    }
}