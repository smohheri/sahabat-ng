<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'label' => 'Administrator', 'description' => 'Akses penuh ke seluruh sistem'],
            ['name' => 'petugas', 'label' => 'Petugas', 'description' => 'Mengelola data operasional LKSA'],
            ['name' => 'dinas', 'label' => 'Dinas Sosial', 'description' => 'Melihat laporan dan statistik'],
            ['name' => 'operator', 'label' => 'Operator', 'description' => 'Mengelola data akademik dan konten'],
            ['name' => 'pengajar', 'label' => 'Pengajar', 'description' => 'Mengelola penilaian dan akademik kelas'],
            ['name' => 'anak', 'label' => 'Anak Asuh', 'description' => 'Melihat profil dan penilaian mandiri'],
        ];

        foreach ($roles as $role) {
            $exists = $this->db->table('roles')->where('name', $role['name'])->countAllResults();

            if (! $exists) {
                $role['created_at'] = date('Y-m-d H:i:s');
                $role['updated_at'] = date('Y-m-d H:i:s');
                $this->db->table('roles')->insert($role);
            }
        }
    }
}
