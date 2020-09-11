<?php

namespace Source\Core;

class RouterBase 
{
    public function run(array $routes): void
    {
        $method = Request::getMethod();
        $url = Request::getUrl();

        // Define os itens padrão
        $controller = 'Error';
        $action = 'index';
        $args = [];

        if (isset($routes[$method])) {
            foreach ($routes[$method] as $route => $callback) {
                $pattern = preg_replace('(\{[a-z0-9]{1,}\})', '([a-z0-9-]{1,})', $route);

                if (preg_match('#^('.$pattern.')*$#i', $url, $matches) === 1) {

                    array_shift($matches);
                    array_shift($matches);

                    $itens = [];
                    if(preg_match_all('(\{[a-z0-9]{1,}\})', $route, $m)) {
                        $itens = preg_replace('(\{|\})', '', $m[0]);
                    }

                    $args = [];
                    foreach($matches as $key => $match) {
                        $args[$itens[$key]] = $match;
                    }

                    $callbackSplit = explode('@', $callback);
                    $controller = $callbackSplit[0];
                    if (isset($callbackSplit[1])) {
                        $action = $callbackSplit[1];
                    }

                    break;
                }   
            }
        }

        $controller = "Source\App\\$controller";
        $definedController = new $controller();

        $definedController->$action($args);
    }
}