<?php

namespace App\Controllers;

use App\Models\RespondenModel;
use App\Models\SurveyModel;
use App\Models\OPDModel;
use App\Models\PertanyaanModel;
use App\Models\OpsiModel;
use App\Models\PertanyaanOpsiModel;
use App\Models\UsersModel;
use App\Models\RPOJModel;
use App\Models\RespondenSurveyModel;
use App\Models\BeritaModel;
use \Mailjet\Resources;


class Responden extends BaseController
{
    protected $respondenModel;
    public function __construct()
    {
        $this->respondenModel = new RespondenModel();
        $this->surveyModel = new SurveyModel();
        $this->opdModel = new OPDModel();
        $this->opsiModel = new OpsiModel();
        $this->pertanyaanModel = new PertanyaanModel();
        $this->pertanyaanopsiModel = new PertanyaanOpsiModel();
        $this->usersModel = new UsersModel();
        $this->rpojModel = new RPOJModel();
        $this->respondensurveyModel = new RespondenSurveyModel();
        $this->beritaModel = new BeritaModel();
        $this->mj = new \Mailjet\Client(getenv('APIkey'), getenv('Secretkey'), true, ['version' => 'v3.1']);
    }

    // METHOD DAFTAR SEMUA OPD SISI RESPONDEN
    public function listOPD()
    {
        $data = [
            // 'listopd' => $this->opdModel->paginate(8, 'ms_opd'),
            'listopd' => $this->opdModel->findAll(),
            'pager' => $this->opdModel->pager
        ];
        return view('Responden/ListOPDResponden', $data);
    }

    //METHOD LIST DAFTAR SURVEY DARI RESPONDEN + 3 BERITA TERAKHIR OPD
    public function surveyResponden($opd_kode)
    {
        $data = [
            'surveyopd' => $this->surveyModel->getSurveyByOPDID($opd_kode),
            'opd' => $this->opdModel->getOPD($opd_kode),
            'berita' => $this->beritaModel->get3BeritaByOPDKode($opd_kode)
        ];
        return view('Responden/ListSurveyBeritaResponden', $data);
    }

    //METHOD HALAMAN FORM TAMBAH SURVEY
    public function addResponden($survey_id)
    {
        $data = [
            'validation' => \Config\Services::validation(),
            'survey' => $this->surveyModel->getDetailSurveyOPD($survey_id),
            'survey_id' => $survey_id,
        ];
        return view('Responden/AddResponden', $data);
    }

    //METHOD PENGISIAN EMAIL + PENGECEKAN EMAIL
    public function checkEmailResponden($survey_id)
    {
        $responden_email = $this->request->getPost('responden_email');
        $responden_survey_id = $this->request->getPost('responden_survey_id');
        $data_responden = $this->respondenModel->where('responden_email', $responden_email)->first();
        $data_respondensurvey = $this->respondensurveyModel->getRespondenSurvey($responden_email);

        if ($data_respondensurvey != false) {
            foreach ($data_respondensurvey as $respondensurvey) {
                if ($respondensurvey['respondensurvey_survey_id'] != $responden_survey_id) {
                    $ruleEmail = 'required';
                } else {
                    $ruleEmail = 'required|is_unique[ms_responden.responden_email]';
                }
            }
        } else {
            $ruleEmail = 'required';
        }
        //Validasi pengisian email
        if (!$this->validate([
            'responden_email' => [
                'rules' => $ruleEmail . '|valid_email',
                'errors' => [
                    'required' => 'Email harus diisi.',
                    'valid_email' => 'Yang Anda Inputkan bukan Email',
                    'is_unique' => 'Email sudah terdaftar'
                ]
            ]
        ])) {
            return redirect()->to(site_url('/responden/addresponden/' . $survey_id))->withInput();
        } else {

            $recaptchaResponse = trim($this->request->getVar('g-recaptcha-response'));

            $secret = env('RECAPTCHAV2_SECRET');

            $credential = array(
                'secret' => $secret,
                'response' => $recaptchaResponse
            );

            $verify = curl_init();
            curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
            curl_setopt($verify, CURLOPT_POST, true);
            curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($credential));
            curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($verify);

            $status = json_decode($response, true);

            if ($status['success']) {
                session()->set('responden_email', $responden_email);
                return redirect()->to(site_url("/responden/formSurveyResponden/$survey_id"));
            } else {
                session()->setFlashdata('pesan', 'Mohon Verifikasi Captcha dengan Benar!');
                return redirect()->to(site_url("/responden/addresponden/$survey_id"))->withInput();
            }
        }
    }

    //METHOD HALAMAN FORMULIR SURVEY RESPONDEN
    public function formSurveyResponden($survey_id)
    {
        if (session()->get('responden_email') == "") {
            return redirect()->to(site_url("/"));
        }

        $pertanyaan = $this->pertanyaanModel->getPertanyaanBySurveyID($survey_id);
        $pertanyaanOpsi = [];
        foreach ($pertanyaan as $q) {
            $pertanyaan_id = $q['pertanyaan_id'];
            $soal = [
                'pertanyaan' => $q,
                'opsi' => $this->opsiModel->getOpsiByPertanyaanID($q['pertanyaan_id']),
            ];
            array_push($pertanyaanOpsi, $soal);
        }
        $jumlah = $this->opsiModel->getJumlahOpsiByPertanyaanID($pertanyaan_id);

        $data = [
            'judul' => 'Daftar Pertanyaan Survey',
            'pertanyaanOpsi' => $pertanyaanOpsi,
            // 'respondensurvey_id' => $respondensurvey_id,
            'jumlah' => $jumlah,
            'survey' => $this->surveyModel->getDetailSurveyOPD($survey_id),
            'validation' => \Config\Services::validation()
        ];
        return view('Responden/FormSurveyResponden', $data);
    }

    //METHOD SIMPAN DATA RESPONDEN BERDASARKAN SURVEY YANG DIISI + PENGIRIMAN NOTIFIKASI KE EMAIL RESPONDEN
    public function createRespondenSurvey($survey_id)
    {
        $responden_email = session()->get('responden_email');
        $data_responden = $this->respondenModel->where('responden_email', $responden_email)->first();
        $opd = $this->surveyModel
            ->select('opd_kode, opd_nama')
            ->join('ms_users', 'ms_users.users_nip=tr_survey.created_by')
            ->join('ms_opd', 'ms_opd.opd_kode=ms_users.users_opd_kode')
            ->where('survey_id', $survey_id)
            ->get()->getResultArray()[0];
        $opd_kode = $opd['opd_kode'];
        $opd_nama = $opd['opd_nama'];

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "syaifullohumarxtkj3@gmail.com",
                        'Name' => "$opd_nama"
                    ],
                    'To' => [
                        [
                            'Email' => $responden_email,
                        ]
                    ],
                    'Subject' => "Terima kasih telah mengisi survey!",
                    'HTMLPart' => '<p>Saudara telah mengisi survey dari ' . $opd_nama . '. Kami sangat mengapresiasi ketersediaan Saudara dalam meluangkan waktu dan mengisi survey tersebut. Jawaban dari survey yang Saudara kirimkan sangat membantu kami untuk mengetahui bagaimana tingkat kepuasan masyarakat terhadap pelayanan kami dan kami akan berusaha untuk terus meningkatkan kualitas pelayanan kami.</p>

                    <p>Untuk mengisi survey dari Organisasi Perangkat Daerah lain, Saudara dapat mengunjungi link&nbsp;<a href="http://localhost:8080">di sini</a></p>.'
                ]
            ]
        ];

        $response = $this->mj->post(Resources::$Email, ['body' => $body]);
        if ($response->success()) {

            if ($data_responden == null) {
                $this->respondenModel->save([
                    'responden_email' => $responden_email
                ]);
            }

            $data_responden = $this->respondenModel->where('responden_email', $responden_email)->first();

            $this->respondensurveyModel->save([
                'respondensurvey_responden_id' => $data_responden['responden_id'],
                'respondensurvey_survey_id' => $survey_id
            ]);

            $data_responden = $this->respondenModel->where('responden_email', $responden_email)->first();
            $responden_id = $data_responden['responden_id'];

            $respondensurvey_id = $this->respondensurveyModel->select('respondensurvey_id')->where(['respondensurvey_responden_id' => $responden_id])->where(['respondensurvey_survey_id' => $survey_id])->first();
            $respondensurvey_id = $respondensurvey_id['respondensurvey_id'];

            $rpoj_pertanyaanopsi_id = $this->request->getVar('opsi');
            $rpoj_respondensurvey_id = $respondensurvey_id;

            $pertanyaan = $this->pertanyaanModel->getPertanyaanSurvey($survey_id);

            $jmlPertanyaan = count($pertanyaan);
            $jmlRespon = count($rpoj_pertanyaanopsi_id);

            if ($jmlPertanyaan != $jmlRespon) {
                for ($i = 1; $i <= $jmlPertanyaan; $i++) {
                    if (!$this->validate([
                        "opsi[$i]" => [
                            'rules' => 'required',
                            'errors' => [
                                'required' => 'Maaf, masih ada pertanyaan yang belum terjawab.'
                            ]
                        ]
                    ])) {
                        return redirect()->to(site_url("/responden/formsurveyresponden/" . $survey_id))->withInput();
                    }
                }
            }

            for ($i = 1; $i <= $jmlPertanyaan; $i++) {
                $this->rpojModel->insert([
                    'rpoj_respondensurvey_id' => $rpoj_respondensurvey_id,
                    'rpoj_pertanyaanopsi_id' => $rpoj_pertanyaanopsi_id[$i]
                ]);
            }

            session()->remove('responden_email');
            session()->remove('responden_id');
            session()->setFlashdata('pesan', 'Terima kasih telah mengisi survey dari kami');
            return redirect()->to(site_url("/responden/surveyresponden/$opd_kode"));
        } else {
            session()->setFlashdata('pesan', 'Email Yang Anda Masukkan Tidak Valid');
            return redirect()->to(site_url("/responden/addresponden/$survey_id"))->withInput();
        }
    }

    /* START BERITA RESPONDEN */
    //METHOD HALAMAN BERITA UTAMA DARI SISI RESPONDEN
    public function berita()
    {
        $opd = $this->opdModel->select('opd_kode')->get()->getResultArray();
        $beritaTerakhirOPD = [];
        foreach ($opd as $o) {
            $berita = [
                'berita' =>  $this->beritaModel->getBeritaTerakhirByOPDKode($o['opd_kode'])
            ];
            array_push($beritaTerakhirOPD, $berita);
        }

        $data = [
            'beritaTerakhirOPD' => $beritaTerakhirOPD,
            'berita' => $this->beritaModel->getListBeritaLain()
        ];
        return view('Responden/ListBeritaResponden', $data);
    }

    //METHOD DETAIL BERITA DARI SISI RESPONDEN
    public function detailBerita($berita_id)
    {
        $data = [
            'berita' => $this->beritaModel->getDetailBerita($berita_id)
        ];
        return view('Responden/DetailBeritaResponden', $data);
    }

    //METHOD LIST BERITA SURVEY PER OPD
    public function beritaSurvey($opd_kode)
    {
        $data = [
            'topBerita' => $this->beritaModel->get3BeritaByOPDKode($opd_kode),
            'berita' => $this->beritaModel->getListBeritaLainByOPD($opd_kode)
        ];
        return view('Responden/ListBeritaOPDResponden', $data);
    }
    /* END BERITA RESPONDEN */
}
