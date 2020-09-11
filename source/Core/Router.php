<?php

namespace Source\Core;

use Source\Core\RouterBase;

class Router extends RouterBase
{
    public $routes;
    private $namespace;
    private $group;

    /**
     * Função responsável por trazer o namespace escolhido ou null
     *
     * @param null|string $namespace
     * 
     * @return Router
     */
    public function namespace(?string $namespace): Router
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * Função responsável por definir um grupo de rotas
     *
     * @param string|null $group
     * 
     * @return void
     */
    public function group(?string $group = null): void 
    {
        if (!$group) {
            $this->group = '/';
        } else {
            $this->group = $group;
        }
    }

    /**
     * Função responsável por executar o metodo get
     *
     * @param string $endpoint
     * @param string $trigger
     * 
     * @return void
     */
    public function get(string $endpoint, string $trigger): void
    {
        if ($endpoint == '/') {
            $this->group = '';
        }

        if (!$this->namespace) { 
            $this->routes['get'][$this->group . $endpoint] = $trigger;
        } else {
            $this->routes['get'][$this->group . $endpoint] = $this->namespace . '\\' . $trigger;
        }
            
    }

    /**
     * Função responsável por executar o metodo post
     *
     * @param string $endpoint
     * @param string $trigger
     * 
     * @return void
     */
    public function post(string $endpoint, string $trigger): void
    {
        if ($endpoint == '/') {
            $this->group = '';
        }

        if (!$this->namespace) { 
            $this->routes['post'][$this->group . $endpoint] = $trigger;
        } else {
            $this->routes['post'][$this->group . $endpoint] = $this->namespace . '\\' . $trigger;
        }
    }

    /**
     * Função responsável por executar o metodo put
     *
     * @param string $endpoint
     * @param string $trigger
     * 
     * @return void
     */
    public function put(string $endpoint, string $trigger): void
    {   
        if ($endpoint == '/') {
            $this->group = '';
        }

        if (!$this->namespace) { 
            $this->routes['put'][$this->group . $endpoint] = $trigger;
        } else {
            $this->routes['put'][$this->group . $endpoint] = $this->namespace . '\\' . $trigger;
        }
    }

    /**
     * Função responsável por executar o metodo delete
     *
     * @param string $endpoint
     * @param string $trigger
     * 
     * @return void
     */
    public function delete(string $endpoint, string $trigger): void
    {
        if ($endpoint == '/') {
            $this->group = '';
        }
        
        if (!$this->namespace) { 
            $this->routes['delete'][$this->group . $endpoint] = $trigger;
        } else {
            $this->routes['delete'][$this->group . $endpoint] = $this->namespace . '\\' . $trigger;
        }
    }
}