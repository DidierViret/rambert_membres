<?php

namespace Members\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id' => 1, 'fk_team' => 1, 'name' => 'Président-e', 'description' => null, 'date_creation' => '2024-10-28 20:23:45', 'date_modification' => null, 'date_delete' => null],
            ['id' => 2, 'fk_team' => 1, 'name' => 'Vice-président-e', 'description' => null, 'date_creation' => '2024-10-28 20:23:45', 'date_modification' => '2024-10-28 20:24:00', 'date_delete' => null],
            ['id' => 3, 'fk_team' => 1, 'name' => 'Caissier / Caissière', 'description' => null, 'date_creation' => '2024-10-28 20:25:05', 'date_modification' => null, 'date_delete' => null],
            ['id' => 4, 'fk_team' => 1, 'name' => 'Responsable des réservations de chalets', 'description' => null, 'date_creation' => '2024-10-28 20:25:05', 'date_modification' => null, 'date_delete' => null],
            ['id' => 5, 'fk_team' => 1, 'name' => 'Membre', 'description' => null, 'date_creation' => '2024-10-28 20:25:32', 'date_modification' => null, 'date_delete' => null],
            ['id' => 6, 'fk_team' => 2, 'name' => 'Membre', 'description' => null, 'date_creation' => '2024-10-28 20:26:56', 'date_modification' => null, 'date_delete' => null],
            ['id' => 7, 'fk_team' => 3, 'name' => 'Président-e', 'description' => null, 'date_creation' => '2024-10-28 20:27:29', 'date_modification' => null, 'date_delete' => null],
            ['id' => 8, 'fk_team' => 3, 'name' => 'Vice-président-e', 'description' => null, 'date_creation' => '2024-10-28 20:29:55', 'date_modification' => null, 'date_delete' => null],
            ['id' => 9, 'fk_team' => 3, 'name' => 'Membre', 'description' => null, 'date_creation' => '2024-10-28 20:27:29', 'date_modification' => '2024-10-28 20:29:31', 'date_delete' => null],
            ['id' => 10, 'fk_team' => 4, 'name' => 'Président-e', 'description' => null, 'date_creation' => '2024-10-28 20:28:37', 'date_modification' => '2024-10-28 20:29:17', 'date_delete' => null],
            ['id' => 11, 'fk_team' => 4, 'name' => 'Secrétaire', 'description' => null, 'date_creation' => '2024-10-28 20:30:39', 'date_modification' => null, 'date_delete' => null],
            ['id' => 12, 'fk_team' => 4, 'name' => 'Membre', 'description' => null, 'date_creation' => '2024-10-28 20:28:37', 'date_modification' => '2024-10-28 20:29:10', 'date_delete' => null],
            ['id' => 13, 'fk_team' => 5, 'name' => 'Membre', 'description' => null, 'date_creation' => '2024-10-28 20:32:42', 'date_modification' => null, 'date_delete' => null],
            ['id' => 14, 'fk_team' => 6, 'name' => 'Membre', 'description' => null, 'date_creation' => '2024-10-28 20:33:05', 'date_modification' => null, 'date_delete' => null],
            ['id' => 15, 'fk_team' => 7, 'name' => 'Responsable', 'description' => null, 'date_creation' => '2024-10-28 20:34:14', 'date_modification' => '2024-10-28 20:35:50', 'date_delete' => null],
            ['id' => 16, 'fk_team' => 8, 'name' => 'Président-e', 'description' => null, 'date_creation' => '2024-10-28 20:34:14', 'date_modification' => null, 'date_delete' => null],
            ['id' => 17, 'fk_team' => 8, 'name' => 'Membre', 'description' => null, 'date_creation' => '2024-10-28 20:34:28', 'date_modification' => null, 'date_delete' => null],
            ['id' => 18, 'fk_team' => 9, 'name' => 'Responsable', 'description' => null, 'date_creation' => '2024-10-28 20:37:40', 'date_modification' => null, 'date_delete' => null],
            ['id' => 19, 'fk_team' => 10, 'name' => 'Responsable du site web', 'description' => null, 'date_creation' => '2024-10-28 20:37:40', 'date_modification' => null, 'date_delete' => null],
            ['id' => 20, 'fk_team' => 10, 'name' => 'Responsable des réseaux sociaux', 'description' => null, 'date_creation' => '2024-10-28 20:38:23', 'date_modification' => null, 'date_delete' => null],
            ['id' => 21, 'fk_team' => 10, 'name' => 'Membre', 'description' => null, 'date_creation' => '2024-10-28 20:38:23', 'date_modification' => null, 'date_delete' => null],
            ['id' => 22, 'fk_team' => 11, 'name' => 'Responsable', 'description' => null, 'date_creation' => '2024-10-28 20:39:26', 'date_modification' => null, 'date_delete' => null],
            ['id' => 23, 'fk_team' => 12, 'name' => 'Responsable', 'description' => null, 'date_creation' => '2024-10-28 20:39:26', 'date_modification' => null, 'date_delete' => null],
            ['id' => 24, 'fk_team' => 13, 'name' => 'Délégué', 'description' => null, 'date_creation' => '2024-10-28 20:40:55', 'date_modification' => null, 'date_delete' => null],
            ['id' => 25, 'fk_team' => 14, 'name' => 'Délégué', 'description' => null, 'date_creation' => '2024-10-28 20:40:55', 'date_modification' => null, 'date_delete' => null],
            ['id' => 26, 'fk_team' => 15, 'name' => 'Membre', 'description' => null, 'date_creation' => '2024-10-28 20:40:55', 'date_modification' => null, 'date_delete' => null],
        ];

        // Insert batch
        $this->db->table('role')->insertBatch($data);
    }
}
