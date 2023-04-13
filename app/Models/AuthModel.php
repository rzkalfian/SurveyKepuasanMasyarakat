<?php

namespace App\Models;

use CodeIgniter\Model;

class AuthModel extends Model
{
    protected $table = 'ms_users';
    protected $primaryKey = 'users_nip';
    protected $allowedFields = ['users_nip', 'users_nama', 'users_image', 'users_email', 'users_password', 'users_alamat', 'users_telp', 'users_opd_kode', 'users_role_id'];
}
