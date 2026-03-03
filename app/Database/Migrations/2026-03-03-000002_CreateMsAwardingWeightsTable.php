<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMsAwardingWeightsTable extends Migration
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
            'criteria_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'comment'    => 'analytics, content, web_standardization',
            ],
            'criteria_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'weight_value' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,4',
                'comment'    => 'Nilai bobot 0.0000 - 1.0000, total harus = 1',
            ],
            'criteria_type' => [
                'type'       => 'ENUM',
                'constraint' => ['benefit', 'cost'],
                'default'    => 'benefit',
                'comment'    => 'benefit = semakin besar semakin baik',
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
        $this->forge->addUniqueKey(['awarding_period_id', 'criteria_code']);
        $this->forge->createTable('ms_awarding_weights');
    }

    public function down()
    {
        $this->forge->dropTable('ms_awarding_weights');
    }
}
