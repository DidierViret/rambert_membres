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
                'id'                    => 10,
                'address_name'          => 'Home address name',
                'address_line_1'        => 'Home address line 1',
            ],
        ];
        $this->db->table('home')->insertBatch($data);

        // INSERTIONS IN person TABLE
        $data = [
            [
                'id'                    => 20,
                'fk_home'               => 10,
                'fk_category'           => 1,
                'first_name'            => 'Administrator',
                'last_name'             => 'Administrator',
            ],
            [
                'id'                    => 21,
                'fk_home'               => 10,
                'fk_category'           => 1,
                'first_name'            => 'Manager',
                'last_name'             => 'Manager',
            ],
        ];
        $this->db->table('person')->insertBatch($data);

        // INSERTIONS IN access TABLE
        $data = [
            [
                'id'                    => 30,
                'fk_access_level'       => 1, // Administrator
                'fk_person'             => 20,
            ],
            [
                'id'                    => 31,
                'fk_access_level'       => 2, // Manager
                'fk_person'             => 21,
            ],
        ];
        $this->db->table('access')->insertBatch($data);
    }
}
