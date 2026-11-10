<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AnakModel;
use App\Models\RoleModel;
use App\Models\UserModel;

class Anak extends BaseController
{
    private AnakModel $anakModel;

    public function __construct()
    {
        $this->anakModel = new AnakModel();
    }

    public function anak_aktif()
    {
        return $this->index('Aktif');
    }

    public function anak_nonaktif()
    {
        return $this->index(['Nonaktif', 'Alumni']);
    }

    private function index(string|array $status)
    {
        $filters = [
            'status_anak'    => $status,
            'search'         => trim((string) $this->request->getGet('q')),
            'jenis_kelamin'  => (string) $this->request->getGet('jenis_kelamin'),
            'pendidikan'     => (string) $this->request->getGet('pendidikan'),
            'kategori'       => (string) $this->request->getGet('kategori'),
            'sort'           => (string) ($this->request->getGet('sort') ?: 'nama_anak'),
            'dir'            => (string) ($this->request->getGet('dir') ?: 'ASC'),
        ];

        $perPage = 15;
        $page    = max(1, (int) $this->request->getGet('page'));
        $total   = $this->anakModel->countList($filters);
        $items   = $this->anakModel->getList($filters, $perPage, ($page - 1) * $perPage);

        $isActive = $status === 'Aktif';

        return $this->render('admin/anak/index', [
            'title'         => ($isActive ? 'Data Anak Aktif' : 'Data Anak Nonaktif') . ' - ' . app_name(),
            'pageTitle'     => $isActive ? 'Data Anak Aktif' : 'Data Anak Nonaktif',
            'items'         => $items,
            'filters'       => $filters,
            'page'          => $page,
            'perPage'       => $perPage,
            'total'         => $total,
            'statusMode'    => $isActive ? 'aktif' : 'nonaktif',
            'options'       => $this->formOptions(),
            'exportColumns' => anak_export_columns(),
        ]);
    }

    public function edit(int $id)
    {
        $anak = $this->anakModel->byId($id);

        if (! $anak) {
            return redirect()->to(site_url('admin/anak_aktif'))->with('error', 'Data anak tidak ditemukan.');
        }

        return $this->render('admin/anak/form', [
            'title'     => 'Edit Data Anak - ' . app_name(),
            'pageTitle' => 'Edit Data Anak',
            'anak'      => $anak,
            'options'   => $this->formOptions(),
        ]);
    }

    public function create()
    {
        return $this->render('admin/anak/form', [
            'title'     => 'Tambah Data Anak - ' . app_name(),
            'pageTitle' => 'Tambah Data Anak',
            'anak'      => null,
            'options'   => $this->formOptions(),
        ]);
    }

    public function store()
    {
        $data = $this->payload();

        if (! $this->validate($this->anakModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        if (empty($data['no_registrasi'])) {
            $data['no_registrasi'] = $this->anakModel->generateNoRegistrasi();
        }

        $id = $this->anakModel->insert($data, true);

        if (! $id) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data anak.');
        }

        $this->handleUploads((int) $id, $data);
        log_activity('add_anak', 'Menambahkan data anak: ' . $data['nama_anak']);

        return redirect()->to(site_url('admin/anak_aktif'))->with('success', 'Data anak berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $anak = $this->anakModel->find($id);

        if (! $anak) {
            return redirect()->to(site_url('admin/anak_aktif'))->with('error', 'Data anak tidak ditemukan.');
        }

        $data = $this->payload();

        if (! $this->validate($this->anakModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        if (! $this->anakModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data anak.');
        }

        $this->handleUploads($id, $data);
        log_activity('edit_anak', 'Memperbarui data anak: ' . $data['nama_anak']);

        return redirect()->to(site_url('admin/anak_aktif'))->with('success', 'Data anak berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        $anak = $this->anakModel->find($id);

        if (! $anak) {
            return redirect()->back()->with('error', 'Data anak tidak ditemukan.');
        }

        foreach (['file_kk' => 'file_kk', 'file_akta' => 'file_akta', 'file_pendukung' => 'file_pendukung', 'foto' => 'foto_anak'] as $column => $folder) {
            delete_upload($folder, $anak[$column] ?? null);
        }

        $this->anakModel->delete($id);
        log_activity('delete_anak', 'Menghapus data anak: ' . $anak['nama_anak']);

        return redirect()->back()->with('success', 'Data anak berhasil dihapus.');
    }

    public function upload_kk(int $id)
    {
        return $this->uploadDocument($id, 'file_kk', 'file_kk', 'kk', 'upload_kk');
    }

    public function upload_akta(int $id)
    {
        return $this->uploadDocument($id, 'file_akta', 'file_akta', 'akta', 'upload_akta');
    }

    public function upload_pendukung(int $id)
    {
        return $this->uploadDocument($id, 'file_pendukung', 'file_pendukung', 'pendukung', 'upload_pendukung');
    }

    public function upload_foto(int $id)
    {
        return $this->uploadDocument($id, 'foto', 'foto_anak', 'foto', 'upload_foto', ['jpg', 'jpeg', 'png', 'webp']);
    }

    private function uploadDocument(int $id, string $column, string $folder, string $prefix, string $activity, array $allowed = ['jpg', 'jpeg', 'png', 'webp', 'pdf'])
    {
        $anak = $this->anakModel->find($id);

        if (! $anak) {
            return redirect()->back()->with('error', 'Data anak tidak ditemukan.');
        }

        $file = $this->request->getFile('file');
        $name = store_upload($file, $folder, $prefix, $allowed);

        if ($name === null) {
            return redirect()->back()->with('error', 'Berkas tidak valid atau gagal diunggah. Format: ' . implode(', ', $allowed));
        }

        delete_upload($folder, $anak[$column] ?? null);
        $this->anakModel->update($id, [$column => $name]);
        log_activity($activity, 'Mengunggah ' . $prefix . ' untuk ' . $anak['nama_anak']);

        return redirect()->back()->with('success', 'Berkas berhasil diunggah.');
    }

    public function view_dokumen(int $id, string $jenis)
    {
        $anak = $this->anakModel->find($id);

        if (! $anak) {
            return redirect()->back()->with('error', 'Data anak tidak ditemukan.');
        }

        $map = [
            'kk'        => ['file_kk', 'file_kk'],
            'akta'      => ['file_akta', 'file_akta'],
            'pendukung' => ['file_pendukung', 'file_pendukung'],
            'foto'      => ['foto', 'foto_anak'],
        ];

        if (! isset($map[$jenis])) {
            return redirect()->back()->with('error', 'Jenis dokumen tidak dikenal.');
        }

        [$column, $folder] = $map[$jenis];
        $filename = $anak[$column] ?? null;

        if (empty($filename)) {
            return redirect()->back()->with('error', 'Dokumen belum tersedia.');
        }

        $path = upload_path($folder) . DIRECTORY_SEPARATOR . basename($filename);

        if (! is_file($path)) {
            return redirect()->back()->with('error', 'File dokumen tidak ditemukan di server.');
        }

        $mime = mime_content_type($path) ?: 'application/octet-stream';

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'inline; filename="' . basename($filename) . '"')
            ->setBody((string) file_get_contents($path));
    }

    public function activate_akun(int $id)
    {
        $anak = $this->anakModel->find($id);

        if (! $anak) {
            return redirect()->back()->with('error', 'Data anak tidak ditemukan.');
        }

        $userModel = new UserModel();

        if ($userModel->byAnak($id)) {
            return redirect()->back()->with('error', 'Akun untuk anak ini sudah ada.');
        }

        $role = (new RoleModel())->where('name', 'anak')->first();

        if (! $role) {
            return redirect()->back()->with('error', 'Role anak belum tersedia. Jalankan seeder terlebih dahulu.');
        }

        $base     = strtolower(preg_replace('/[^a-z0-9]+/i', '', (string) $anak['nama_anak'])) ?: 'anak';
        $username = $base . $id;
        $suffix   = 1;

        while ($userModel->isUsernameTaken($username)) {
            $username = $base . $id . $suffix++;
        }

        $password = 'anak' . $id;

        $userModel->saveUser([
            'nama'      => $anak['nama_anak'],
            'username'  => $username,
            'password'  => $password,
            'id_role'   => (int) $role['id_role'],
            'id_anak'   => $id,
            'is_active' => 1,
        ]);

        log_activity('add_user', 'Mengaktifkan akun anak: ' . $anak['nama_anak']);

        return redirect()->back()->with('success', 'Akun anak berhasil dibuat. Username: ' . $username . ' | Password: ' . $password);
    }

    public function ajax()
    {
        $filters = [
            'status_anak'   => (string) $this->request->getGet('status'),
            'search'        => (string) $this->request->getGet('search'),
            'jenis_kelamin' => (string) $this->request->getGet('jenis_kelamin'),
        ];

        $start  = max(0, (int) $this->request->getGet('start'));
        $length = min(200, max(1, (int) ($this->request->getGet('length') ?: 25)));

        return $this->response->setJSON([
            'draw'            => (int) $this->request->getGet('draw'),
            'recordsTotal'    => $this->anakModel->countList([]),
            'recordsFiltered' => $this->anakModel->countList($filters),
            'data'            => $this->anakModel->getList($filters, $length, $start),
        ]);
    }

    private function payload(): array
    {
        $fields = [
            'no_registrasi', 'nama_anak', 'nik', 'no_kk', 'jenis_kelamin', 'tempat_lahir',
            'tanggal_lahir', 'agama', 'kewarganegaraan', 'anak_ke', 'jumlah_saudara_kandung',
            'jumlah_saudara_tiri', 'nama_wali', 'no_telp_wali', 'alamat_wali',
            'nama_ayah_kandung', 'nama_ayah_tiri', 'nama_ibu_kandung', 'nama_ibu_tiri',
            'pekerjaan_ayah', 'pekerjaan_ibu', 'no_telp_orang_tua', 'alamat_orang_tua',
            'pendidikan', 'status_anak', 'status_tinggal', 'kategori', 'tanggal_masuk',
            'id_provinsi', 'id_kabupaten', 'nama_sekolah', 'kelas', 'alamat_sekolah',
            'no_telp_sekolah', 'biaya_spp',
        ];

        $data = [];

        foreach ($fields as $field) {
            $value = $this->request->getPost($field);
            $data[$field] = ($value === '' ? null : $value);
        }

        foreach (['id_provinsi', 'id_kabupaten', 'anak_ke', 'jumlah_saudara_kandung', 'jumlah_saudara_tiri'] as $intField) {
            $data[$intField] = ($data[$intField] === null ? null : (int) $data[$intField]);
        }

        $data['biaya_spp'] = ($data['biaya_spp'] === null || $data['biaya_spp'] === '')
            ? null
            : (float) str_replace(',', '.', (string) $data['biaya_spp']);
        $data['status_anak'] = $data['status_anak'] ?: 'Aktif';

        return $data;
    }

    private function handleUploads(int $id, array $data): void
    {
        $map = [
            'foto'           => ['foto_anak', 'foto', ['jpg', 'jpeg', 'png', 'webp']],
            'file_kk'        => ['file_kk', 'kk', ['jpg', 'jpeg', 'png', 'webp', 'pdf']],
            'file_akta'      => ['file_akta', 'akta', ['jpg', 'jpeg', 'png', 'webp', 'pdf']],
            'file_pendukung' => ['file_pendukung', 'pendukung', ['jpg', 'jpeg', 'png', 'webp', 'pdf']],
        ];

        foreach ($map as $postName => [$folder, $prefix, $allowed]) {
            $file = $this->request->getFile($postName);

            if ($file === null || $file->getError() === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $name = store_upload($file, $folder, $prefix, $allowed);

            if ($name !== null) {
                $this->anakModel->update($id, [$postName => $name]);
            }
        }
    }

    private function formOptions(): array
    {
        return [
            'status'      => ['Aktif', 'Nonaktif', 'Alumni'],
            'pendidikan'  => ['TK', 'SD', 'MI', 'SMP', 'MTs', 'SMA', 'MA', 'SMK', 'D3', 'S1', 'PT'],
            'kategori'    => ['Yatim', 'Piatu', 'Yatim Piatu', 'Dhuafa', 'Fakir dan Miskin', 'Ibnu Sabil', 'Laqith'],
            'tinggal'     => ['Tinggal di LKSA', 'Luar LKSA', 'Pulang ke Keluarga'],
            'agama'       => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'],
            'provinsi'    => $this->db->table('provinsi')->orderBy('nama_provinsi', 'ASC')->get()->getResultArray(),
            'kabupaten'   => $this->db->table('kabupaten')->orderBy('nama_kabupaten', 'ASC')->get()->getResultArray(),
        ];
    }
}
