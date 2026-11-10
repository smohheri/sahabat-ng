<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReferenceTables extends Migration
{
    public function up(): void
    {
        $opts = ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci'];

        // roles
        $this->forge->addField([
            'id_role'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 50],
            'label'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'description' => ['type' => 'TEXT', 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_role', true);
        $this->forge->addUniqueKey('name');
        $this->forge->createTable('roles', true, $opts);

        // provinsi
        $this->forge->addField([
            'id_provinsi'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_provinsi' => ['type' => 'VARCHAR', 'constraint' => 100],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_provinsi', true);
        $this->forge->addKey('nama_provinsi');
        $this->forge->createTable('provinsi', true, $opts);

        // kabupaten
        $this->forge->addField([
            'id_kabupaten'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_provinsi'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nama_kabupaten' => ['type' => 'VARCHAR', 'constraint' => 100],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_kabupaten', true);
        $this->forge->addKey(['id_provinsi', 'nama_kabupaten']);
        $this->forge->addForeignKey('id_provinsi', 'provinsi', 'id_provinsi', 'CASCADE', 'CASCADE');
        $this->forge->createTable('kabupaten', true, $opts);

        // pengaturan (single row)
        $this->forge->addField([
            'id_pengaturan'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_lksa'        => ['type' => 'VARCHAR', 'constraint' => 150],
            'alamat'           => ['type' => 'TEXT', 'null' => true],
            'no_telp'          => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'email'            => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'nama_kepala'      => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'nama_kepala_putra' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'nama_kepala_putri' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'tahun_berdiri'    => ['type' => 'YEAR', 'null' => true],
            'logo'             => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'dokumen_legal'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'kop_surat'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'hero_image'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'about_image'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'facebook'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'twitter'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'instagram'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'youtube'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'linkedin'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'whatsapp'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_pengaturan', true);
        $this->forge->createTable('pengaturan', true, $opts);

        // guru
        $this->forge->addField([
            'id_guru'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_guru'            => ['type' => 'VARCHAR', 'constraint' => 150],
            'nip'                  => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'jabatan'              => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'pendidikan_terakhir'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'bidang_keahlian'      => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'sertifikasi'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'pengalaman_tahun'     => ['type' => 'TINYINT', 'constraint' => 3, 'unsigned' => true, 'null' => true],
            'status_kepegawaian'   => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'no_hp'                => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'email'                => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'alamat'               => ['type' => 'TEXT', 'null' => true],
            'foto'                 => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'file_ijazah_terakhir' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_active'            => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'           => ['type' => 'DATETIME', 'null' => true],
            'updated_at'           => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_guru', true);
        $this->forge->addKey('nama_guru');
        $this->forge->addKey('email');
        $this->forge->createTable('guru', true, $opts);

        // pengurus
        $this->forge->addField([
            'id_pengurus'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_pengurus'  => ['type' => 'VARCHAR', 'constraint' => 150],
            'jabatan'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'no_hp'          => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'email'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'file_ktp'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'foto'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_pengurus', true);
        $this->forge->addKey('nama_pengurus');
        $this->forge->addKey('jabatan');
        $this->forge->createTable('pengurus', true, $opts);
    }

    public function down(): void
    {
        $this->forge->dropTable('pengurus', true);
        $this->forge->dropTable('guru', true);
        $this->forge->dropTable('pengaturan', true);
        $this->forge->dropTable('kabupaten', true);
        $this->forge->dropTable('provinsi', true);
        $this->forge->dropTable('roles', true);
    }
}
