<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        if ($this->db->table('pengaturan')->where('id_pengaturan', 1)->countAllResults() === 0) {
            $this->db->table('pengaturan')->insert([
                'id_pengaturan'     => 1,
                'nama_lksa'         => 'LKSA Harapan Bangsa',
                'alamat'            => 'Jl. Contoh No. 123',
                'no_telp'           => '08123456789',
                'email'             => 'info@lksa.id',
                'nama_kepala'       => 'Budi Santoso',
                'nama_kepala_putra' => 'Budi Santoso',
                'nama_kepala_putri' => 'Siti Aminah',
                'tahun_berdiri'     => '2010',
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
