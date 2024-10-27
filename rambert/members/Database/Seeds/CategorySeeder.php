<?php

namespace Members\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id'              => 1,
                'name'            => 'Junior',
                'description'     => 'Entre 8 et 16 ans',
                'admission_price' => null,
                'annual_price'    => null,
                'date_creation'   => '2024-10-24 18:43:08',
                'date_modification' => null,
                'date_delete'     => null,
            ],
            [
                'id'              => 2,
                'name'            => 'Jeune',
                'description'     => 'Entre 17 et 24 ans',
                'admission_price' => null,
                'annual_price'    => null,
                'date_creation'   => '2024-10-24 18:43:08',
                'date_modification' => null,
                'date_delete'     => null,
            ],
            [
                'id'              => 3,
                'name'            => 'Actif',
                'description'     => 'dès 25 ans',
                'admission_price' => null,
                'annual_price'    => null,
                'date_creation'   => '2024-10-24 18:46:00',
                'date_modification' => null,
                'date_delete'     => null,
            ],
            [
                'id'              => 4,
                'name'            => 'Conjoint-e d\'actif',
                'description'     => 'Conjoint ou conjointe d\'un membre actif',
                'admission_price' => null,
                'annual_price'    => null,
                'date_creation'   => '2024-10-24 18:47:43',
                'date_modification' => null,
                'date_delete'     => null,
            ],
            [
                'id'              => 5,
                'name'            => 'Honoraire',
                'description'     => '25 ans de sociétariat',
                'admission_price' => null,
                'annual_price'    => null,
                'date_creation'   => '2024-10-24 18:48:21',
                'date_modification' => null,
                'date_delete'     => null,
            ],
            [
                'id'              => 6,
                'name'            => 'Conjoint-e d\'honoraire',
                'description'     => 'Conjoint ou conjointe d\'un membre honoraire',
                'admission_price' => null,
                'annual_price'    => null,
                'date_creation'   => '2024-10-24 18:48:51',
                'date_modification' => null,
                'date_delete'     => null,
            ],
            [
                'id'              => 7,
                'name'            => 'Jubilaire',
                'description'     => '50 ans de sociétariat',
                'admission_price' => null,
                'annual_price'    => null,
                'date_creation'   => '2024-10-24 18:49:28',
                'date_modification' => null,
                'date_delete'     => null,
            ],
            [
                'id'              => 8,
                'name'            => 'Membre d\'honneur',
                'description'     => 'Reconnaissance pour un engagement particulier',
                'admission_price' => null,
                'annual_price'    => null,
                'date_creation'   => '2024-10-24 18:50:29',
                'date_modification' => null,
                'date_delete'     => null,
            ],
            [
                'id'              => 9,
                'name'            => 'Veuf-ve',
                'description'     => 'Conjoint ou conjointe veuf-ve d\'un membre décédé',
                'admission_price' => null,
                'annual_price'    => null,
                'date_creation'   => '2024-10-24 18:51:27',
                'date_modification' => null,
                'date_delete'     => null,
            ],
        ];

        // Insertion des données
        $this->db->table('category')->insertBatch($data);
    }
}
