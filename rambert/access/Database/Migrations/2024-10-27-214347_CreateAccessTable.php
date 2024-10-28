<?php

namespace Access\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAccessTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'fk_access_level' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => false,
            ],
            'fk_person' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => false,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
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
        $this->forge->addForeignKey('fk_access_level', 'access_level', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('fk_person', 'person', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->createTable('access');
    }

    public function down()
    {
        $this->forge->dropTable('access');
    }
}
