<?php
namespace Tests\rambert\access\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AccessTestSeeder extends Seeder
{
    public function run()
    {
        // INSERTIONS IN home TABLE
        $data = [
            [
                'id'                    => 1,
                'address_name'          => 'Home address name',
                'address_line_1'        => 'Home address line 1',
            ],
        ];
        $this->db->table('home')->insertBatch($data);

        // INSERTIONS IN person TABLE
        $data = [
            [
                'id'                    => 1,
                'fk_home'               => 1,
                'fk_category'           => 1,
                'first_name'            => 'Administrator',
                'last_name'             => 'Administrator',
            ],
            [
                'id'                    => 2,
                'fk_home'               => 1,
                'fk_category'           => 1,
                'first_name'            => 'Manager',
                'last_name'             => 'Manager',
            ],
        ];
        $this->db->table('person')->insertBatch($data);

        // INSERTIONS IN access TABLE
        $data = [
            [
                'id'                    => 1,
                'fk_access_level'       => 1, // Administrator
                'fk_person'             => 1,
            ],
            [
                'id'                    => 2,
                'fk_access_level'       => 2, // Manager
                'fk_person'             => 2,
            ],
        ];
        $this->db->table('access')->insertBatch($data);
    }
}
