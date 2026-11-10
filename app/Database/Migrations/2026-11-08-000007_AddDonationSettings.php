<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDonationSettings extends Migration
{
    private array $opts = ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci'];

    public function up(): void
    {
        // Kolom informasi donasi LKSA (rekening resmi & QRIS), bukan donasi developer.
        $fields = [
            'bank_nama'           => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'whatsapp'],
            'bank_nomor_rekening' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'bank_nama'],
            'bank_atas_nama'      => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true, 'after' => 'bank_nomor_rekening'],
            'qris_image'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'bank_atas_nama'],
        ];

        $this->forge->addColumn('pengaturan', $fields);
    }

    public function down(): void
    {
        $this->forge->dropColumn('pengaturan', ['qris_image', 'bank_atas_nama', 'bank_nomor_rekening', 'bank_nama']);
    }
}