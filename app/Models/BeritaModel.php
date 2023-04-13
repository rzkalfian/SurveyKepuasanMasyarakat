<?php

namespace App\Models;

use CodeIgniter\Model;

class BeritaModel extends Model
{
    protected $table      = 'tr_berita';
    protected $primaryKey = 'berita_id';
    protected $useTimestamps = true;
    protected $allowedFields = ['berita_judul', 'berita_isi', 'berita_gambar', 'created_by', 'updated_by'];

    //METHOD MENAMPILKAN LIST 3 BERITA TERAKHIR
    public function getBerita($berita_id = false)
    {
        if ($berita_id == false) {
            return $this
                // ->select("*, DATE_FORMAT(created_at, '%d %M %Y') AS tanggal")
                ->select("*, DATE(created_at) AS tanggal")
                ->join('ms_users', 'ms_users.users_nip=tr_berita.created_by')
                ->orderBy('created_at', 'DESC')
                ->limit(3)
                ->get()->getResultArray();
        }

        return $this->where(['berita_id' => $berita_id])->first();
    }

    //METHOD MENAMPILKAN SELURUH BERITA MULAI DARI YANG TERBARU
    public function getListBeritaLain()
    {
        return $this
            ->select("*, DATE(created_at) AS tanggal")
            ->join('ms_users', 'ms_users.users_nip=tr_berita.created_by')
            ->orderBy('created_at', 'DESC')
            ->get()->getResultArray();
    }

    //METHOD MENAMPILKAN DETAIL BERITA SISI RESPONDEN
    public function getDetailBerita($berita_id)
    {
        return
            $this->select("*, DATE(created_at) AS tanggal")
                ->join('ms_users', 'ms_users.users_nip=tr_berita.created_by')
                ->where('berita_id', $berita_id)
                ->get()->getResultArray()[0];
    }

    //METHOD HITUNG TOTAL BERITA BY OPD KODE
    public function getTotalBerita($users_opd_kode)
    {
        return
            $this->join('ms_users', 'tr_berita.created_by=ms_users.users_nip')
            ->where(['users_opd_kode' => $users_opd_kode])
            ->selectCount('berita_id')->get();
    }

    //METHOD UNTUK MEANMPILKAN DATA TERBARU JUDUL BERITA BY OPD_KODE
    public function getBeritaTerakhirByOPDKode($kode)
    {
        return
            $this
            ->select("*, DATE(created_at) AS tanggal")
            ->join('ms_users', 'ms_users.users_nip=tr_berita.created_by')
            ->where(['users_opd_kode' => $kode])
            ->orderBy('created_at', 'DESC')
            ->limit(1)
            ->get()->getResultArray();
    }

    //MENAMPILKAN 3 BERITA TERAKHIR BY OPD KODE
    public function get3BeritaByOPDKode($kode)
    {
        return
            $this
            ->select("berita_id,berita_judul, berita_isi, berita_gambar, users_nama, DATE(created_at) AS tanggal")
            ->join('ms_users', 'ms_users.users_nip=tr_berita.created_by')
            ->where(['users_opd_kode' => $kode])
            ->orderBy('created_at', 'DESC')
            ->limit(3)
            ->get()->getResultArray();
    }

    //MENAMPILKAN SELURUH BERITA BY OPD SISI RESPONDEN 
    public function getListBeritaLainByOPD($opd_kode)
    {
        return $this
            ->select("berita_id, berita_judul, berita_isi, berita_gambar, users_nama, DATE(created_at) AS tanggal")
            ->join('ms_users', 'ms_users.users_nip=tr_berita.created_by')
            ->where('users_opd_kode', $opd_kode)
            ->orderBy('created_at', 'DESC')
            ->get()->getResultArray();
    }

    //METHOD
    public function getBeritaByUser($users_opd_kode, $berita_id = null)
    {
        if ($berita_id == null) {
            return $this
                // ->select("*, DATE_FORMAT(created_at, '%d %M %Y') AS tanggal")
                ->select("*, DATE(created_at) AS tanggal")
                ->join('ms_users', 'ms_users.users_nip=tr_berita.created_by')
                ->where('users_opd_kode', $users_opd_kode)
                ->orderBy('created_at', 'ASC')
                ->get()
                ->getResultArray();
        } else {
            return $this
                ->select("*, DATE(created_at) AS tanggal")
                ->join('ms_users', 'ms_users.users_nip=tr_berita.created_by')
                ->where('users_opd_kode', $users_opd_kode)
                ->where('berita_id', $berita_id)
                ->first();
        }
    }
}
