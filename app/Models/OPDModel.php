<?php

namespace App\Models;

use CodeIgniter\Model;

class OPDModel extends Model
{
    protected $table      = 'ms_opd';
    protected $primaryKey = 'opd_kode';
    protected $allowedFields = ['opd_kode', 'opd_nama', 'opd_logo', 'opd_email', 'opd_alamat', 'opd_telp'];

    //METHOD LIST OPD
    public function getOPD($opd_kode = false)
    {
        if ($opd_kode == false) {
            return $this->orderBy('opd_kode')->findAll();
        }

        return $this->where(['opd_kode' => $opd_kode])->first();
    }

    //MENAMPILKAN TOTAL OPD KAB. WONOGIRI
    public function getTotalOPD()
    {
        return
            $this
            ->selectCount('opd_kode')
            ->get()->getResultArray();
    }
}
