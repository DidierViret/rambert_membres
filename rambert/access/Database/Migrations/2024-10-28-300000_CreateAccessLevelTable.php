<?php

namespace Access\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAccessLevelTable extends Migration
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
                'null'       => false,
            ],
            'level' => [
                'type'       => 'INT',
                'null'       => false,
            ],
            'date_creation TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'date_modification TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP',
            'date_delete' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'default'    => null,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('access_level');

        // Insert entries in newly created table
        $seeder = \Config\Database::seeder();
        $seeder->call('\Access\Database\Seeds\AccessLevelSeeder');
    }

    public function down()
    {
        $this->forge->dropTable('access_level');
    }
}
