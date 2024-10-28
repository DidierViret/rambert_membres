<?php

namespace Members\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id'            => 1,
                'name'          => 'Comité central',
                'description'   => null,
                'date_creation' => '2024-10-28 20:21:12',
                'date_modification' => null,
                'date_delete'   => null,
            ],
            [
                'id'            => 2,
                'name'          => 'Commission de vérification des comptes',
                'description'   => null,
                'date_creation' => '2024-10-28 20:21:12',
                'date_modification' => null,
                'date_delete'   => null,
            ],
            [
                'id'            => 3,
                'name'          => 'Commission Rosaly',
                'description'   => null,
                'date_creation' => '2024-10-28 20:22:03',
                'date_modification' => null,
                'date_delete'   => null,
            ],
            [
                'id'            => 4,
                'name'          => 'Commission Saanenwald',
                'description'   => null,
                'date_creation' => '2024-10-28 20:22:03',
                'date_modification' => null,
                'date_delete'   => null,
            ],
            [
                'id'            => 5,
                'name'          => 'Commission des bâtiments',
                'description'   => null,
                'date_creation' => '2024-10-28 20:31:10',
                'date_modification' => null,
                'date_delete'   => null,
            ],
            [
                'id'            => 6,
                'name'          => 'Commission des finances',
                'description'   => null,
                'date_creation' => '2024-10-28 20:31:22',
                'date_modification' => null,
                'date_delete'   => null,
            ],
            [
                'id'            => 7,
                'name'          => 'Jeudistes',
                'description'   => null,
                'date_creation' => '2024-10-28 20:33:40',
                'date_modification' => null,
                'date_delete'   => null,
            ],
            [
                'id'            => 8,
                'name'          => 'Commission jeunesse',
                'description'   => null,
                'date_creation' => '2024-10-28 20:33:40',
                'date_modification' => null,
                'date_delete'   => null,
            ],
            [
                'id'            => 9,
                'name'          => 'Programme des courses',
                'description'   => null,
                'date_creation' => '2024-10-28 20:37:12',
                'date_modification' => null,
                'date_delete'   => null,
            ],
            [
                'id'            => 10,
                'name'          => 'Commission Internet',
                'description'   => null,
                'date_creation' => '2024-10-28 20:37:12',
                'date_modification' => null,
                'date_delete'   => null,
            ],
            [
                'id'            => 11,
                'name'          => 'Bulletin',
                'description'   => null,
                'date_creation' => '2024-10-28 20:39:02',
                'date_modification' => null,
                'date_delete'   => null,
            ],
            [
                'id'            => 12,
                'name'          => 'Livret annuel',
                'description'   => null,
                'date_creation' => '2024-10-28 20:39:02',
                'date_modification' => null,
                'date_delete'   => null,
            ],
            [
                'id'            => 13,
                'name'          => 'FUL, Foyer Unioniste Lausannois',
                'description'   => null,
                'date_creation' => '2024-10-28 20:40:22',
                'date_modification' => null,
                'date_delete'   => null,
            ],
        ];

        $this->db->table('team')->insertBatch($data);
    }
}
