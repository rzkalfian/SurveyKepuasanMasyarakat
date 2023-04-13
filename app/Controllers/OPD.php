<?php

namespace App\Controllers;

use App\Models\OPDModel;
use App\Models\UsersModel;
use App\Models\SurveyModel;

class OPD extends BaseController
{
    protected $opdModel;

    public function __construct()
    {
        $this->opdModel = new OPDModel();
        $this->usersModel = new UsersModel();
        $this->surveyModel = new SurveyModel();
    }

    //PROFIL OPD [ADMIN]
    public function profil()
    {
        $data = [
            'opd' => $this->opdModel->getOPD(session()->get('users_opd_kode')),
            'users' => $this->users
        ];
        return view('OPD/ProfilOPD', $data);
    }

    // METHOD HALAMAN UBAH PROFIL OPD [ADMIN]
    public function editProfil()
    {
        $opd_kode = session()->get('users_opd_kode');
        $opd = $this->opdModel->getWhere(['opd_kode' => $opd_kode])->getResultArray();
        $data = [
            'judul' => 'Ubah Data OPD',
            'opd' => $opd[0],
            'users' => $this->users,
            'validation' => \Config\Services::validation()
        ];

        return view('OPD/EditProfilOPD', $data);
    }

    // METHOD UBAH PROFIL OPD [ADMIN]
    public function updateProfil()
    {
        $opd_kode = session()->get('users_opd_kode');

        $this->opdModel->where(['opd_kode' => $opd_kode])->first();

        if (!$this->validate([
            'opd_nama' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama OPD Mohon diisi'
                ]
            ],
            'opd_email' => [
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'Email OPD Mohon diisi',
                    'valid_email' => 'Yang Anda Masukkan Bukan Email'
                ]
            ],
            'opd_alamat' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Alamat OPD Mohon diisi'
                ]
            ],
            'opd_telp' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nomor Telepon OPD Mohon diisi'
                ]
            ],
            'opd_logo' => [
                'rules' => 'max_size[opd_logo, 2048]|is_image[opd_logo]|mime_in[opd_logo,image/jpg,image/png,image/jpeg]',
                'errors' => [
                    'max_size' => 'Ukuran logo OPD terlalu besar',
                    'is_image' => 'Yang anda pilih bukanlah logo OPD',
                    'mime_in' => 'Format logo OPD tidak sesuai'

                ]
            ]

        ])) {
            return redirect()->to("/opd/editprofil/" . $this->request->getVar('opd_kode'))->withInput();
        }

        $fileLogo = $this->request->getFile('opd_logo');

        //apabila tidak ada logo baru yang diupload
        if ($fileLogo->getError() == 4) {
            $namaLogo = $this->request->getVar('logoLama');
        } else {
            //generate nama logo random
            $namaLogo = $fileLogo->getRandomName();

            //pindahkan logo ke folder img/logoopd
            $fileLogo->move('img/logoopd', $namaLogo);

            /* Apabila file gambar yang lama ingin di hapus gunakan method di bawah ini*/
            unlink('img/logoopd/' . $this->request->getVar('logoLama'));
        }

        if ($namaLogo == 1) {
            $namaLogo = 'defaultLogo.png';
        }

        $ubah = [
            'opd_kode' => $this->request->getVar('opd_kode'),
            'opd_nama' => $this->request->getVar('opd_nama'),
            'opd_logo' => $namaLogo,
            'opd_email' => $this->request->getVar('opd_email'),
            'opd_alamat' => $this->request->getVar('opd_alamat'),
            'opd_telp' => $this->request->getVar('opd_telp')
        ];

        $this->opdModel->update($opd_kode, $ubah);

        session()->setFlashdata('pesan', 'diubah!');

        return redirect()->to('/opd/profil');
    }

    /* -------------------------------------------------------------SUPER ADMIN----------------------------------------------------- */

    // METHOD LIST OPD [SUPERADMIN]
    public function index()
    {
        $data = [
            'judul' => 'DAFTAR OPD KABUPATEN WONOGIRI',
            'opd' => $this->opdModel->getOPD(),
            'users' => $this->users
        ];

        return view('OPD/ListOPD', $data);
    }

    // METHOD DETAIL OPD [SUPERADMIN]
    public function detail($opd_kode)
    {
        $data = [
            'judul' => 'DETAIL OPD',
            'opd' => $this->opdModel->where(['opd_kode' => $opd_kode])->get()->getResultArray()[0],
            'useropd' => $this->usersModel->getUsersByOPDKode($opd_kode),
            'surveyopd' => $this->surveyModel->getSurveyByOPDKode($opd_kode),
            'users' => $this->users
        ];

        if (empty($data['opd'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("OPD dengan kode $opd_kode tidak ditemukan");
        }
        return view('OPD/DetailOPD', $data);
    }

    // METHOD HAPUS OPD [SUPERADMIN]
    public function delete($opd_kode)
    {
        $opd = $this->opdModel->find($opd_kode);
        if ($opd['opd_logo'] != 'defaultLogo.png') {

            unlink('img/logoopd/' . $opd['opd_logo']);
        }

        $this->opdModel->where(['opd_kode' => $opd_kode])->delete();
        session()->setFlashdata('pesan', 'dihapus!');
        return redirect()->to('/OPD');
    }

    // METHOD HALAMAN TAMBAH OPD [SUPERADMIN]
    public function add()
    {
        $data = [
            'judul' => 'Tambah Data OPD',
            'validation' => \Config\Services::validation(),
            'users' => $this->users
        ];
        return view('OPD/AddOPD', $data);
    }

    // METHOD TAMBAH OPD [SUPERADMIN]
    public function create()
    {
        if (!$this->validate([
            'opd_kode' => [
                'rules' => 'required|is_unique[ms_opd.opd_kode]',
                'errors' => [
                    'required' => 'Kode OPD Mohon diisi',
                    'is_unique' => 'kode OPD sudah terdaftar'
                ]
            ],
            'opd_nama' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama OPD Mohon diisi'
                ]
            ],
            'opd_email' => [
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'Email OPD Mohon diisi',
                    'valid_email' => 'Yang Anda Masukkan Bukan Email'
                ]
            ],
            'opd_alamat' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Alamat OPD Mohon diisi'
                ]
            ],
            'opd_telp' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nomor Telepon OPD Mohon diisi'
                ]
            ],
            'opd_logo' => [
                'rules' => 'max_size[opd_logo, 2048]|is_image[opd_logo]|mime_in[opd_logo,image/jpg,image/png,image/jpeg]',
                'errors' => [
                    'max_size' => 'Ukuran logo OPD terlalu besar',
                    'is_image' => 'Yang anda pilih bukanlah logo OPD',
                    'mime_in' => 'Format logo OPD tidak sesuai'

                ]
            ]

        ])) {
            return redirect()->to('/OPD/add')->withInput();
        }

        $fileLogo = $this->request->getFile('opd_logo');

        if ($fileLogo->getError() == 4) {
            $namaLogo = 'defaultLogo.png';
        } else {
            $namaLogo = $fileLogo->getRandomName();
            $fileLogo->move('img/logoopd', $namaLogo);
        }

        $simpan = [
            'opd_kode' => $this->request->getVar('opd_kode'),
            'opd_nama' => $this->request->getVar('opd_nama'),
            'opd_logo' => $namaLogo,
            'opd_email' => $this->request->getVar('opd_email'),
            'opd_alamat' => $this->request->getVar('opd_alamat'),
            'opd_telp' => $this->request->getVar('opd_telp'),
        ];

        $this->opdModel->insert($simpan);

        session()->setFlashdata('pesan', 'ditambahkan!');

        return redirect()->to('/OPD');
    }

    // METHOD HALAMAN UBAH OPD [SUPERADMIN]
    public function edit($opd_kode)
    {
        $opd = $this->opdModel->where(['opd_kode' => $opd_kode])->get()->getResultArray();
        $data = [
            'judul' => 'Ubah Data OPD',
            'opd' => $opd[0],
            'users' => $this->users,
            'validation' => \Config\Services::validation()
        ];

        return view("OPD/EditOPD", $data);
    }

    // METHOD UBAH OPD [SUPERADMIN]
    public function update($opd_kode)
    {
        $kodeLama = $this->opdModel->where(['opd_kode' => $opd_kode])->first();
        if ($kodeLama['opd_kode'] ==  $this->request->getVar('opd_kode')) {
            $rule_kode = 'required';
        } else {
            $rule_kode = 'required|is_unique[ms_opd.opd_kode]';
        }
        if (!$this->validate([
            'opd_kode' => [
                'rules' => $rule_kode,
                'errors' => [
                    'required' => 'Kode OPD Mohon diisi',
                    'is_unique' => 'kode OPD sudah terdaftar'
                ]
            ],
            'opd_nama' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama OPD Mohon diisi'
                ]
            ],
            'opd_email' => [
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'Email OPD Mohon diisi',
                    'valid_email' => 'Yang Anda Masukkan Bukan Email'
                ]
            ],
            'opd_alamat' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Alamat OPD Mohon diisi'
                ]
            ],
            'opd_telp' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nomor Telepon OPD Mohon diisi'
                ]
            ],
            'opd_logo' => [
                'rules' => 'max_size[opd_logo, 2048]|is_image[opd_logo]|mime_in[opd_logo,image/jpg,image/png,image/jpeg]',
                'errors' => [
                    'max_size' => 'Ukuran logo OPD terlalu besar',
                    'is_image' => 'Yang anda pilih bukanlah logo OPD',
                    'mime_in' => 'Format logo OPD tidak sesuai'

                ]
            ]

        ])) {
            return redirect()->to('/OPD/edit/' . $this->request->getVar('opd_kode'))->withInput();
        }

        $fileLogo = $this->request->getFile('opd_logo');

        //apabila tidak ada logo baru yang diupload
        if ($fileLogo->getError() == 4) {
            $namaLogo = $this->request->getVar('logoLama');
        } else {
            //generate nama logo random
            $namaLogo = $fileLogo->getRandomName();

            //pindahkan logo ke folder img/logoopd
            $fileLogo->move('img/logoopd', $namaLogo);

            /* Apabila file gambar yang lama ingin di hapus gunakan method di bawah ini*/
            unlink('img/logoopd/' . $this->request->getVar('logoLama'));
        }

        if ($namaLogo == 1) {
            $namaLogo = 'defaultLogo.png';
        }

        $ubah = [
            'opd_kode' => $this->request->getVar('opd_kode'),
            'opd_nama' => $this->request->getVar('opd_nama'),
            'opd_logo' => $namaLogo,
            'opd_email' => $this->request->getVar('opd_email'),
            'opd_alamat' => $this->request->getVar('opd_alamat'),
            'opd_telp' => $this->request->getVar('opd_telp')
        ];

        $this->opdModel->update($opd_kode, $ubah);

        session()->setFlashdata('pesan', 'diubah!');

        return redirect()->to('/OPD');
    }
}
