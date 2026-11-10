<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CarouselModel;
use App\Models\FasilitasModel;
use App\Models\HeroModel;

class LandingContent extends BaseController
{
    public function carousel()
    {
        return $this->render('admin/landing/carousel', [
            'title'     => 'Carousel Landing - ' . app_name(),
            'pageTitle' => 'Carousel Landing',
            'items'     => (new CarouselModel())->allOrdered(),
        ]);
    }

    public function upload_carousel_image()
    {
        $file = $this->request->getFile('image');
        $name = store_upload($file, 'landing', 'carousel', ['jpg', 'jpeg', 'png', 'webp']);

        if ($name === null) {
            return redirect()->back()->with('error', 'Gambar carousel tidak valid atau gagal diunggah.');
        }

        (new CarouselModel())->insert([
            'image_name'  => $name,
            'title'       => $this->request->getPost('title') ?: null,
            'description' => $this->request->getPost('description') ?: null,
            'sort_order'  => (int) ($this->request->getPost('sort_order') ?: 0),
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
            'created_by'  => (int) $this->session->get('id_user'),
        ]);

        log_activity('upload_carousel_image', 'Menambahkan gambar carousel');

        return redirect()->back()->with('success', 'Gambar carousel berhasil ditambahkan.');
    }

    public function update_carousel_image()
    {
        $model = new CarouselModel();
        $id    = (int) $this->request->getPost('id_carousel');
        $item  = $model->find($id);

        if (! $item) {
            return redirect()->back()->with('error', 'Carousel tidak ditemukan.');
        }

        $data = [
            'title'       => $this->request->getPost('title') ?: null,
            'description' => $this->request->getPost('description') ?: null,
            'sort_order'  => (int) ($this->request->getPost('sort_order') ?: 0),
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $name = store_upload($this->request->getFile('image'), 'landing', 'carousel', ['jpg', 'jpeg', 'png', 'webp']);

        if ($name !== null) {
            delete_upload('landing', $item['image_name'] ?? null);
            $data['image_name'] = $name;
        }

        $model->update($id, $data);
        log_activity('update_carousel_image', 'Memperbarui carousel');

        return redirect()->back()->with('success', 'Carousel berhasil diperbarui.');
    }

    public function delete_carousel_image(int $id)
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        $model = new CarouselModel();
        $item  = $model->find($id);

        if ($item) {
            delete_upload('landing', $item['image_name'] ?? null);
            $model->delete($id);
            log_activity('delete_carousel_image', 'Menghapus carousel');
        }

        return redirect()->back()->with('success', 'Carousel berhasil dihapus.');
    }

    public function update_carousel_sort_order()
    {
        $orders = (array) $this->request->getPost('sort_order');
        $model  = new CarouselModel();

        foreach ($orders as $id => $order) {
            $model->update((int) $id, ['sort_order' => (int) $order]);
        }

        log_activity('update_carousel_sort_order', 'Mengubah urutan carousel');

        return $this->response->setJSON(['status' => true]);
    }

    public function hero()
    {
        return $this->render('admin/landing/hero', [
            'title'     => 'Hero Landing - ' . app_name(),
            'pageTitle' => 'Hero Landing',
            'items'     => (new HeroModel())->allOrdered(),
        ]);
    }

    public function upload_hero_image()
    {
        $name = store_upload($this->request->getFile('image'), 'landing', 'hero', ['jpg', 'jpeg', 'png', 'webp']);

        if ($name === null) {
            return redirect()->back()->with('error', 'Gambar hero tidak valid atau gagal diunggah.');
        }

        (new HeroModel())->insert([
            'image_name'  => $name,
            'title'       => $this->request->getPost('title') ?: null,
            'subtitle'    => $this->request->getPost('subtitle') ?: null,
            'description' => $this->request->getPost('description') ?: null,
            'sort_order'  => (int) ($this->request->getPost('sort_order') ?: 0),
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
            'created_by'  => (int) $this->session->get('id_user'),
        ]);

        log_activity('upload_hero_image', 'Menambahkan gambar hero');

        return redirect()->back()->with('success', 'Gambar hero berhasil ditambahkan.');
    }

    public function update_hero_image(int $id)
    {
        $model = new HeroModel();
        $item  = $model->find($id);

        if (! $item) {
            return redirect()->back()->with('error', 'Hero tidak ditemukan.');
        }

        $data = [
            'title'       => $this->request->getPost('title') ?: null,
            'subtitle'    => $this->request->getPost('subtitle') ?: null,
            'description' => $this->request->getPost('description') ?: null,
            'sort_order'  => (int) ($this->request->getPost('sort_order') ?: 0),
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $name = store_upload($this->request->getFile('image'), 'landing', 'hero', ['jpg', 'jpeg', 'png', 'webp']);

        if ($name !== null) {
            delete_upload('landing', $item['image_name'] ?? null);
            $data['image_name'] = $name;
        }

        $model->update($id, $data);
        log_activity('update_hero_image', 'Memperbarui hero');

        return redirect()->back()->with('success', 'Hero berhasil diperbarui.');
    }

    public function delete_hero_image(int $id)
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        $model = new HeroModel();
        $item  = $model->find($id);

        if ($item) {
            delete_upload('landing', $item['image_name'] ?? null);
            $model->delete($id);
            log_activity('delete_hero_image', 'Menghapus hero');
        }

        return redirect()->back()->with('success', 'Hero berhasil dihapus.');
    }

    public function facilities()
    {
        return $this->render('admin/landing/facilities', [
            'title'     => 'Fasilitas Landing - ' . app_name(),
            'pageTitle' => 'Fasilitas Landing',
            'items'     => (new FasilitasModel())->allOrdered(),
        ]);
    }

    public function facilities_upload()
    {
        $name = store_upload($this->request->getFile('gambar'), 'landing', 'fasilitas', ['jpg', 'jpeg', 'png', 'webp']);

        (new FasilitasModel())->insert([
            'nama_fasilitas' => (string) $this->request->getPost('nama_fasilitas'),
            'deskripsi'      => $this->request->getPost('deskripsi') ?: null,
            'icon'           => $this->request->getPost('icon') ?: null,
            'gambar'         => $name,
            'sort_order'     => (int) ($this->request->getPost('sort_order') ?: 0),
            'is_active'      => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        log_activity('add_facility', 'Menambahkan fasilitas');

        return redirect()->back()->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    public function facilities_update()
    {
        $model = new FasilitasModel();
        $id    = (int) $this->request->getPost('id_fasilitas');
        $item  = $model->find($id);

        if (! $item) {
            return redirect()->back()->with('error', 'Fasilitas tidak ditemukan.');
        }

        $data = [
            'nama_fasilitas' => (string) $this->request->getPost('nama_fasilitas'),
            'deskripsi'      => $this->request->getPost('deskripsi') ?: null,
            'icon'           => $this->request->getPost('icon') ?: null,
            'sort_order'     => (int) ($this->request->getPost('sort_order') ?: 0),
            'is_active'      => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $name = store_upload($this->request->getFile('gambar'), 'landing', 'fasilitas', ['jpg', 'jpeg', 'png', 'webp']);

        if ($name !== null) {
            delete_upload('landing', $item['gambar'] ?? null);
            $data['gambar'] = $name;
        }

        $model->update($id, $data);
        log_activity('update_facility', 'Memperbarui fasilitas');

        return redirect()->back()->with('success', 'Fasilitas berhasil diperbarui.');
    }

    public function facilities_delete(int $id)
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        $model = new FasilitasModel();
        $item  = $model->find($id);

        if ($item) {
            delete_upload('landing', $item['gambar'] ?? null);
            $model->delete($id);
            log_activity('delete_facility', 'Menghapus fasilitas');
        }

        return redirect()->back()->with('success', 'Fasilitas berhasil dihapus.');
    }
}
