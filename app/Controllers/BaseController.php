<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $session;
    protected $db;
    protected array $settings = [];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->helpers = ['app', 'form', 'url', 'text'];
        $this->session = service('session');
        $this->db      = db_connect();

        $settings = setting();

        if ($settings !== null) {
            $this->settings = (array) $settings;
        }
    }

    /**
     * Render view di dalam layout.
     */
    protected function render(string $view, array $data = [], string $layout = 'layouts/app')
    {
        $data['settings']    = $this->settings;
        $data['currentUser'] = $this->currentUser();
        $data['content']     = view($view, $data);

        return view($layout, $data);
    }

    protected function renderLanding(string $view, array $data = [])
    {
        $data['settings'] = $this->settings;
        $data['content']  = view($view, $data);

        return view('layouts/landing', $data);
    }

    /**
     * Potong array (hasil filter) menjadi halaman berdasarkan query string `page`.
     * Mengembalikan item halaman aktif beserta meta pagination.
     *
     * @param array<int, mixed> $items
     * @return array{items: array<int, mixed>, page: int, perPage: int, total: int}
     */
    protected function paginate(array $items, int $perPage = 15): array
    {
        $perPage = max(1, $perPage);
        $total   = count($items);
        $page    = max(1, (int) $this->request->getGet('page'));
        $offset  = ($page - 1) * $perPage;

        return [
            'items'   => array_slice($items, $offset, $perPage),
            'page'    => $page,
            'perPage' => $perPage,
            'total'   => $total,
        ];
    }

    protected function currentUser(): array
    {
        return [
            'id_user'  => (int) ($this->session->get('id_user') ?? 0),
            'nama'     => (string) ($this->session->get('nama') ?? ''),
            'username' => (string) ($this->session->get('username') ?? ''),
            'role'     => (string) ($this->session->get('role') ?? ''),
            'id_anak'  => (int) ($this->session->get('id_anak') ?? 0),
            'id_guru'  => (int) ($this->session->get('id_guru') ?? 0),
        ];
    }

    protected function isTeacher(): bool
    {
        return in_array(strtolower((string) $this->session->get('role')), ['guru', 'pengajar'], true);
    }

    protected function isAdmin(): bool
    {
        return in_array(strtolower((string) $this->session->get('role')), ['admin', 'petugas', 'dinas', 'operator'], true);
    }

    /**
     * Apakah user adalah admin murni (role = admin)?
     * Digunakan untuk aksi sensitif yang hanya boleh dilakukan administrator.
     */
    protected function isStrictAdmin(): bool
    {
        return strtolower((string) $this->session->get('role')) === 'admin';
    }

    /**
     * Penjaga aksi sensitif: hanya role `admin` yang boleh lanjut.
     * Mengembalikan response redirect (beserta flash error) bila ditolak,
     * atau null bila diizinkan.
     */
    protected function requireAdmin()
    {
        if ($this->isStrictAdmin()) {
            return null;
        }

        if ($this->request->isAJAX()) {
            return service('response')->setStatusCode(403)->setJSON([
                'status'  => false,
                'message' => 'Hanya administrator yang dapat melakukan aksi ini.',
            ]);
        }

        $this->session->setFlashdata('error', 'Hanya administrator yang dapat melakukan aksi ini.');

        return redirect()->back();
    }

    protected function flashSuccess(string $message): void
    {
        $this->session->setFlashdata('success', $message);
    }

    protected function flashError(string $message): void
    {
        $this->session->setFlashdata('error', $message);
    }

    protected function redirectBack(string $fallback = 'admin')
    {
        $referer = $this->request->getServer('HTTP_REFERER');

        return redirect()->to($referer ?: site_url($fallback));
    }
}
