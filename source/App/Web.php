<?php 

namespace Source\App;

use Source\Core\Controller;

class Web extends Controller 
{
    public function __construct() 
    {
        parent::__construct('web');
    }
    public function index()
    {
        $this->render('index');
    }
}