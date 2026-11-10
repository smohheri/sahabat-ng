<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCharacterTables extends Migration
{
    private array $opts = ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci'];

    public function up(): void
    {
        // character_scale
        $this->forge->addField([
            'id_scale'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'score'       => ['type' => 'INT', 'constraint' => 11],
            'category'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'description' => ['type' => 'TEXT', 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_scale', true);
        $this->forge->addUniqueKey('score');
        $this->forge->createTable('character_scale', true, $this->opts);

        // character_aspects
        $this->forge->addField([
            'id_aspect'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'aspect_name'   => ['type' => 'VARCHAR', 'constraint' => 150],
            'aspect_code'   => ['type' => 'VARCHAR', 'constraint' => 50],
            'description'   => ['type' => 'TEXT', 'null' => true],
            'sort_order'    => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_aspect', true);
        $this->forge->addUniqueKey('aspect_code');
        $this->forge->createTable('character_aspects', true, $this->opts);

        // character_indicators
        $this->forge->addField([
            'id_indicator'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_aspect'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'indicator_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'indicator_code' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'description'    => ['type' => 'TEXT', 'null' => true],
            'sort_order'     => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_indicator', true);
        $this->forge->addKey('id_aspect');
        $this->forge->addForeignKey('id_aspect', 'character_aspects', 'id_aspect', 'CASCADE', 'CASCADE');
        $this->forge->createTable('character_indicators', true, $this->opts);

        // character_assessments
        $this->forge->addField([
            'id_assessment'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_anak'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'id_assessor'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'assessor_type'   => ['type' => 'ENUM', 'constraint' => "'guru','anak_asuh'", 'default' => 'guru'],
            'assessment_date' => ['type' => 'DATE'],
            'week_number'     => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'month'           => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'year'            => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'notes'           => ['type' => 'TEXT', 'null' => true],
            'status'          => ['type' => 'ENUM', 'constraint' => "'draft','completed','reviewed'", 'default' => 'draft'],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_assessment', true);
        $this->forge->addKey(['week_number', 'year']);
        $this->forge->addKey(['month', 'year']);
        $this->forge->addKey(['id_anak', 'assessment_date']);
        $this->forge->addKey('id_assessor');
        $this->forge->addForeignKey('id_anak', 'anak', 'id_anak', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_assessor', 'users', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->createTable('character_assessments', true, $this->opts);

        // character_assessment_details
        $this->forge->addField([
            'id_detail'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_assessment' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'id_indicator'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'score'         => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_detail', true);
        $this->forge->addUniqueKey(['id_assessment', 'id_indicator']);
        $this->forge->addKey('id_indicator');
        $this->forge->addForeignKey('id_assessment', 'character_assessments', 'id_assessment', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_indicator', 'character_indicators', 'id_indicator', 'CASCADE', 'CASCADE');
        $this->forge->createTable('character_assessment_details', true, $this->opts);

        // character_qualitative_notes
        $this->forge->addField([
            'id_note'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_assessment'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'strengths'          => ['type' => 'TEXT', 'null' => true],
            'development_observed' => ['type' => 'TEXT', 'null' => true],
            'areas_to_support'   => ['type' => 'TEXT', 'null' => true],
            'support_strategy'   => ['type' => 'TEXT', 'null' => true],
            'created_at'         => ['type' => 'DATETIME', 'null' => true],
            'updated_at'         => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_note', true);
        $this->forge->addUniqueKey('id_assessment');
        $this->forge->addForeignKey('id_assessment', 'character_assessments', 'id_assessment', 'CASCADE', 'CASCADE');
        $this->forge->createTable('character_qualitative_notes', true, $this->opts);

        // character_monthly_summary
        $this->forge->addField([
            'id_summary'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_anak'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'id_aspect'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'month'         => ['type' => 'INT', 'constraint' => 11],
            'year'          => ['type' => 'INT', 'constraint' => 11],
            'avg_score'     => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'assessor_type' => ['type' => 'ENUM', 'constraint' => "'guru','anak_asuh'", 'default' => 'guru'],
            'last_updated'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_summary', true);
        $this->forge->addUniqueKey(['id_anak', 'id_aspect', 'month', 'year', 'assessor_type'], 'uq_monthly');
        $this->forge->addKey('id_aspect');
        $this->forge->addForeignKey('id_anak', 'anak', 'id_anak', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_aspect', 'character_aspects', 'id_aspect', 'CASCADE', 'CASCADE');
        $this->forge->createTable('character_monthly_summary', true, $this->opts);

        // character_weekly_summary
        $this->forge->addField([
            'id_summary'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_anak'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'id_aspect'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'week_number'   => ['type' => 'INT', 'constraint' => 11],
            'year'          => ['type' => 'INT', 'constraint' => 11],
            'avg_score'     => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'assessor_type' => ['type' => 'ENUM', 'constraint' => "'guru','anak_asuh'", 'default' => 'guru'],
            'last_updated'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_summary', true);
        $this->forge->addUniqueKey(['id_anak', 'id_aspect', 'week_number', 'year', 'assessor_type'], 'uq_weekly');
        $this->forge->addKey('id_aspect');
        $this->forge->addForeignKey('id_anak', 'anak', 'id_anak', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_aspect', 'character_aspects', 'id_aspect', 'CASCADE', 'CASCADE');
        $this->forge->createTable('character_weekly_summary', true, $this->opts);

        // Reporting views
        $this->db->query("CREATE OR REPLACE VIEW v_monthly_character_report AS
            SELECT a.id_anak, a.nama_anak, cms.id_aspect, asp.aspect_name,
                   cms.month, cms.year, cms.avg_score, cms.assessor_type,
                   CASE
                       WHEN cms.avg_score >= 3.5 THEN 'Sangat Baik'
                       WHEN cms.avg_score >= 2.5 THEN 'Baik'
                       WHEN cms.avg_score >= 1.5 THEN 'Mulai Berkembang'
                       ELSE 'Perlu Pendampingan Intensif'
                   END AS category
            FROM anak a
            LEFT JOIN character_monthly_summary cms ON a.id_anak = cms.id_anak
            LEFT JOIN character_aspects asp ON asp.id_aspect = cms.id_aspect
            ORDER BY a.nama_anak, cms.year DESC, cms.month DESC, asp.sort_order");

        $this->db->query("CREATE OR REPLACE VIEW v_weekly_character_report AS
            SELECT a.id_anak, a.nama_anak, cws.id_aspect, asp.aspect_name,
                   cws.week_number, cws.year, cws.avg_score, cws.assessor_type,
                   CASE
                       WHEN cws.avg_score >= 3.5 THEN 'Sangat Baik'
                       WHEN cws.avg_score >= 2.5 THEN 'Baik'
                       WHEN cws.avg_score >= 1.5 THEN 'Mulai Berkembang'
                       ELSE 'Perlu Pendampingan Intensif'
                   END AS category
            FROM anak a
            LEFT JOIN character_weekly_summary cws ON a.id_anak = cws.id_anak
            LEFT JOIN character_aspects asp ON asp.id_aspect = cws.id_aspect
            ORDER BY a.nama_anak, cws.year DESC, cws.week_number DESC, asp.sort_order");
    }

    public function down(): void
    {
        $this->db->query('DROP VIEW IF EXISTS v_weekly_character_report');
        $this->db->query('DROP VIEW IF EXISTS v_monthly_character_report');
        $this->forge->dropTable('character_weekly_summary', true);
        $this->forge->dropTable('character_monthly_summary', true);
        $this->forge->dropTable('character_qualitative_notes', true);
        $this->forge->dropTable('character_assessment_details', true);
        $this->forge->dropTable('character_assessments', true);
        $this->forge->dropTable('character_indicators', true);
        $this->forge->dropTable('character_aspects', true);
        $this->forge->dropTable('character_scale', true);
    }
}
