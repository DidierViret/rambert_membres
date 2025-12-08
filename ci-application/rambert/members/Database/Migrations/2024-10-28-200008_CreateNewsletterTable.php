<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNewsletterTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'date_creation TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'date_modification TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP',
            'date_delete' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('newsletter');

        // Insert entries in newly created table
        $seeder = \Config\Database::seeder();
        $seeder->call('\Members\Database\Seeds\NewsletterSeeder');
    }

    public function down()
    {
        $this->forge->dropTable('newsletter');
    }
}

