<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateMsWebsitesGeneralStructure extends Migration
{
    public function up()
    {
        // Rename nama_prodi → website_name
        $this->forge->modifyColumn('ms_websites', [
            'nama_prodi' => [
                'name'       => 'website_name',
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
        ]);

        // Add category column after website_name
        $this->forge->addColumn('ms_websites', [
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'prodi',
                'after'      => 'website_name',
            ],
            'description' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'url',
            ],
        ]);
    }

    public function down()
    {
        // Remove added columns
        $this->forge->dropColumn('ms_websites', ['category', 'description']);

        // Rename back
        $this->forge->modifyColumn('ms_websites', [
            'website_name' => [
                'name'       => 'nama_prodi',
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
        ]);
    }
}
