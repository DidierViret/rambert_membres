<?php

namespace Members\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateChangeTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'fk_change_author' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => false,
            ],
            'fk_person_concerned' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
            ],
            'fk_change_type' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
            ],
            'value_old' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'value_new' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'date TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('fk_change_author', 'person', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('fk_person_concerned', 'person', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('fk_change_type', 'change_type', 'id', 'RESTRICT', 'RESTRICT');

        $this->forge->createTable('change');
    }

    public function down()
    {
        $this->forge->dropTable('change');
    }
}
