<?php 

namespace core;

use src\Config;

class Request 
{
    /**
     * Função responsável por trazer a url filtrada e correta
     *
     * @return string
     */
    public static function getUrl(): string
    {
        $url = filter_input(INPUT_GET, 'request');
        $url = str_replace(Config::BASE_DIR, '', $url);
        return '/' . $url;
    }

    /**
     * Função responável por retornar o method utilizado podendo ser GET, POST etc
     *
     * @return string
     */
    public static function getMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }
}