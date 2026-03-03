<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTrPerformanceTable extends Migration
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
            'website_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'period_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
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
            'total_new_posts' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'last_post_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'input_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
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
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('website_id', 'ms_websites', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('period_id', 'ms_periods', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['website_id', 'period_id']);
        $this->forge->createTable('tr_performance');
    }

    public function down()
    {
        $this->forge->dropTable('tr_performance');
    }
}
