<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LandingSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        if ($this->db->table('carousel_images')->countAllResults() === 0) {
            $this->db->table('carousel_images')->insertBatch([
                ['image_name' => 'default_carousel_1.jpg', 'title' => 'Pendidikan untuk Masa Depan', 'description' => 'Mendorong anak-anak untuk meraih impian mereka', 'sort_order' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['image_name' => 'default_carousel_2.jpg', 'title' => 'Perkembangan Holistik', 'description' => 'Membantu anak berkembang secara fisik dan mental', 'sort_order' => 2, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['image_name' => 'default_carousel_3.jpg', 'title' => 'Belajar Bersama', 'description' => 'Menciptakan lingkungan belajar yang menyenangkan', 'sort_order' => 3, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['image_name' => 'default_carousel_4.jpg', 'title' => 'Kegiatan Sosial', 'description' => 'Membangun keterampilan sosial dan kreativitas', 'sort_order' => 4, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if ($this->db->table('hero_images')->countAllResults() === 0) {
            $this->db->table('hero_images')->insert([
                'image_name'  => 'default_hero_1.jpg',
                'title'       => 'LKSA Harapan Bangsa',
                'subtitle'    => 'Membentuk Generasi Unggul',
                'description' => 'Pendidikan dan pengasuhan anak yang berkualitas untuk masa depan yang lebih baik',
                'sort_order'  => 1,
                'is_active'   => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }

        if ($this->db->table('fasilitas')->countAllResults() === 0) {
            $facilities = [
                ['Asrama', 'Tempat tinggal yang nyaman dan aman untuk anak-anak dengan fasilitas lengkap.', 'home'],
                ['Ruang Belajar', 'Fasilitas belajar modern dengan peralatan lengkap untuk mendukung pendidikan anak.', 'book-open'],
                ['Kantin', 'Area makan yang bersih dan sehat dengan menu bergizi untuk kebutuhan nutrisi anak.', 'utensils'],
                ['Lapangan Olahraga', 'Fasilitas olahraga lengkap untuk mengembangkan kesehatan fisik dan mental anak.', 'futbol'],
                ['Klinik Kesehatan', 'Pelayanan kesehatan 24 jam dengan tenaga medis profesional untuk kesehatan anak.', 'heart-pulse'],
                ['Perpustakaan', 'Koleksi buku dan bahan bacaan lengkap untuk meningkatkan pengetahuan dan kreativitas.', 'library'],
                ['Ruang Ibadah', 'Tempat ibadah yang tenang dan nyaman untuk membentuk karakter spiritual anak.', 'mosque'],
                ['Taman Bermain', 'Area bermain yang aman dan menyenangkan untuk mengembangkan kreativitas dan sosialisasi.', 'child'],
            ];

            $rows = [];
            foreach ($facilities as $i => $facility) {
                $rows[] = [
                    'nama_fasilitas' => $facility[0],
                    'deskripsi'      => $facility[1],
                    'icon'           => $facility[2],
                    'sort_order'     => $i + 1,
                    'is_active'      => 1,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];
            }
            $this->db->table('fasilitas')->insertBatch($rows);
        }
    }
}
