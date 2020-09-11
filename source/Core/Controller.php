<?php

namespace Source\Core;

use Source\Support\Message;

class Controller
{
    private $theme;
    protected $message;

    public function __construct(string $theme) {
        $this->theme = $theme;
        $this->message = new Message();
    }
   
    protected function redirect(string $url): void
    {
        header("Location: ".$this->getBaseUrl().$url);
        exit;
    }

    private function getBaseUrl(): string  
    {
        $base = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') ? 'https://' : 'http://';
        $base .= $_SERVER['SERVER_NAME'];
        if($_SERVER['SERVER_PORT'] != '80') {
            $base .= ':'.$_SERVER['SERVER_PORT'];
        }
        $base .= '/blog/public';
        
        return $base;
    }

    private function _render(string $folder, string $viewName, array $viewData = []): void
    {
        if(file_exists(__DIR__ . '/../../themes/'.$this->theme.'/views/'. $folder . '/' . $viewName . '.php')) {
            extract($viewData);
            $render = fn($vN, $vD = []) => $this->renderPartial($vN, $vD);
            $base = $this->getBaseUrl();
            require __DIR__ . '/../../themes/'. $this->theme .'/views/' . $folder . '/' . $viewName . '.php';
        }
    }

    private function renderPartial(string $viewName, array $viewData = []): void
    {
        $this->_render('partials', $viewName, $viewData);
    }

    public function render(string $viewName, array $viewData = []): void 
    {
        $this->_render('pages', $viewName, $viewData);
    }
}