<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $role    = strtolower((string) $session->get('role'));
        $allowed = array_map('strtolower', array_filter((array) $arguments));

        if ($allowed === []) {
            return null;
        }

        // Guru dan pengajar dianggap role yang sama.
        $normalized = $role === 'guru' ? 'pengajar' : $role;
        $allowed    = array_map(static fn ($item) => $item === 'guru' ? 'pengajar' : $item, $allowed);

        if (in_array($normalized, $allowed, true)) {
            return null;
        }

        if ($request->isAJAX()) {
            return service('response')->setStatusCode(403)->setJSON([
                'status'  => false,
                'message' => 'Anda tidak memiliki akses ke halaman ini.',
            ]);
        }

        $session->setFlashdata('error', 'Anda tidak memiliki akses ke halaman tersebut.');

        return redirect()->to(role_home($role));
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
