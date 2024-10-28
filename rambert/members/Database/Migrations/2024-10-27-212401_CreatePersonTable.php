<?php

namespace Members\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePersonTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'fk_home' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => false,
            ],
            'fk_category' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => false,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'first_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'last_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'phone_1' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'phone_2' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'birth' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'profession' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'godfathers' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'membership_start' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'membership_end' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'membership_end_reason' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'comments' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'date_modification' => [
                'type'    => 'TIMESTAMP',
                'null'    => true,
                'default' => null,
                'on_update' => 'CURRENT_TIMESTAMP',
            ],
            'date_delete' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('fk_home', 'home', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('fk_category', 'category', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->createTable('person');
    }

    public function down()
    {
        $this->forge->dropTable('person');
    }
}
