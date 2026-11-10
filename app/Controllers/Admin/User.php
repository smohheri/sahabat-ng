<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AnakModel;
use App\Models\GuruModel;
use App\Models\PengurusModel;
use App\Models\RoleModel;
use App\Models\UserModel;

class User extends BaseController
{
    private UserModel $model;

    public function __construct()
    {
        $this->model = new UserModel();
    }

    public function index()
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        $search = trim((string) $this->request->getGet('q'));
        $role   = (string) $this->request->getGet('role');
        $rows   = $this->model->allWithRole();

        if ($search !== '') {
            $rows = array_values(array_filter($rows, static function (array $row) use ($search): bool {
                $haystack = strtolower(implode(' ', [
                    $row['nama'] ?? '', $row['username'] ?? '', $row['role_label'] ?? '',
                    $row['nama_anak'] ?? '', $row['nama_guru'] ?? '',
                ]));

                return str_contains($haystack, strtolower($search));
            }));
        }

        if ($role !== '') {
            $rows = array_values(array_filter($rows, static fn (array $row): bool => ($row['role'] ?? '') === $role));
        }

        $pagination = $this->paginate($rows);

        return $this->render('admin/user/index', [
            'title'     => 'Manajemen User - ' . app_name(),
            'pageTitle' => 'Manajemen User',
            'items'     => $pagination['items'],
            'search'    => $search,
            'role'      => $role,
            'roles'     => (new RoleModel())->options(),
            'page'      => $pagination['page'],
            'perPage'   => $pagination['perPage'],
            'total'     => $pagination['total'],
        ]);
    }

    public function create()
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        return $this->render('admin/user/form', [
            'title'     => 'Tambah User - ' . app_name(),
            'pageTitle' => 'Tambah User',
            'item'      => null,
            'options'   => $this->formOptions(),
        ]);
    }

    public function edit(int $id)
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        $item = $this->model->withRole($id);

        if (! $item) {
            return redirect()->to(site_url('admin/user'))->with('error', 'User tidak ditemukan.');
        }

        return $this->render('admin/user/form', [
            'title'     => 'Edit User - ' . app_name(),
            'pageTitle' => 'Edit User',
            'item'      => $item,
            'options'   => $this->formOptions(),
        ]);
    }

    public function store()
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        $data = $this->payload();

        if (! $this->validate($this->model->getValidationRules())) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        if (empty($data['password'])) {
            return redirect()->back()->withInput()->with('error', 'Password wajib diisi untuk user baru.');
        }

        if ($this->model->isUsernameTaken($data['username'])) {
            return redirect()->back()->withInput()->with('error', 'Username sudah digunakan.');
        }

        $this->model->saveUser($data);
        log_activity('add_user', 'Menambahkan user: ' . $data['nama']);

        return redirect()->to(site_url('admin/user'))->with('success', 'User berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        $item = $this->model->find($id);

        if (! $item) {
            return redirect()->to(site_url('admin/user'))->with('error', 'User tidak ditemukan.');
        }

        $data = $this->payload();

        if (! $this->validate($this->model->getValidationRules())) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        if ($this->model->isUsernameTaken($data['username'], $id)) {
            return redirect()->back()->withInput()->with('error', 'Username sudah digunakan.');
        }

        $this->model->saveUser($data, $id);
        log_activity('edit_user', 'Memperbarui user: ' . $data['nama']);

        return redirect()->to(site_url('admin/user'))->with('success', 'User berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        if ($id === (int) $this->session->get('id_user')) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $item = $this->model->find($id);

        if (! $item) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }

        $this->model->delete($id);
        log_activity('delete_user', 'Menghapus user: ' . $item['nama']);

        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }

    private function payload(): array
    {
        $roleName = (string) $this->request->getPost('role');
        $role     = (new RoleModel())->where('name', $roleName)->first();

        return [
            'nama'        => trim((string) $this->request->getPost('nama')),
            'username'    => trim((string) $this->request->getPost('username')),
            'password'    => (string) $this->request->getPost('password'),
            'id_role'     => $role ? (int) $role['id_role'] : 0,
            'id_anak'     => ((int) $this->request->getPost('id_anak')) ?: null,
            'id_guru'     => ((int) $this->request->getPost('id_guru')) ?: null,
            'id_pengurus' => ((int) $this->request->getPost('id_pengurus')) ?: null,
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
        ];
    }

    private function formOptions(): array
    {
        $anakOptions = [];

        foreach ((new AnakModel())->orderBy('nama_anak', 'ASC')->findAll() as $anak) {
            $anakOptions[(int) $anak['id_anak']] = $anak['nama_anak'];
        }

        return [
            'roles'     => (new RoleModel())->options(),
            'anak'      => $anakOptions,
            'guru'      => (new GuruModel())->options(),
            'pengurus'  => (new PengurusModel())->options(),
        ];
    }
}
