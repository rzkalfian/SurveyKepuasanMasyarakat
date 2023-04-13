<?php

namespace App\Controllers;

use App\Models\SurveyModel;
use App\Models\PertanyaanModel;
use App\Models\OpsiModel;
use App\Models\PertanyaanOpsiModel;
use App\Models\RespondenModel;
use App\Models\RPOJModel;
use App\Models\UsersModel;
use App\Models\OPDModel;


class Laporan extends BaseController
{
    public function __construct()
    {
        $this->surveyModel = new SurveyModel();
        $this->opsiModel = new OpsiModel();
        $this->pertanyaanModel = new PertanyaanModel();
        $this->pertanyaanopsiModel = new PertanyaanOpsiModel();
        $this->respondenModel = new RespondenModel();
        $this->rpojModel = new RPOJModel();
        $this->usersModel = new UsersModel();
        $this->opdModel = new OPDModel();
    }

    // METHOD HALAMAN LIST TAHUN LAPORAN 
    public function listTahun($opd_kode = null)
    {
        if (session()->get('users_role_id') == 1) {
            $users_opd_kode = $opd_kode;
        } else {
            $users_opd_kode = session()->get('users_opd_kode');
        }

        $data = [
            'judul' => 'Formulir Survey',
            'survey' => $this->surveyModel->getSurveyTahunByUsers($users_opd_kode),
            'users' => $this->users,
            'opd_kode' => $users_opd_kode
        ];
        return view('Laporan/ListTahun', $data);
    }

    // METHOD HALAMAN LIST SURVEY LAPORAN 
    public function listSurvey($opd_kode = null, $survey_tahun = null)
    {
        if (session()->get('users_role_id') == 1) {
            $users_opd_kode = $opd_kode;
        } else {
            $users_opd_kode = session()->get('users_opd_kode');
        }

        $data = [
            'tahun' => $survey_tahun,
            'survey' => $this->surveyModel->getListSurveyByTahun($users_opd_kode, $survey_tahun),
            'users' => $this->users,
            'opd_kode' => $users_opd_kode
        ];
        return view('Laporan/ListSurvey', $data);
    }

    // METHOD HALAMAN DETAIL LAPORAN 
    public function laporan($survey_id, $opd_kode = null)
    {
        $pertanyaan = $this->pertanyaanModel->getPertanyaanBySurveyID($survey_id);

        $totalRespon = [];
        foreach ($pertanyaan as $q) {
            $data = [
                'pertanyaan' => $q,
                'opsi' => $this->opsiModel->getOpsidanTotal($q['pertanyaan_id'])
            ];
            array_push($totalRespon, $data);
        }

        $data = [
            'judul' => 'Laporan Survey',
            'survey' => $this->surveyModel->getSurveyDetail($survey_id),
            'totalRespon' => $totalRespon,
            'users' => $this->users,
            'opd_kode' => $opd_kode
        ];
        return view('Laporan/Laporan', $data);
    }

    //METHOD HALAMAN LIST OPD PER TAHUN [SUPERADMIN]
    public function listOPD()
    {
        $data = [
            'users' => $this->users,
            'listopd' => $this->opdModel->select('opd_nama, opd_kode')->findAll(),
            'pager' => $this->opdModel->pager
        ];
        return view('Laporan/ListOPD', $data);
    }
}
