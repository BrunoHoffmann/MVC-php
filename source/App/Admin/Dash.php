<?php 

namespace Source\App\Admin;

use Source\App\Admin\Admin;

class Dash extends Admin
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->render('dash');
    }
}