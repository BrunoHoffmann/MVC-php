<?php

namespace Source\Core;

class Request 
{
    
    public static function getUrl(): string 
    {
        $url = filter_input(INPUT_GET, 'request');
        $url = str_replace('/blog/public', '', $url);
        return '/' . $url;
    }

    public static function getMethod(): string 
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }
}