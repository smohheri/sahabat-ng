<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('is_logged_in')) {
            if ($request->isAJAX()) {
                return service('response')->setStatusCode(401)->setJSON([
                    'status'  => false,
                    'message' => 'Sesi berakhir. Silakan login kembali.',
                ]);
            }

            $session->setFlashdata('error', 'Silakan login terlebih dahulu.');

            return redirect()->to(site_url('login'));
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
