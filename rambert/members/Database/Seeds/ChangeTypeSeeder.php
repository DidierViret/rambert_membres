<?php

namespace Members\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ChangeTypeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id'            => 1,
                'name'          => 'Admission',
                'description'   => 'Inscription d\'une nouvelle personne en tant que membre du club Rambert',
                'date_creation' => '2024-10-28 19:47:56',
                'date_modification' => '2024-10-28 19:55:56',
                'date_delete'   => null,
            ],
            [
                'id'            => 2,
                'name'          => 'Sortie',
                'description'   => 'Démission, décès ou toute autre raison qui fait qu\'une personne n\'est plus membre du club Rambert',
                'date_creation' => '2024-10-28 19:47:56',
                'date_modification' => '2024-10-28 19:56:01',
                'date_delete'   => null,
            ],
            [
                'id'            => 3,
                'name'          => 'Changement de catégorie de membre',
                'description'   => null,
                'date_creation' => '2024-10-28 19:50:11',
                'date_modification' => '2024-10-28 19:53:20',
                'date_delete'   => null,
            ],
            [
                'id'            => 4,
                'name'          => 'Changement d\'adresse postale',
                'description'   => 'Changement de foyer ou modification de l\'adresse postale d\'un foyer',
                'date_creation' => '2024-10-28 19:50:11',
                'date_modification' => '2024-10-28 19:54:00',
                'date_delete'   => null,
            ],
            [
                'id'            => 5,
                'name'          => 'Changement de nom',
                'description'   => 'Modification du nom ou du prénom d\'un membre',
                'date_creation' => '2024-10-28 19:55:45',
                'date_modification' => null,
                'date_delete'   => null,
            ],
            [
                'id'            => 6,
                'name'          => 'Changement de coordonnées',
                'description'   => 'Modification d\'un numéro de téléphone ou de l\'e-mail d\'un membre',
                'date_creation' => '2024-10-28 19:55:45',
                'date_modification' => null,
                'date_delete'   => null,
            ],
            [
                'id'            => 7,
                'name'          => 'Changement d\'une information',
                'description'   => 'Modification des parrains, de la date de naissance ou de la profession d\'un membre',
                'date_creation' => '2024-10-28 19:59:08',
                'date_modification' => null,
                'date_delete'   => null,
            ],
            [
                'id'            => 8,
                'name'          => 'Changement de mot de passe',
                'description'   => 'Modification du mot de passe d\'un membre',
                'date_creation' => '2024-10-28 19:59:08',
                'date_modification' => null,
                'date_delete'   => null,
            ],
            [
                'id'            => 9,
                'name'          => 'Changement de contribution',
                'description'   => 'Début ou fin de la contribution d\'un membre à une commission ou autre fonction',
                'date_creation' => '2024-10-28 20:00:39',
                'date_modification' => null,
                'date_delete'   => null,
            ],
        ];

        // Insère les données dans la table `change_type`
        $this->db->table('change_type')->insertBatch($data);
    }
}
