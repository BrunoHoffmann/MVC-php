<?php

namespace Source\Core;

class RouterBase 
{
    /**
     * Função responsável por executar o metodo de rotas
     *
     * @param array $routes
     * 
     * @return void
     */
    public function run(array $routes): void
    {
        $method = Request::getMethod();
        $url = Request::getUrl();

        // Define os itens padrão
        $controller = CONF_ERROR_CONTROLLER;
        $action = CONF_DEFAULT_ACTION;
        $args = [];

        if (isset($routes[$method])) {
            foreach ($routes[$method] as $route => $callback) {
                // indentifica os argumentos e substitui por regex
                $pattern = preg_replace('(\{[a-z0-9]{1,}\})', '([a-z0-9-]{1,})', $route);

                // Faz o match da URL 
                if (preg_match('#^('.$pattern.')*$#i', $url, $matches) === 1) {

                    array_shift($matches);
                    array_shift($matches);

                    // Pega todos os argumentos para associar
                    $itens = [];
                    if(preg_match_all('(\{[a-z0-9]{1,}\})', $route, $m)) {
                        $itens = preg_replace('(\{|\})', '', $m[0]);
                    }

                    // Faz a associação
                    $args = [];
                    foreach($matches as $key => $match) {
                        $args[$itens[$key]] = $match;
                    }

                    // Seta o controller\action
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