<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\BeritaModel;
use App\Models\OPDModel;

class Berita extends BaseController
{
    public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->beritaModel = new BeritaModel();
        $this->opdModel = new OPDModel();
    }

    //METHOD MENAMPILKAN LIST BERITA
    public function index($opd_kode = null)
    {
        if (session()->get('users_role_id') == 1) {
            $users_opd_kode = $opd_kode;
        } else {
            $users_opd_kode = session()->get('users_opd_kode');
        }

        $data = [
            'users' => $this->users,
            'title' => 'Berita',
            'listberita' => $this->beritaModel->getBeritaByUser($users_opd_kode),
            'users_opd_kode' => $users_opd_kode
        ];
        return view('Berita/ListBerita', $data);
    }

    //METHOD DETAIL BERITA
    public function detail($berita_id, $opd_kode = null)
    {
        if (session()->get('users_role_id') == 1) {
            $users_opd_kode = $opd_kode;
        } else {
            $users_opd_kode = session()->get('users_opd_kode');
        }
        $data = [
            'users' => $this->users,
            'title' => 'Daftar Berita',
            'berita' => $this->beritaModel->getBeritaByUser($users_opd_kode, $berita_id)
        ];
        return view('Berita/DetailBerita', $data);
    }

    //METHOD HALAMAN TAMBAH BERITA
    public function add()
    {
        $data = [
            'users' => $this->users,
            'title' => 'Form Tambah Data Responden',
            'validation' => \Config\Services::validation()
        ];

        return view('Berita/AddBerita', $data);
    }

    //METHOD TAMBAH BERITA
    public function create()
    {
        if (!$this->validate([
            'berita_judul' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Judul berita harus diisi.',
                ]
            ],
            'berita_isi' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Isi berita harus diisi.',
                ]
            ],
            'berita_gambar' => [
                'rules' => 'uploaded[berita_gambar]|max_size[berita_gambar, 2048]|is_image[berita_gambar]|mime_in[berita_gambar,image/jpg,image/png,image/jpeg]',
                'errors' => [
                    'uploaded' => 'Gambar tumbnail harus diupload',
                    'max_size' => 'Ukuran logo OPD terlalu besar',
                    'is_image' => 'Yang anda pilih bukanlah logo OPD',
                    'mime_in' => 'Format logo OPD tidak sesuai'

                ]
            ]

        ])) {
            return redirect()->to('berita/add')->withInput();
        }
        // ambil gambar
        if ($this->request->getFile('berita_gambar') != '') {
            $fileGambar = $this->request->getFile('berita_gambar');
            $namaGambar = $fileGambar->getRandomName();
            $fileGambar->move('img/beritaimage', $namaGambar);
        } else {
            $namaGambar = null;
        }

        /* function to fit image */
        // $beritaGambar = \Config\Services::image();
        // $beritaGambar->withFile("./img/temp/$namaGambar");
        // $beritaGambar->fit("700", "350", "center");
        // $beritaGambar->save("./img/beritaimage/$namaGambar");
        // unlink('img/temp/' . $namaGambar);

        $data = [
            'berita_judul' => $this->request->getVar('berita_judul'),

            'berita_isi' => $this->request->getVar('berita_isi'),

            'created_by' => $this->request->getVar('created_by'),

            'berita_gambar' => $namaGambar
        ];
        $this->beritaModel->insert($data);

        session()->setFlashdata('pesan', 'Ditambahkan');

        if (session()->get('users_role_id') == 1) {
            return redirect()->to("/berita/index/" . $this->users['opd_kode']);
        } else {
            return redirect()->to("/berita");
        }
    }

    //METHOD LIST OPD [SUPER ADMIN]
    public function listOPD()
    {
        $data = [
            'listopd' => $this->opdModel->select('opd_nama, opd_kode')->findAll(),
            'pager' => $this->opdModel->pager,
            'users' => $this->users
        ];
        return view('Berita/ListOPD', $data);
    }

    //METHOD HAPUS BERITA
    public function delete($berita_id)
    {
        $berita = $this->beritaModel->find($berita_id);
        if ($berita['berita_gambar'] != 'defaultLogo.jpg') {

            unlink('img/beritaimage/' . $berita['berita_gambar']);
        }
        $this->beritaModel->where(['berita_id' => $berita_id])->delete();
        session()->setFlashdata('pesan', 'Dihapus');

        if (session()->get('users_role_id') == 1) {
            return redirect()->to("/berita/index/" . $this->users['opd_kode']);
        } else {
            return redirect()->to("/berita");
        }
    }

    //METHOD HALAMAN EDIT BERITA
    public function edit($berita_id)
    {
        $data = [
            'users' => $this->users,
            'title' => 'Form ubah isi',
            'validation' => \Config\Services::validation(),
            'berita' => $this->beritaModel->getBerita($berita_id)
        ];
        return view('Berita/EditBerita', $data);
    }

    //METHOD UBAH BERITA
    public function update($berita_id)
    {
        if (!$this->validate([
            'berita_judul' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Judul berita harus diisi.',
                ]
            ],
            'berita_isi' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Isi berita harus diisi.',
                ]
            ],
            'berita_gambar' => [
                'rules' => 'max_size[berita_gambar,2048]|is_image[berita_gambar]|mime_in[berita_gambar,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Ukuran gambar terlalu besar',
                    'is_image' => 'Ini bukan gambar',
                    'mime_in' => 'Ini bukan gambar',
                ]
            ],
        ])) {
            return redirect()->to('berita/edit/' . $this->request->getVar('berita_id'))->withInput();
        }

        $fileGambar = $this->request->getFile('berita_gambar');

        //cek gambar, apakah tetap gambar lama
        if ($fileGambar->getError() == 4) {
            $namaGambar = $this->request->getVar('gambarLama');
        } else {
            //generate nama file random
            $namaGambar = $fileGambar->getRandomName();
            //pindahkan gambar 
            $fileGambar->move('img/beritaimage', $namaGambar);
            //hapus file lama
            unlink('img/beritaimage/' . $this->request->getVar('gambarLama'));
        }
        $ubah = [
            'berita_id' => $berita_id,
            'berita_judul' => $this->request->getVar('berita_judul'),
            'berita_isi' => $this->request->getVar('berita_isi'),
            'updated_by' => $this->request->getVar('updated_by'),
            'berita_gambar' => $namaGambar
        ];
        $this->beritaModel->update($berita_id, $ubah);
        session()->setFlashdata('pesan', 'Diubah');

        if (session()->get('users_role_id') == 1) {
            return redirect()->to("/berita/index/" . $this->users['opd_kode']);
        } else {
            return redirect()->to("/berita");
        }
    }
}
