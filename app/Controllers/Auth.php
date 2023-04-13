<?php

namespace App\Controllers;

use App\Models\AuthModel;
use App\Models\UsersModel;

class Auth extends BaseController
{
    public function __construct()
    {
        $this->authModel = new AuthModel();
        $this->usersModel = new UsersModel();
    }

    //METHOD HALAMAN LOGIN
    public function login()
    {
        if (session('users_nip')) {
            return redirect()->to(site_url('/dashboard'));
        }
        $data = [
            'validation' => \Config\Services::validation()
        ];
        return view('Auth/Login', $data);
    }

    //METHOD VERIFIKASI LOGIN
    public function cekLogin()
    {
        if (!$this->validate([
            'users_nip' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'NIP Admin Mohon diisi'
                ]
            ],
            'users_password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Password harus di isi'
                ]
            ]
        ])) {
            session()->setFlashdata('pesan', 'Login Gagal, NIP dan Password tidak boleh kosong');
            return redirect()->to(site_url('Auth/login'));
        } else {
            //Jika Valid
            $post = $this->request->getPost();
            $query = $this->authModel->getWhere(['users_nip' => $post['users_nip']]);
            $users = $query->getRow();

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
                if ($users) {
                    if (password_verify($post['users_password'], $users->users_password)) {
                        //Jika datanya cocok
                        session()->set('log', true);
                        session()->set('users_nip', $users->users_nip);
                        session()->set('users_nama', $users->users_nama);
                        session()->set('users_email', $users->users_email);
                        session()->set('users_alamat', $users->users_alamat);
                        session()->set('users_image', $users->users_image);
                        session()->set('users_telp', $users->users_telp);
                        session()->set('users_opd_kode', $users->users_opd_kode);
                        session()->set('users_role_id', $users->users_role_id);
                        //login
                        // return redirect()->to(site_url("/dashboard/" . session()->get('users_nip')));
                        return redirect()->to(site_url("/dashboard"));
                    } else {
                        //Jika password tidak sama
                        session()->setFlashdata('pesan', 'Login Gagal, Cek NIP dan Password Anda');
                        return redirect()->to(site_url('/auth/login'));
                    }
                } else {
                    //Jika nip tidak cocok
                    session()->setFlashdata('pesan', 'Login Gagal, Cek NIP dan Password Anda');
                    return redirect()->to(site_url('/auth/login'));
                }
            } else {
                return redirect()->to(site_url('/auth/login'))->with('pesan', 'Validasi Captcha Gagal');
            }
        }
    }

    //METHOD LOG OUT
    public function logout()
    {
        session()->remove('log');
        session()->remove('users_nip');
        session()->remove('users_nama');
        session()->remove('users_email');
        session()->remove('users_alamat');
        session()->remove('users_image');
        session()->remove('users_telp');
        session()->remove('users_opd_kode');
        session()->remove('users_role_id');

        return redirect()->to(site_url('/'));
    }
}
