<?php 

namespace src\controller;

use \core\Controller;

class HomeController extends Controller 
{
    public function index()
    {
        $this->render('home', ['nome' => 'Boniaky']);
    }

    public function sobre()
    {
        $this->render('sobre');
    }

    public function sobreP($args)
    {
        print_r($args);
    }
}