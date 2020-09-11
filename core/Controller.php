<?php 

namespace core;

use \src\Config;

class Controller 
{
    protected function redirect($url)
    {
        header("Location: " . $this->getBaseUrl().$url);
        exit;
    }

    /**
     * Função responsável por retornar a url do projeto, validando se é https ou http e pegando seu server name e porta
     *
     * @return string
     */
    private function getBaseUrl(): string
    {
        $base = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') ? 'https://' : 'http://';
        $base .= $_SERVER['SERVER_NAME'];
        if ($_SERVER['SERVER_PORT'] != '80') {
            $base .= ':' . $_SERVER['SERVER_PORT'];
        }

        $base .= Config::BASE_DIR;

        return $base;
    }

    /**
     * 
     *
     * @param string $folder
     * @param string $viewName
     * @param array $viewdata
     * @return void
     */
    private function _render(string $folder, string $viewName, array $viewdata = []): void  
    {
        if (file_exists('../src/views'.$folder.'/'.$viewName.'.php')) {
            extract($viewData);
            $render = fn($vN, $vD = []) => $this->renderPartial($vN, $vD);
            $base = $this->getBaseUrl();
            require '../src/views/'.$folder.'/'.$viewName.'.php';
        }
    }

    /**
     * 
     *
     * @param string $viewName
     * @param array $viewData
     * @return void
     */
    private function renderPartial(string $viewName, array $viewData = []):void 
    {
        $this->_render('partials', $viewName, $viewData);
    }

    /**
     * Função responsável por renderizar a view chamando a função _render
     *
     * @param string $viewName
     * @param array $viewData
     * @return void
     */
    public function render(string $viewName, array $viewData = []) 
    {
        $this->_render('pages', $viewName, $viewData);
    }
}
