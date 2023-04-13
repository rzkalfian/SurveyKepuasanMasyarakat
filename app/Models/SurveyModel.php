<?php

namespace App\Models;

use CodeIgniter\Model;

class SurveyModel extends Model
{
    protected $table      = 'tr_survey';
    protected $primaryKey = 'survey_id';
    protected $useTimestamps = true;
    protected $allowedFields = ['survey_judul', 'survey_deskripsi', 'survey_tahun', 'survey_status', 'created_by', 'updated_by'];

    public function getDetailSurveyOPD($survey_id = null)
    {
        return
            $this
            ->select('survey_judul, survey_deskripsi, survey_tahun, survey_status, survey_id, opd_logo, opd_kode')
            ->join('ms_users', 'ms_users.users_nip=tr_survey.created_by')
            ->join('ms_opd', 'ms_opd.opd_kode=ms_users.users_opd_kode')
            ->where(['survey_id' => $survey_id])
            ->first();
    }

    // METHOD LIST SURVEY
    public function getSurveyByUsers($users_opd_kode = null)
    {
        if (session()->get('users_role_id') == 1) {
            return
                $this->orderBy('created_at')->get()->getResultArray();
        } else {
            return
                $this
                ->join('ms_users', 'tr_survey.created_by=ms_users.users_nip')
                ->where(['users_opd_kode' => $users_opd_kode])
                ->orderBy('created_at')
                ->get()->getResultArray();
        }
    }

    public function getSurveyByOPDKode($opd_kode)
    {
        return
            $this
            ->join('ms_users', 'tr_survey.created_by=ms_users.users_nip')
            ->join('ms_opd', 'ms_users.users_opd_kode=ms_opd.opd_kode')
            ->where(['opd_kode' => $opd_kode])
            ->get()->getResultArray();
    }

    //METHOD DETAIL SURVEY
    public function getSurveyDetail($survey_id)
    {
        return
            $this->where(['survey_id' => $survey_id])->first();
    }

    //METHOD HITUNG TOTAL SURVEY
    public function getTotalSurvey($users_opd_kode)
    {
        return
            $this->join('ms_users', 'tr_survey.created_by=ms_users.users_nip')
            ->where(['users_opd_kode' => $users_opd_kode])
            ->selectCount('survey_id')->get();
    }

    //METHOD
    public function getSurveyByOPDID($opd_kode = null)
    {
        return
            $this
            ->select('survey_judul, survey_id')
            ->join('ms_users', 'ms_users.users_nip=tr_survey.created_by')
            ->join('ms_opd', 'ms_opd.opd_kode=ms_users.users_opd_kode')
            ->where(['opd_kode' => $opd_kode])
            ->where(['survey_status' => 'Aktif'])
            ->get()->getResultArray();
    }

    // METHOD HALAMAN LIST LAPORAN 
    public function getSurveyTahunByUsers($users_opd_kode)
    {
        return
            $this
            ->select('survey_tahun')
            ->join('ms_users', 'tr_survey.created_by=ms_users.users_nip')
            ->groupBy('survey_tahun')
            ->where(['users_opd_kode' => $users_opd_kode])
            ->get()->getResultArray();
    }

    // METHOD HALAMAN LIST LAPORAN 
    public function getListSurveyByTahun($users_opd_kode = null, $survey_tahun = null)
    {
        return
            $this
            ->join('ms_users', 'tr_survey.created_by=ms_users.users_nip')
            ->where(['users_opd_kode' => $users_opd_kode])
            ->where(['survey_tahun' => $survey_tahun])
            ->get()->getResultArray();
    }
}
