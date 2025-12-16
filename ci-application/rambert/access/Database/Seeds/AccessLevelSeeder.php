<?php

namespace Access\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AccessLevelSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id'            => 1,
                'name'          => 'Administrateur',
                'description'   => '',
                'level'         => 5,
                'date_creation' => '2024-10-27 21:42:19',
                'date_modification' => null,
                'date_delete'   => null,
            ],
            [
                'id'            => 2,
                'name'          => 'Gestionnaire',
                'description'   => 'Gestionnaire du fichier des membres',
                'level'         => 4,
                'date_creation' => '2024-10-27 21:42:19',
                'date_modification' => null,
                'date_delete'   => null,
            ],
        ];

        // Insère les données dans la table `access_level`
        $this->db->table('access_level')->insertBatch($data);
    }
}
