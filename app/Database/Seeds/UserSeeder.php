<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $role = $this->db->table('roles')->where('name', 'admin')->get()->getRowArray();

        if (! $role) {
            return;
        }

        if ($this->db->table('users')->where('username', 'admin')->countAllResults() === 0) {
            $this->db->table('users')->insert([
                'nama'       => 'Administrator',
                'username'   => 'admin',
                'password'   => password_hash('admin', PASSWORD_BCRYPT),
                'id_role'    => (int) $role['id_role'],
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
