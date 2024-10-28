<?php

namespace Members\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFunctionTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'fk_team' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
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
            'date_creation TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'date_modification TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP',
            'date_delete' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
        ]);

        $this->forge->addKey('id', true);

        $this->forge->addForeignKey('fk_team', 'team', 'id', 'RESTRICT', 'RESTRICT');

        $this->forge->createTable('function');

        // Insert entries in newly created table
        $seeder = \Config\Database::seeder();
        $seeder->call('\Members\Database\Seeds\FunctionSeeder');
    }

    public function down()
    {
        $this->forge->dropTable('function');
    }
}
