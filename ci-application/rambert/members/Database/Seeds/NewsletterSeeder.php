<?php

namespace Members\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NewsletterSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'id' => 1,
            'title' => 'Bulletin d\'informations par e-mail',
            'date_creation' => '2024-11-03 20:54:11',
            'date_modification' => null,
            'date_delete' => null,
        ];

        // Insertion de l'enregistrement
        $this->db->table('newsletter')->insert($data);
    }
}
