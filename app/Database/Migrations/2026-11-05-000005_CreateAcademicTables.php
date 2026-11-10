<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAcademicTables extends Migration
{
    private array $opts = ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci'];

    public function up(): void
    {
        // mata_pelajaran
        $this->forge->addField([
            'id_mata_pelajaran' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'kode_mapel'        => ['type' => 'VARCHAR', 'constraint' => 50],
            'nama_mapel'        => ['type' => 'VARCHAR', 'constraint' => 150],
            'id_user_pengampu'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'is_active'         => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_mata_pelajaran', true);
        $this->forge->addUniqueKey('kode_mapel');
        $this->forge->addKey('id_user_pengampu');
        $this->forge->addForeignKey('id_user_pengampu', 'users', 'id_user', 'SET NULL', 'CASCADE');
        $this->forge->createTable('mata_pelajaran', true, $this->opts);

        // rombel
        $this->forge->addField([
            'id_rombel'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'kode_rombel'  => ['type' => 'VARCHAR', 'constraint' => 50],
            'nama_rombel'  => ['type' => 'VARCHAR', 'constraint' => 150],
            'tahun_ajaran' => ['type' => 'VARCHAR', 'constraint' => 20],
            'semester'     => ['type' => 'TINYINT', 'constraint' => 1],
            'is_active'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'keterangan'   => ['type' => 'TEXT', 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_rombel', true);
        $this->forge->addUniqueKey('kode_rombel');
        $this->forge->addUniqueKey(['nama_rombel', 'tahun_ajaran', 'semester']);
        $this->forge->addKey(['tahun_ajaran', 'semester']);
        $this->forge->createTable('rombel', true, $this->opts);

        // rombel_anak
        $this->forge->addField([
            'id_rombel_anak' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_rombel'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'id_anak'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_rombel_anak', true);
        $this->forge->addUniqueKey(['id_rombel', 'id_anak']);
        $this->forge->addKey('id_anak');
        $this->forge->addForeignKey('id_rombel', 'rombel', 'id_rombel', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_anak', 'anak', 'id_anak', 'CASCADE', 'CASCADE');
        $this->forge->createTable('rombel_anak', true, $this->opts);

        // rombel_mata_pelajaran
        $this->forge->addField([
            'id_rombel_mapel'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_rombel'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'id_mata_pelajaran' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_rombel_mapel', true);
        $this->forge->addUniqueKey(['id_rombel', 'id_mata_pelajaran']);
        $this->forge->addKey('id_mata_pelajaran');
        $this->forge->addForeignKey('id_rombel', 'rombel', 'id_rombel', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_mata_pelajaran', 'mata_pelajaran', 'id_mata_pelajaran', 'CASCADE', 'CASCADE');
        $this->forge->createTable('rombel_mata_pelajaran', true, $this->opts);

        // absensi_mapel_sessions
        $this->forge->addField([
            'id_session'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_rombel'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'id_mata_pelajaran' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tahun_ajaran'      => ['type' => 'VARCHAR', 'constraint' => 20],
            'semester'          => ['type' => 'TINYINT', 'constraint' => 1],
            'tanggal_absensi'   => ['type' => 'DATE'],
            'catatan'           => ['type' => 'TEXT', 'null' => true],
            'created_by'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_session', true);
        $this->forge->addUniqueKey(['id_rombel', 'id_mata_pelajaran', 'tahun_ajaran', 'semester', 'tanggal_absensi'], 'uq_absensi_session');
        $this->forge->addKey(['tahun_ajaran', 'semester', 'tanggal_absensi']);
        $this->forge->addKey('created_by');
        $this->forge->addForeignKey('id_rombel', 'rombel', 'id_rombel', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_mata_pelajaran', 'mata_pelajaran', 'id_mata_pelajaran', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id_user', 'SET NULL', 'CASCADE');
        $this->forge->createTable('absensi_mapel_sessions', true, $this->opts);

        // absensi_mapel_details
        $this->forge->addField([
            'id_detail'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_session'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'id_anak'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'status_kehadiran' => ['type' => 'ENUM', 'constraint' => "'Hadir','Izin','Sakit','Alpha'", 'default' => 'Hadir'],
            'keterangan'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_detail', true);
        $this->forge->addUniqueKey(['id_session', 'id_anak']);
        $this->forge->addKey('id_anak');
        $this->forge->addKey('status_kehadiran');
        $this->forge->addForeignKey('id_session', 'absensi_mapel_sessions', 'id_session', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_anak', 'anak', 'id_anak', 'CASCADE', 'CASCADE');
        $this->forge->createTable('absensi_mapel_details', true, $this->opts);
    }

    public function down(): void
    {
        $this->forge->dropTable('absensi_mapel_details', true);
        $this->forge->dropTable('absensi_mapel_sessions', true);
        $this->forge->dropTable('rombel_mata_pelajaran', true);
        $this->forge->dropTable('rombel_anak', true);
        $this->forge->dropTable('rombel', true);
        $this->forge->dropTable('mata_pelajaran', true);
    }
}
