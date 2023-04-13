<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\OPDModel;

class Users extends BaseController
{
    public function __construct()
    {
        $this->usersModel = new UsersModel();
    }

    // METHOD HALAMAN TAMBAH USERS [SUPERADMIN]
    public function index()
    {
        $data = [
            'users' => $this->users
        ];
        return view('Folder/NamaFile', $data);
    }
}
