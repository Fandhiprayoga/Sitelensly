<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTrAwardingScoresTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'awarding_period_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'website_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],

            // ---- Analytics (klik per perangkat) ----
            'clicks_web' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'clicks_mobile' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'clicks_tablet' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],

            // ---- Konten (jumlah postingan) ----
            'total_posts' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],

            // ---- Standarisasi Web (17 elemen) ----
            'std_banner' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'std_greeting' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'std_hot_news' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'std_facilities' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'std_graduated_testimony' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'std_about_program' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'std_vision_mission' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'std_organization' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'std_accreditation' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'std_academic_staff' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'std_curriculum' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'std_career_prospect' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'std_title_graduation' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'std_learning_outcome' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'std_research' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'std_news' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'std_admission' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],

            // ---- Meta ----
            'is_imported' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'comment'    => '1 = data analytics & konten diimport dari modul performansi',
            ],
            'input_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('awarding_period_id', 'ms_awarding_periods', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('website_id', 'ms_websites', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['awarding_period_id', 'website_id']);
        $this->forge->createTable('tr_awarding_scores');
    }

    public function down()
    {
        $this->forge->dropTable('tr_awarding_scores');
    }
}
