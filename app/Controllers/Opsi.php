<?php

namespace App\Controllers;

use App\Models\OpsiModel;
use App\Models\UsersModel;

class Opsi extends BaseController
{

    public function __construct()
    {
        $this->OpsiModel = new OpsiModel();
        $this->usersModel = new UsersModel();
    }

    //METHOD LIST OPSI MASTER [SUPERADMIN]
    public function index()
    {
        $data = [
            'judul' => 'List Opsi Jawaban',
            'opsi' => $this->OpsiModel->getOpsi(),
            'users' => $this->users
        ];
        return view('Opsi/ListOpsi', $data);
    }

    //METHOD HALAMAN TAMBAH OPSI [SUPERADMIN]
    public function addOpsi()
    {
        // session();
        $data = [
            'judul' => 'Form Tambah Data opsi',
            'validation' => \Config\Services::validation(),
            'users' => $this->users
        ];

        return view('Opsi/AddOpsiMaster', $data);
    }

    //METHOD TAMBAH OPSI [SUPER ADMIN]
    public function createOpsi()
    {
        //validasi input
        if (!$this->validate([
            'opsi_nama' => [
                'rules' => 'required|is_unique[ms_opsi.opsi_nama]',
                'errors' => [
                    'required' => ' opsi harus diisi.',
                    'is_unique' => 'opsi sudah terdaftar.'
                ]
            ]
        ])) {
            return redirect()->to('/opsi/addOpsi')->withInput();
        }

        $this->OpsiModel->save([
            'opsi_nama' => ucwords(strtolower($this->request->getVar('opsi_nama'))),
            'opsi_id' => ''
        ]);

        session()->setFlashdata('pesan', 'Data Berhasil Ditambahkan.');

        return redirect()->to('/opsi');
    }

    //METHOD HAPUS OPSI MASTER [SUPERADMIN]
    public function delete($opsi_id)
    {
        $this->OpsiModel->delete($opsi_id);
        session()->setFlashdata('pesan', 'dihapus!');
        return redirect()->to('/opsi');
    }

    //METHOD HALAMAN UBAH OPSI MASTER [SUPERADMIN]
    public function edit($opsi_id)
    {
        $data = [
            'judul' => 'Form Ubah Data Opsi',
            'validation' => \Config\Services::validation(),
            'opsi' => $this->OpsiModel->getOpsi($opsi_id),
            'users' => $this->users
        ];

        return view('Opsi/EditOpsi', $data);
    }

    //METHOD UBAH OPSI MASTER [SUPERADMIN]
    public function update()
    {
        $opsi_id = $this->request->getVar('opsi_id');
        $opsiLama = $this->OpsiModel->where(['opsi_id' => $opsi_id])->first();
        if ($opsiLama['opsi_nama'] ==  $this->request->getVar('opsi_nama')) {
            $rule_opsi = 'required';
        } else {
            $rule_opsi = 'required|is_unique[ms_opsi.opsi_nama]';
        }
        if (!$this->validate([
            'opsi_nama' => [
                'rules' => $rule_opsi,
                'errors' => [
                    'required' => 'Nama Opsi Mohon diisi',
                    'is_unique' => 'Nama Opsi sudah terdaftar'
                ]
            ],
        ])) {
            return redirect()->to('/opsi/edit/' . $opsi_id)->withInput();
        }
        $this->OpsiModel->save([
            'opsi_id'  => $opsi_id,
            'opsi_nama' => $this->request->getVar('opsi_nama'),
        ]);
        session()->setFlashdata('pesan', 'diubah!');
        return redirect()->to('/opsi');
    }

    //METHOD HALAMAN TAMBAH OPSI MASTER [ADMIN]
    public function add($pertanyaan_id, $survey_id)
    {
        $data = [
            'judul' => 'Form Tambah Data Opsi',
            'validation' => \Config\Services::validation(),
            'pertanyaan_id' => $pertanyaan_id,
            'survey_id' => $survey_id,
            'users' => $this->users
        ];
        return view('Opsi/AddOpsi', $data);
    }

    //METHOD TAMBAH OPSI MASTER [ADMIN]
    public function create($pertanyaan_id, $survey_id)
    {
        if (!$this->validate([
            'opsi_nama' => [
                'rules' => 'required|is_unique[ms_opsi.opsi_nama]',
                'errors' => [
                    'required' => ' Opsi harus diisi',
                    'is_unique' => ' Opsi sudah terdaftar'
                ]
            ]
        ])) {
            $validation = \config\Services::validation();
            return redirect()->to("/opsi/add/$pertanyaan_id/$survey_id")->withInput()->with('validation', $validation);
        }

        $this->OpsiModel->save([
            'opsi_nama' => ucwords(strtolower($this->request->getVar('opsi_nama')))
        ]);
        session()->setFlashdata('pesan', 'ditambah!');
        return redirect()->to("/opsi/add/$pertanyaan_id/$survey_id");
    }
}
