<?php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;

class PbhCabangSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['kode'=>'DPN','nama'=>'DPN PERADI (Pusat)','tipe'=>'DPN','alamat'=>'Jl. Jend. Achmad Yani No.116, Jakarta Timur 13120','telepon'=>'021-1234567','email'=>'dpn@peradi.or.id','is_active'=>1],
            ['kode'=>'JKT','nama'=>'PBH DPC Jakarta','tipe'=>'DPC','alamat'=>'Jakarta','telepon'=>'021-1111111','email'=>'jakarta@peradi.or.id','is_active'=>1],
            ['kode'=>'BDG','nama'=>'PBH DPC Bandung','tipe'=>'DPC','alamat'=>'Bandung','telepon'=>'022-2222222','email'=>'bandung@peradi.or.id','is_active'=>1],
            ['kode'=>'SBY','nama'=>'PBH DPC Surabaya','tipe'=>'DPC','alamat'=>'Surabaya','telepon'=>'031-3333333','email'=>'surabaya@peradi.or.id','is_active'=>1],
            ['kode'=>'MDN','nama'=>'PBH DPC Medan','tipe'=>'DPC','alamat'=>'Medan','telepon'=>'061-4444444','email'=>'medan@peradi.or.id','is_active'=>1],
            ['kode'=>'MKS','nama'=>'PBH DPC Makassar','tipe'=>'DPC','alamat'=>'Makassar','telepon'=>'0411-5555555','email'=>'makassar@peradi.or.id','is_active'=>1],
        ];
        $now = date('Y-m-d H:i:s');
        foreach ($data as &$d) { $d['created_at'] = $now; $d['updated_at'] = $now; }
        $this->db->table('pbh_cabang')->insertBatch($data);
    }
}