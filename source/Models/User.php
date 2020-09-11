<?php 

namespace Source\Models;

use Source\Core\Model;

class User extends Model
{
    public function __construct()
    {
        parent::__construct('USERS', ['ID'], ['FIRST_NAME', 'LAST_NAME', 'EMAIL', 'PASSWORD']);
    }

}