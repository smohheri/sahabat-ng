<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PengaturanModel;
use App\Models\PengurusModel;

class Pengaturan extends BaseController
{
    private PengaturanModel $model;

    public function __construct()
    {
        $this->model = new PengaturanModel();
    }

    public function index()
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        $settings = $this->model->find(1);

        if (! $settings) {
            $this->model->insert(['id_pengaturan' => 1, 'nama_lksa' => 'LKSA Harapan Bangsa']);
            $settings = $this->model->find(1);
        }

        return $this->render('admin/pengaturan', [
            'title'     => 'Pengaturan - ' . app_name(),
            'pageTitle' => 'Pengaturan Aplikasi',
            'item'      => $settings,
            'pengurus'  => (new PengurusModel())->orderBy('jabatan', 'ASC')->orderBy('nama_pengurus', 'ASC')->findAll(),
        ]);
    }

    public function update()
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        $fields = [
            'nama_lksa', 'alamat', 'no_telp', 'email', 'nama_kepala',
            'nama_kepala_putra', 'nama_kepala_putri', 'tahun_berdiri',
            'facebook', 'twitter', 'instagram', 'youtube', 'linkedin', 'whatsapp',
            'bank_nama', 'bank_nomor_rekening', 'bank_atas_nama',
        ];

        $data = [];

        foreach ($fields as $field) {
            $value = $this->request->getPost($field);
            $data[$field] = ($value === '' ? null : trim((string) $value));
        }

        $this->model->update(1, $data);

        $this->uploadTo('logo', 'logos', 'logo', 'logo', 'upload_logo');
        $this->uploadTo('dokumen_legal', 'dokumen', 'dokumen', 'dokumen_legal', 'upload_dokumen', ['pdf', 'jpg', 'jpeg', 'png']);
        $this->uploadTo('kop_surat', 'kop', 'kop', 'kop_surat', 'upload_kop', ['jpg', 'jpeg', 'png', 'webp']);
        $this->uploadTo('hero_image', 'landing', 'hero', 'hero_image', 'upload_hero_image', ['jpg', 'jpeg', 'png', 'webp']);
        $this->uploadTo('about_image', 'landing', 'about', 'about_image', 'upload_about_image', ['jpg', 'jpeg', 'png', 'webp']);
        $this->uploadTo('qris_image', 'qris', 'qris', 'qris_image', 'upload_qris', ['jpg', 'jpeg', 'png', 'webp']);

        log_activity('update_settings', 'Memperbarui pengaturan aplikasi');

        return redirect()->to(site_url('admin/pengaturan'))->with('success', 'Pengaturan berhasil disimpan.');
    }

    public function upload_logo()
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        return $this->uploadTo('logo', 'logos', 'logo', 'logo', 'upload_logo');
    }

    public function upload_dokumen()
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        return $this->uploadTo('dokumen_legal', 'dokumen', 'dokumen', 'dokumen_legal', 'upload_dokumen', ['pdf', 'jpg', 'jpeg', 'png']);
    }

    public function upload_kop()
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        return $this->uploadTo('kop_surat', 'kop', 'kop', 'kop_surat', 'upload_kop', ['jpg', 'jpeg', 'png', 'webp']);
    }

    public function upload_about_image()
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        return $this->uploadTo('about_image', 'landing', 'about', 'about_image', 'upload_about_image', ['jpg', 'jpeg', 'png', 'webp']);
    }

    private function uploadTo(string $postName, string $folder, string $prefix, string $column, string $activity, array $allowed = ['jpg', 'jpeg', 'png', 'webp'])
    {
        $file = $this->request->getFile($postName);

        if ($file === null || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $name = store_upload($file, $folder, $prefix, $allowed);

        if ($name === null) {
            return redirect()->back()->with('error', 'Berkas ' . $prefix . ' tidak valid atau gagal diunggah.');
        }

        $old = $this->model->find(1);

        if ($old) {
            delete_upload($folder, $old->{$column} ?? null);
        }

        $this->model->update(1, [$column => $name]);
        log_activity($activity, 'Mengunggah ' . $prefix);

        // Regenerate favicon saat logo diunggah (mendukung WebP -> PNG).
        if ($column === 'logo') {
            generate_favicon($name);
        }

        return redirect()->to(site_url('admin/pengaturan'))->with('success', 'Berkas berhasil diunggah.');
    }
}
