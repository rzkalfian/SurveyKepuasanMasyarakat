<?php

namespace App\Controllers;

use App\Models\OPDModel;
use App\Models\OpsiModel;
use App\Models\RPOJModel;
use App\Models\UsersModel;
use App\Models\SurveyModel;
use App\Models\RespondenModel;
use App\Models\PertanyaanModel;
use App\Models\PertanyaanOpsiModel;

class Grafik extends BaseController
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

    //METHOD TAMPILAN HALAMAN GRAFIK
    public function grafik($survey_id)
    {
        $pertanyaan = $this->pertanyaanModel->getPertanyaanBySurveyID($survey_id);

        $total_pertanyaan = count($pertanyaan);

        $responden = $this->respondenModel->getTotalRespondenBySurveyId($survey_id);

        $total_responden = count($responden);

        $dataGrafiks = [];
        foreach ($pertanyaan as $q) {
            $data = [
                'pertanyaan' => $q,
                'total' => $this->opsiModel->getOpsidanTotal($q['pertanyaan_id'])
            ];
            array_push($dataGrafiks, $data);
        }

        $survey = $this->surveyModel->getDetailSurveyOPD($survey_id);

        $data = [
            'judul' => 'Grafik Survey',
            'users' => $this->users,
            'totalpertanyaan' => $total_pertanyaan,
            'totalresponden' => $total_responden,
            'dataGrafiks' => $dataGrafiks,
            'survey' => $survey
        ];

        return view('Grafik/Grafik', $data);
    }
}
