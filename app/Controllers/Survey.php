<?php

namespace App\Controllers;

use App\Models\SurveyModel;
use App\Models\PertanyaanModel;
use App\Models\UsersModel;
use App\Models\OPDModel;

class Survey extends BaseController
{
    protected $surveyModel;

    protected $helpers = ['custom'];

    public function __construct()
    {
        $this->surveyModel = new SurveyModel();
        $this->pertanyaanModel = new PertanyaanModel();
        $this->usersModel = new UsersModel();
        $this->opdModel = new OPDModel();
    }

    // METHOD LIST SURVEY
    public function index()
    {
        $data = [
            'judul' => 'DAFTAR SURVEY',
            'survey' => $this->surveyModel->getSurveyByUsers($this->users_opd_kode),
            'users' => $this->users
        ];
        return view('Survey/ListSurvey', $data);
    }

    // METHOD DETAIL SURVEY + FORM TAMBAH PERTANYAAN SURVEY + LIST PERTANYAAN SURVEY
    public function detail($survey_id)
    {
        $currentPage = $this->request->getVar('page_tr_pertanyaan') ? $this->request->getVar('page_tr_pertanyaan') : 1;
        $users_opd_kode = session()->get('users_opd_kode');
        $data = [
            'judul' => 'DETAIL SURVEY',
            'survey' => $this->surveyModel->getSurveyDetail($survey_id),
            'pertanyaan' => $this->pertanyaanModel->getPertanyaanSurvey($survey_id),
            'opd' => $this->opdModel->where(['opd_kode' => $users_opd_kode])->get()->getResultArray()[0],
            'validation' => \Config\Services::validation(),
            'survey_id' => $survey_id,
            'users' => $this->users,
            'currentPage' => $currentPage
        ];

        if (empty($data['survey'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Survey dengan id $survey_id tidak ditemukan");
        }

        return view('Survey/DetailSurvey', $data);
    }

    // METHOD DETAIL SURVEY + FORM TAMBAH PERTANYAAN SURVEY + LIST PERTANYAAN SURVEY [SUPER ADMIN]
    public function detailsuper($survey_id, $opd_kode)
    {
        $currentPage = $this->request->getVar('page_tr_pertanyaan') ? $this->request->getVar('page_tr_pertanyaan') : 1;

        $data = [
            'judul' => 'DETAIL SURVEY',
            'survey' => $this->surveyModel->getSurveyDetail($survey_id),
            'pertanyaan' => $this->pertanyaanModel->getPertanyaanSurvey($survey_id),
            'opd' => $this->opdModel->where(['opd_kode' => $opd_kode])->get()->getResultArray()[0],
            'validation' => \Config\Services::validation(),
            'survey_id' => $survey_id,
            'users' => $this->users,
            'currentPage' => $currentPage
        ];

        if (empty($data['survey'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Survey dengan id $survey_id tidak ditemukan");
        }
        return view('Survey/DetailSurvey', $data);
    }

    // METHOD HALAMAN TAMBAH SURVEY
    public function add()
    {
        $users_opd_kode = session()->get('users_opd_kode');
        $data = [
            'judul' => 'Tambah Data Survey',
            'validation' => \Config\Services::validation(),
            'users' => $this->users,
            $users_opd_kode
        ];
        return view('/Survey/AddSurvey', $data);
    }

    // METHOD TAMBAH SURVEY
    public function create()
    {
        if (!$this->validate([
            'survey_judul' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Judul Survey harus diisi'
                ]
            ],
            'survey_deskripsi' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Deskripsi survey Mohon diisi',
                ]
            ],
            'survey_tahun' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tahun Survey harus diisi',
                ]
            ]
        ])) {
            return redirect()->to("/survey/add")->withInput();
        }

        $simpan = [
            'survey_id' => '',
            'survey_judul' => $this->request->getVar('survey_judul'),
            'survey_deskripsi' => $this->request->getVar('survey_deskripsi'),
            'survey_tahun' => $this->request->getVar('survey_tahun'),
            'survey_status' => $this->request->getVar('survey_status'),
            'created_by' => $this->request->getVar('created_by')
        ];

        $this->surveyModel->insert($simpan);

        session()->setFlashdata('pesan', 'ditambah!');

        if (session()->get('users_role_id') == 1) {
            return redirect()->to("/opd/detail/" . $this->users['opd_kode']);
        } else {
            return redirect()->to("/survey");
        }
    }

    // METHOD HAPUS SURVEY
    public function delete($survey_id)
    {
        $this->surveyModel->where(['survey_id' => $survey_id])->delete();
        session()->setFlashdata('pesan', 'dihapus!');

        if (session()->get('users_role_id') == 1) {
            return redirect()->to("/opd/detail/" . $this->users['opd_kode']);
        } else {
            return redirect()->to("/survey");
        }
    }

    // METHOD HALAMAN UBAH SURVEY
    public function edit($survey_id)
    {
        $users_opd_kode = session()->get('users_opd_kode');
        $data = [
            'judul' => 'Ubah Data Survey',
            'survey' => $this->surveyModel->getSurveyDetail($survey_id),
            'validation' => \Config\Services::validation(),
            'users' => $this->users,
            $users_opd_kode
        ];

        return view('/Survey/EditSurvey', $data);
    }

    // METHOD UBAH SURVEY
    public function update($survey_id)
    {
        $this->surveyModel->where(['survey_id' => $survey_id])->first();
        if (!$this->validate([
            'survey_judul' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Judul Survey harus diisi'
                ]
            ],
            'survey_deskripsi' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Deskripsi survey Mohon diisi',
                ]
            ],
            'survey_tahun' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tahun Survey harus diisi',
                ]
            ],
            'survey_status' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Status Survey Mohon diisi'
                ]
            ]
        ])) {
            return redirect()->to("/survey/edit/" . $this->request->getVar('survey_id'))->withInput();
        }

        $ubah = [
            'survey_judul' => $this->request->getVar('survey_judul'),
            'survey_deskripsi' => $this->request->getVar('survey_deskripsi'),
            'survey_tahun' => $this->request->getVar('survey_tahun'),
            'survey_status' => $this->request->getVar('survey_status'),
            'updated_by' => $this->request->getVar('created_by')
        ];

        $this->surveyModel->update($survey_id, $ubah);

        session()->setFlashdata('pesan', 'diubah!');

        if (session()->get('users_role_id') == 1) {
            return redirect()->to("/opd/detail/" . $this->users['opd_kode']);
        } else {
            return redirect()->to("/survey");
        }
    }
}
