<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $table = 'ms_users';
    protected $primaryKey = 'users_nip';
    protected $allowedFields = ['users_nip', 'users_nama', 'users_image', 'users_email', 'users_password', 'users_alamat', 'users_telp', 'users_opd_kode', 'users_role_id'];

    /**
     * Menampilkan seluruh data users(admin) apabila nip tidak ada
     * Menampilkan detail data users(admin) tertentu apabila nip ada
     */
    //METHOD DATA USERS (ADMIN)
    public function getUsers($users_nip = false)
    {
        if ($users_nip == false) {
            return $this->orderBy('users_nip')->findAll();
        }
        return
            $this
            ->join('ms_role', 'ms_users.users_role_id=ms_role.role_id')
            ->join('ms_opd', 'ms_users.users_opd_kode=ms_opd.opd_kode')
            ->where(['users_nip' => $users_nip])
            ->first();
    }

    //METHOD ...
    public function getUsersOPD()
    {
        return
            $this
            ->join('ms_opd', 'ms_users.users_opd_kode=ms_opd.opd_kode')
            ->get()->getResultArray();
    }

    public function getUsersDetail($users_nip)
    {
        return
            $this
            ->join('ms_role', 'ms_users.users_role_id=ms_role.role_id')
            ->where(['users_nip' => $users_nip])
            ->get()->getResultArray();
    }

    public function getUsersByOPDKode($opd_kode)
    {
        return
            $this
            ->join('ms_opd', 'ms_users.users_opd_kode=ms_opd.opd_kode')
            ->where(['opd_kode' => $opd_kode])
            ->get()->getResultArray();
    }
}
