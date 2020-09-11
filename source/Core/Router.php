<?php

namespace Source\Core;

use Source\Core\RouterBase;

class Router extends RouterBase
{
    public $routes;
    private $namespace;
    private $group;

    public function namespace(?string $namespace): Router
    {
        $this->namespace = $namespace;
        return $this;
    }

    public function group(?string $group = null): void 
    {
        if (!$group) {
            $this->group = '/';
        } else {
            $this->group = $group;
        }
    }

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