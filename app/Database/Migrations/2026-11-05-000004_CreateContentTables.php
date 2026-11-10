<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContentTables extends Migration
{
    private array $opts = ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci'];

    public function up(): void
    {
        // carousel_images
        $this->forge->addField([
            'id_carousel' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'image_name'  => ['type' => 'VARCHAR', 'constraint' => 255],
            'title'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'description' => ['type' => 'TEXT', 'null' => true],
            'sort_order'  => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'is_active'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_by'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_carousel', true);
        $this->forge->addKey(['is_active', 'sort_order']);
        $this->forge->addForeignKey('created_by', 'users', 'id_user', 'SET NULL', 'CASCADE');
        $this->forge->createTable('carousel_images', true, $this->opts);

        // hero_images
        $this->forge->addField([
            'id_hero'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'image_name'  => ['type' => 'VARCHAR', 'constraint' => 255],
            'title'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'subtitle'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'description' => ['type' => 'TEXT', 'null' => true],
            'sort_order'  => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'is_active'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_by'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_hero', true);
        $this->forge->addKey(['is_active', 'sort_order']);
        $this->forge->addForeignKey('created_by', 'users', 'id_user', 'SET NULL', 'CASCADE');
        $this->forge->createTable('hero_images', true, $this->opts);

        // fasilitas
        $this->forge->addField([
            'id_fasilitas'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_fasilitas' => ['type' => 'VARCHAR', 'constraint' => 100],
            'deskripsi'      => ['type' => 'TEXT', 'null' => true],
            'gambar'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'icon'           => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'sort_order'     => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'is_active'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_fasilitas', true);
        $this->forge->addKey(['is_active', 'sort_order']);
        $this->forge->createTable('fasilitas', true, $this->opts);

        // user_logs
        $this->forge->addField([
            'id_log'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_user'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'activity'   => ['type' => 'VARCHAR', 'constraint' => 100],
            'description' => ['type' => 'TEXT', 'null' => true],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'user_agent' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_log', true);
        $this->forge->addKey(['id_user', 'created_at']);
        $this->forge->addKey('activity');
        $this->forge->addForeignKey('id_user', 'users', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->createTable('user_logs', true, $this->opts);

        // laporan (arsip dokumen/laporan)
        $this->forge->addField([
            'id_laporan'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'jenis_laporan' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'tahun'         => ['type' => 'YEAR', 'null' => true],
            'file_laporan'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_laporan', true);
        $this->forge->addKey(['jenis_laporan', 'tahun']);
        $this->forge->createTable('laporan', true, $this->opts);
    }

    public function down(): void
    {
        $this->forge->dropTable('laporan', true);
        $this->forge->dropTable('user_logs', true);
        $this->forge->dropTable('fasilitas', true);
        $this->forge->dropTable('hero_images', true);
        $this->forge->dropTable('carousel_images', true);
    }
}
