<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id_user'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama'          => ['type' => 'VARCHAR', 'constraint' => 100],
            'username'      => ['type' => 'VARCHAR', 'constraint' => 50],
            'password'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'id_role'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'id_anak'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'id_guru'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'id_pengurus'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'is_active'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'last_login_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_user', true);
        $this->forge->addUniqueKey('username');
        $this->forge->addUniqueKey('id_anak');
        $this->forge->addUniqueKey('id_guru');
        $this->forge->addKey('id_role');
        $this->forge->addKey('id_pengurus');
        $this->forge->addForeignKey('id_role', 'roles', 'id_role', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('id_anak', 'anak', 'id_anak', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('id_guru', 'guru', 'id_guru', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('id_pengurus', 'pengurus', 'id_pengurus', 'SET NULL', 'CASCADE');
        $this->forge->createTable('users', true, [
            'ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci',
        ]);
    }

    public function down(): void
    {
        $this->forge->dropTable('users', true);
    }
}
