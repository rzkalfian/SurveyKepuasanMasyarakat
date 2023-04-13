<?php

namespace App\Models;

use CodeIgniter\Model;

use function PHPUnit\Framework\returnSelf;

class RespondenModel extends Model
{
    protected $table = 'ms_responden';
    protected $primaryKey = 'responden_id';
    protected $allowedFields = ['responden_id', 'responden_email', 'responden_survey_id'];

    //METHOD HITUNG TOTAL RESPONDEN
    public function getTotalResponden($opd_kode)
    {
        return
            $this
            ->selectCount('responden_id')
            ->join('tr_respondensurvey', 'tr_respondensurvey.respondensurvey_responden_id=ms_responden.responden_id')
            ->join('tr_survey', 'tr_survey.survey_id=tr_respondensurvey.respondensurvey_survey_id')
            ->join('ms_users', 'ms_users.users_nip=tr_survey.created_by')
            ->where('users_opd_kode', $opd_kode)
            ->get();
    }

    public function getTotalRespondenBySurveyId($survey_id)
    {
        return
            $this
            ->select('responden_email')
            ->join('tr_respondensurvey', 'tr_respondensurvey.respondensurvey_responden_id=ms_responden.responden_id')
            ->where('respondensurvey_survey_id', $survey_id)
            ->get()->getResultArray();
    }

    public function getTotalRespondenPerTahun($opd_kode)
    {
        return
            $this
            ->select('survey_tahun as tahun_survey')
            ->join('tr_respondensurvey', 'tr_respondensurvey.respondensurvey_responden_id=ms_responden.responden_id')
            ->join('tr_survey', 'tr_survey.survey_id=tr_respondensurvey.respondensurvey_survey_id')
            ->join('ms_users', 'ms_users.users_nip=tr_survey.created_by')
            ->where('users_opd_kode', $opd_kode)
            ->groupBy('survey_tahun')
            ->selectCount('responden_id', 'total_respon')
            ->orderBy('survey_tahun', 'ASC')
            ->get()
            ->getResultArray();
    }
}
