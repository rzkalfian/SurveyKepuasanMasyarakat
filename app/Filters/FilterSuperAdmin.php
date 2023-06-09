<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class FilterSuperAdmin implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Do something here
        if (session()->get('users_role_id') != 1) {
            session()->setFlashdata('pesan', 'Tidak dapat mengakses halaman tersebut!');
            return redirect()->to(site_url('/dashboard'));
            // throw new \CodeIgniter\Exceptions\PageNotFoundException("Maaf Anda Bukan Super Admin");
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
