<?php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;

class MasterAdvokatSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create('id_ID');
        $cabangs = $this->db->table('pbh_cabang')->get()->getResultArray();
        $data = [];
        $niaCounter = 9800000; // 98.xxxxx

        for ($i = 0; $i < 30; $i++) {
            $niaCounter++;
            $cabang = $cabangs[array_rand($cabangs)];
            $data[] = [
                'nia'            => substr($niaCounter, 0, 2) . '.' . substr($niaCounter, 2),
                'nama_lengkap'   => $faker->name,
                'gelar'          => 'S.H.',
                'email'          => $faker->unique()->safeEmail,
                'no_wa'          => '08' . $faker->numerify('##########'),
                'pbh_cabang_id'  => $cabang['id'],
                'status_advokat' => 'aktif',
                'is_registered'  => 0,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ];
        }
        $this->db->table('master_advokat')->insertBatch($data);
    }
}