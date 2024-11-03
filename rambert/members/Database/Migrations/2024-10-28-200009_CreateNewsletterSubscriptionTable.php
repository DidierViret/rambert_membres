<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNewsletterSubscriptionTable extends Migration
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
                'null' => false,
            ],
            'fk_newsletter' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'date_creation' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'date_modification' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'date_delete' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('fk_person', 'person', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('fk_newsletter', 'newsletter', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('newsletter_subscription');
    }

    public function down()
    {
        $this->forge->dropTable('newsletter_subscription');
    }
}
