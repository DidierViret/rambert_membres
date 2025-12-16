<?php

namespace Members\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContributionTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'fk_person' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'fk_role' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'date_begin' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'date_end' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('fk_person', 'person', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('fk_role', 'role', 'id', 'RESTRICT', 'RESTRICT');

        $this->forge->createTable('contribution');
    }

    public function down()
    {
        $this->forge->dropTable('contribution');
    }
}
