<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMsAwardingPeriodsTable extends Migration
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
            'period_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'performance_period_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'FK ke ms_periods untuk import data performansi',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'active', 'completed'],
                'default'    => 'draft',
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
        $this->forge->addForeignKey('performance_period_id', 'ms_periods', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('ms_awarding_periods');
    }

    public function down()
    {
        $this->forge->dropTable('ms_awarding_periods');
    }
}
