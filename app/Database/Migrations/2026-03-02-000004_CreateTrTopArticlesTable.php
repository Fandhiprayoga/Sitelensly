<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTrTopArticlesTable extends Migration
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
            'performance_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'article_title' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
            ],
            'article_clicks' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'rank' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'comment'    => '1=Top 1, 2=Top 2, 3=Top 3',
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
        $this->forge->addForeignKey('performance_id', 'tr_performance', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tr_top_articles');
    }

    public function down()
    {
        $this->forge->dropTable('tr_top_articles');
    }
}
