<?php 

namespace src\controller;

use \core\Controller;

class ErrorController extends Controller  
{
    public function index()
    {
        $this->render('404');
    }
}