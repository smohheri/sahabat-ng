<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAnakTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id_anak'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'no_registrasi'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'nama_anak'             => ['type' => 'VARCHAR', 'constraint' => 150],
            'nik'                   => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'no_kk'                 => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'jenis_kelamin'         => ['type' => 'ENUM', 'constraint' => "'L','P'"],
            'tempat_lahir'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'tanggal_lahir'         => ['type' => 'DATE', 'null' => true],
            'agama'                 => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'kewarganegaraan'       => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'default' => 'Indonesia'],
            'anak_ke'               => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'jumlah_saudara_kandung' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'jumlah_saudara_tiri'   => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'nama_wali'             => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'no_telp_wali'          => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'alamat_wali'           => ['type' => 'TEXT', 'null' => true],
            'nama_ayah_kandung'     => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'nama_ayah_tiri'        => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'nama_ibu_kandung'      => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'nama_ibu_tiri'         => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'pekerjaan_ayah'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'pekerjaan_ibu'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'no_telp_orang_tua'     => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'alamat_orang_tua'      => ['type' => 'TEXT', 'null' => true],
            'pendidikan'            => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'status_anak'           => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Aktif'],
            'status_tinggal'        => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'default' => 'Tinggal di LKSA'],
            'kategori'              => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'tanggal_masuk'         => ['type' => 'DATE', 'null' => true],
            'id_provinsi'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'id_kabupaten'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'nama_sekolah'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'kelas'                 => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'alamat_sekolah'        => ['type' => 'TEXT', 'null' => true],
            'no_telp_sekolah'       => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'biaya_spp'             => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],
            'file_kk'               => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'file_akta'             => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'file_pendukung'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'foto'                  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'            => ['type' => 'DATETIME', 'null' => true],
            'updated_at'            => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_anak', true);
        $this->forge->addUniqueKey('no_registrasi');
        $this->forge->addUniqueKey('nik');
        $this->forge->addKey('no_kk');
        $this->forge->addKey('nama_anak');
        $this->forge->addKey('jenis_kelamin');
        $this->forge->addKey('pendidikan');
        $this->forge->addKey('status_anak');
        $this->forge->addKey(['status_anak', 'tanggal_masuk']);
        $this->forge->addKey('tanggal_masuk');
        $this->forge->addKey('kategori');
        $this->forge->addKey('id_provinsi');
        $this->forge->addKey('id_kabupaten');
        $this->forge->addForeignKey('id_provinsi', 'provinsi', 'id_provinsi', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('id_kabupaten', 'kabupaten', 'id_kabupaten', 'SET NULL', 'CASCADE');
        $this->forge->createTable('anak', true, [
            'ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci',
        ]);
    }

    public function down(): void
    {
        $this->forge->dropTable('anak', true);
    }
}
