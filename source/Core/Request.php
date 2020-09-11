<?php

namespace Source\Core;

class Request 
{
    /**
     * Função responsável por trazer a url do sistema
     *
     * @return string
     */
    public static function getUrl(): string 
    {
        $url = filter_input(INPUT_GET, 'request');
        $url = str_replace(CONF_BASE_DIR, '', $url);
        return '/' . $url;
    }

    /**
     * Função responsável por trazer o method em uso 
     *
     * @return string
     */
    public static function getMethod(): string 
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }
}