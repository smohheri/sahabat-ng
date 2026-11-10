<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PengurusModel;

class Pengurus extends BaseController
{
    private PengurusModel $model;

    public function __construct()
    {
        $this->model = new PengurusModel();
    }

    public function index()
    {
        $search  = trim((string) $this->request->getGet('q'));
        $perPage = 15;
        $page    = max(1, (int) $this->request->getGet('page'));
        $builder = $this->model;

        if ($search !== '') {
            $builder = $builder->groupStart()
                ->like('nama_pengurus', $search)
                ->orLike('jabatan', $search)
                ->orLike('email', $search)
                ->orLike('no_hp', $search)
                ->groupEnd();
        }

        $total = $builder->countAllResults(false);
        $items = $builder->orderBy('nama_pengurus', 'ASC')->limit($perPage, ($page - 1) * $perPage)->findAll();

        return $this->render('admin/pengurus/index', [
            'title'     => 'Data Pengurus - ' . app_name(),
            'pageTitle' => 'Data Pengurus',
            'items'     => $items,
            'search'    => $search,
            'page'      => $page,
            'perPage'   => $perPage,
            'total'     => $total,
        ]);
    }

    public function create()
    {
        return $this->render('admin/pengurus/form', [
            'title'     => 'Tambah Pengurus - ' . app_name(),
            'pageTitle' => 'Tambah Pengurus',
            'item'      => null,
        ]);
    }

    public function edit(int $id)
    {
        $item = $this->model->find($id);

        if (! $item) {
            return redirect()->to(site_url('admin/pengurus'))->with('error', 'Data pengurus tidak ditemukan.');
        }

        return $this->render('admin/pengurus/form', [
            'title'     => 'Edit Pengurus - ' . app_name(),
            'pageTitle' => 'Edit Pengurus',
            'item'      => $item,
        ]);
    }

    public function store()
    {
        $data = $this->payload();

        if (! $this->validate($this->model->getValidationRules())) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $id = $this->model->insert($data, true);

        if (! $id) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data pengurus.');
        }

        $this->saveUploads((int) $id);
        log_activity('add_pengurus', 'Menambahkan pengurus: ' . $data['nama_pengurus']);

        return redirect()->to(site_url('admin/pengurus'))->with('success', 'Data pengurus berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $item = $this->model->find($id);

        if (! $item) {
            return redirect()->to(site_url('admin/pengurus'))->with('error', 'Data pengurus tidak ditemukan.');
        }

        $data = $this->payload();

        if (! $this->validate($this->model->getValidationRules())) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $this->model->update($id, $data);
        $this->saveUploads($id);
        log_activity('edit_pengurus', 'Memperbarui pengurus: ' . $data['nama_pengurus']);

        return redirect()->to(site_url('admin/pengurus'))->with('success', 'Data pengurus berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        $item = $this->model->find($id);

        if (! $item) {
            return redirect()->back()->with('error', 'Data pengurus tidak ditemukan.');
        }

        delete_upload('file_ktp', $item['file_ktp'] ?? null);
        delete_upload('foto_pengurus', $item['foto'] ?? null);
        $this->model->delete($id);
        log_activity('delete_pengurus', 'Menghapus pengurus: ' . $item['nama_pengurus']);

        return redirect()->back()->with('success', 'Data pengurus berhasil dihapus.');
    }

    public function upload_ktp(int $id)
    {
        $item = $this->model->find($id);

        if (! $item) {
            return redirect()->back()->with('error', 'Data pengurus tidak ditemukan.');
        }

        $name = store_upload($this->request->getFile('file'), 'file_ktp', 'ktp');

        if ($name === null) {
            return redirect()->back()->with('error', 'Berkas KTP tidak valid atau gagal diunggah.');
        }

        delete_upload('file_ktp', $item['file_ktp'] ?? null);
        $this->model->update($id, ['file_ktp' => $name]);
        log_activity('upload_ktp', 'Mengunggah KTP pengurus: ' . $item['nama_pengurus']);

        return redirect()->back()->with('success', 'KTP berhasil diunggah.');
    }

    public function view_ktp(int $id)
    {
        $item = $this->model->find($id);

        if (! $item || empty($item['file_ktp'])) {
            return redirect()->back()->with('error', 'File KTP belum tersedia.');
        }

        $path = upload_path('file_ktp') . DIRECTORY_SEPARATOR . basename($item['file_ktp']);

        if (! is_file($path)) {
            return redirect()->back()->with('error', 'File KTP tidak ditemukan di server.');
        }

        return $this->response
            ->setHeader('Content-Type', mime_content_type($path) ?: 'application/octet-stream')
            ->setHeader('Content-Disposition', 'inline; filename="' . basename($item['file_ktp']) . '"')
            ->setBody((string) file_get_contents($path));
    }

    private function payload(): array
    {
        return [
            'nama_pengurus' => trim((string) $this->request->getPost('nama_pengurus')),
            'jabatan'       => trim((string) $this->request->getPost('jabatan')) ?: null,
            'no_hp'         => trim((string) $this->request->getPost('no_hp')) ?: null,
            'email'         => trim((string) $this->request->getPost('email')) ?: null,
        ];
    }

    private function saveUploads(int $id): void
    {
        $ktp = store_upload($this->request->getFile('file_ktp'), 'file_ktp', 'ktp');

        if ($ktp !== null) {
            $this->model->update($id, ['file_ktp' => $ktp]);
        }

        $foto = store_upload($this->request->getFile('foto'), 'foto_pengurus', 'pengurus', ['jpg', 'jpeg', 'png', 'webp']);

        if ($foto !== null) {
            $this->model->update($id, ['foto' => $foto]);
        }
    }
}
