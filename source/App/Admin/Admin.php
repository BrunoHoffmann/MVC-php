<?php 

namespace Source\App\Admin;

use Source\Core\Controller;
use Source\Support\Message;

class Admin extends Controller
{
    public $message;

    public function __construct()
    {
        parent::__construct('admin');

        $this->message = new Message();
    }
}