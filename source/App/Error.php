<?php 

namespace Source\App;

use Source\Core\Controller;

class Error extends Controller
{
    public function __construct() 
    {
        parent::__construct('web');
    }
    
    public function index()
    {
        $this->render('404');
    }
}