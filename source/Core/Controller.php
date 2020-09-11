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
   
    /**
     * Função responsável por fazer o redirecionamento da pagina
     *
     * @param string $url
     * @return void
     */
    protected function redirect(string $url): void
    {
        header("Location: ".$this->getBaseUrl().$url);
        exit;
    }

    /**
     * Função responsável por trazer a url 
     *
     * @return string
     */
    private function getBaseUrl(): string  
    {
        $base = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') ? 'https://' : 'http://';
        $base .= $_SERVER['SERVER_NAME'];
        if($_SERVER['SERVER_PORT'] != '80') {
            $base .= ':'.$_SERVER['SERVER_PORT'];
        }
        $base .= CONF_BASE_DIR;
        
        return $base;
    }

    /**
     * Função responsável por fazer a renderização de um arquivo na tela
     *
     * @param string $folder
     * @param string $viewName
     * @param array $viewData
     * 
     * @return void
     */
    private function _render(string $folder, string $viewName, array $viewData = []): void
    {
        if(file_exists(__DIR__ . '/../../themes/'.$this->theme.'/views/'. $folder . '/' . $viewName . '.' . CONF_VIEW_EXT)) {
            extract($viewData);
            $render = fn($vN, $vD = []) => $this->renderPartial($vN, $vD);
            $base = $this->getBaseUrl();
            require __DIR__ . '/../../themes/'. $this->theme .'/views/' . $folder . '/' . $viewName . '.' . CONF_VIEW_EXT;
        }
    }

    /**
     * Função responsável por chamar o _render para a partials
     *
     * @param string $viewName
     * @param array $viewData
     * 
     * @return void
     */
    private function renderPartial(string $viewName, array $viewData = []): void
    {
        $this->_render('partials', $viewName, $viewData);
    }

    /**
     * Função responsável por fazer o render da view chamada
     *
     * @param string $viewName
     * @param array $viewData
     * 
     * @return void
     */
    public function render(string $viewName, array $viewData = []): void 
    {
        $this->_render('pages', $viewName, $viewData);
    }
}