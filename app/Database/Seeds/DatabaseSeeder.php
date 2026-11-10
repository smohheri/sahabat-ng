<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call('RoleSeeder');
        $this->call('SettingsSeeder');
        $this->call('UserSeeder');
        $this->call('LandingSeeder');
        $this->call('CharacterSeeder');
    }
}
