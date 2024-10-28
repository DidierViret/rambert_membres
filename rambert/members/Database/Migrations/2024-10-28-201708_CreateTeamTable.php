<?php

namespace Members\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTeamTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'date_creation' => [
                'type'       => 'TIMESTAMP',
                'null'       => false,
                'default'    => 'CURRENT_TIMESTAMP',
            ],
            'date_modification' => [
                'type'       => 'TIMESTAMP',
                'null'       => true,
                'default'    => null,
                'on_update'  => 'CURRENT_TIMESTAMP',
            ],
            'date_delete' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'default'    => null,
            ],
        ]);

        $this->forge->addKey('id', true);

        $this->forge->createTable('team');

        // Insert entries in newly created table
        $seeder = \Config\Database::seeder();
        $seeder->call('\Members\Database\Seeds\TeamSeeder');
    }

    public function down()
    {
        $this->forge->dropTable('team');
    }
}
