<?php

namespace App\Controllers;

use App\Models\PertanyaanModel;
use App\Models\PertanyaanOpsiModel;
use App\Models\OpsiModel;
use App\Models\UsersModel;
use App\Models\OPDModel;

class Pertanyaan extends BaseController
{
    protected $pertanyaanModel;

    public function __construct()
    {
        $this->pertanyaanModel = new PertanyaanModel();
        $this->pertanyaanopsiModel = new PertanyaanOpsiModel();
        $this->opsiModel = new OpsiModel();
        $this->usersModel = new UsersModel();
        $this->opdModel = new OPDModel();
    }

    //HALAMAN DETAIL PERTANYAAN SURVEY + LIST OPSI PERTANYAAN + TAMBAH OPSI PERTANYAAN + TAMBAH OPSI MASTER
    public function detail($pertanyaan_id, $survey_id)
    {
        $users_opd_kode = session()->get('users_opd_kode');
        $data = [
            'judul' => 'DETAIL PERTANYAAN SURVEY',
            'pertanyaan' => $this->pertanyaanModel->getPertanyaanSurveyDetail($pertanyaan_id)[0],
            'pertanyaanopsi' => $this->pertanyaanopsiModel->getOpsiPertanyaan($pertanyaan_id),
            'opd' => $this->opdModel->where(['opd_kode' => $users_opd_kode])->get()->getResultArray()[0],
            'validation' => \Config\Services::validation(),
            'opsi' => $this->opsiModel->getOpsi(),
            'pertanyaan_id' => $pertanyaan_id,
            'survey_id' => $survey_id,
            'users' => $this->users
        ];
        if (empty($data['pertanyaan'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Pertanyaan Survey dengan id $pertanyaan_id tidak ditemukan");
        }
        return view('Pertanyaan/DetailPertanyaan', $data);
    }

    //HALAMAN DETAIL PERTANYAAN SURVEY + LIST OPSI PERTANYAAN + TAMBAH OPSI PERTANYAAN + TAMBAH OPSI MASTER [SUPER ADMIN]
    public function detailsuper($pertanyaan_id, $survey_id, $opd_kode)
    {
        $data = [
            'judul' => 'DETAIL PERTANYAAN SURVEY',
            'pertanyaan' => $this->pertanyaanModel->getPertanyaanSurveyDetail($pertanyaan_id)[0],
            'pertanyaanopsi' => $this->pertanyaanopsiModel->getOpsiPertanyaan($pertanyaan_id),
            'opd' => $this->opdModel->where(['opd_kode' => $opd_kode])->get()->getResultArray()[0],
            'validation' => \Config\Services::validation(),
            'opsi' => $this->opsiModel->getOpsi(),
            'pertanyaan_id' => $pertanyaan_id,
            'survey_id' => $survey_id,
            'users' => $this->users
        ];
        if (empty($data['pertanyaan'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Pertanyaan Survey dengan id $pertanyaan_id tidak ditemukan");
        }
        return view('Pertanyaan/DetailPertanyaan', $data);
    }

    //METHOD TAMBAH PERTANYAAN SURVEY
    public function create($survey_id)
    {
        if (!$this->validate([
            'pertanyaan_nama' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Pertanyaan survey harus diisi'
                ]
            ]
        ])) {
            if (session()->get('users_role_id') == 1) {
                return redirect()->to("/survey/detailsuper/$survey_id/". $this->users['opd_kode'])->withInput();
            } else {
                return redirect()->to("/survey/detail/$survey_id")->withInput();
            }
        }

        $simpan = [
            'pertanyaan_id' => '',
            'pertanyaan_nama' => $this->request->getVar('pertanyaan_nama'),
            'pertanyaan_survey_id' => $this->request->getVar('pertanyaan_survey_id'),
            'created_by' => $this->request->getVar('created_by')
        ];

        $this->pertanyaanModel->insert($simpan);

        session()->setFlashdata('pesan', 'ditambah!');

        if (session()->get('users_role_id') == 1) {
            return redirect()->to("/survey/detailsuper/$survey_id/". $this->users['opd_kode']);
        } else {
            return redirect()->to("/survey/detail/$survey_id");
        }
    }

    //METHOD HAPUS PERTANYAAN SURVEY
    public function delete($pertanyaan_id, $survey_id)
    {
        $this->pertanyaanModel->where(['pertanyaan_id' => $pertanyaan_id])->delete();
        session()->setFlashdata('pesan', 'dihapus!');
        if (session()->get('users_role_id') == 1) {
            return redirect()->to("/survey/detailsuper/$survey_id/". $this->users['opd_kode']);
        } else {
            return redirect()->to("/survey/detail/$survey_id");
        }
    }

    //METHOD HALAMAN UBAH PERTANYAAN SURVEY
    public function edit($pertanyaan_id, $survey_id)
    {
        $users_opd_kode = session()->get('users_opd_kode');
        $data = [
            'judul' => 'Ubah Data Pertanyaan Survey',
            'pertanyaan' => $this->pertanyaanModel->where(['pertanyaan_id' => $pertanyaan_id])->first(),
            'validation' => \Config\Services::validation(),
            'survey_id' => $survey_id,
            'users' => $this->users,
            $users_opd_kode
        ];

        return view('/Pertanyaan/EditPertanyaan', $data);
    }

    //METHOD UBAH PERTANYAAN SURVEY
    public function update($pertanyaan_id, $survey_id)
    {
        $this->pertanyaanModel->where(['pertanyaan_id' => $pertanyaan_id])->first();

        if (!$this->validate([
            'pertanyaan_nama' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Pertanyaan survey harus diisi'
                ]
            ]
        ])) {
            return redirect()->to("/Pertanyaan/edit/"  . $this->request->getVar('pertanyaan_id') . "/$survey_id")->withInput();
        }

        $ubah = [
            'pertanyaan_nama' => $this->request->getVar('pertanyaan_nama'),
            'pertanyaan_survey_id' => $this->request->getVar('pertanyaan_survey_id'),
            'updated_by' => $this->request->getVar('updated_by')
        ];

        $this->pertanyaanModel->update($pertanyaan_id, $ubah);

        session()->setFlashdata('pesan', 'diubah!');

        if (session()->get('users_role_id') == 1) {
            return redirect()->to("/survey/detailsuper/$survey_id/". $this->users['opd_kode']);
        } else {
            return redirect()->to("/survey/detail/$survey_id");
        }
    }
}
