<?php

namespace App\Controllers;

use App\Models\UsersModel;

class Home extends BaseController
{
    public function index()
    {
        return view('LandingPage');
    }

    //METHOD HASHING PASSWORD BYCRIPT
    public function generate()
    {
        echo password_hash('admin', PASSWORD_BCRYPT);
    }
}
