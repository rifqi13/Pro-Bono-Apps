<?php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $dpn = $this->db->table('pbh_cabang')->where('kode','DPN')->get()->getRow();
        $jkt = $this->db->table('pbh_cabang')->where('kode','JKT')->get()->getRow();

        $users = [
            // Super Admin (DPN)
            [
                'nia' => null, 'nama_lengkap' => 'Super Admin DPN', 'gelar' => null,
                'email' => 'superadmin@peradi.or.id', 'no_wa' => '081111111111',
                'password_hash' => password_hash('Admin@2026', PASSWORD_ARGON2ID),
                'role' => 'super_admin', 'pbh_cabang_id' => $dpn->id, 'status' => 'aktif',
                'email_verified_at' => $now, 'created_at' => $now, 'updated_at' => $now,
            ],
            // Admin (DPN)
            [
                'nia' => null, 'nama_lengkap' => 'Admin Verifikator DPN', 'gelar' => null,
                'email' => 'admin@peradi.or.id', 'no_wa' => '082222222222',
                'password_hash' => password_hash('Admin@2026', PASSWORD_ARGON2ID),
                'role' => 'admin', 'pbh_cabang_id' => $dpn->id, 'status' => 'aktif',
                'email_verified_at' => $now, 'created_at' => $now, 'updated_at' => $now,
            ],
            // Cabang (DPC Jakarta)
            [
                'nia' => null, 'nama_lengkap' => 'Pengurus PBH Jakarta', 'gelar' => null,
                'email' => 'cabang.jakarta@peradi.or.id', 'no_wa' => '083333333333',
                'password_hash' => password_hash('Cabang@2026', PASSWORD_ARGON2ID),
                'role' => 'cabang', 'pbh_cabang_id' => $jkt->id, 'status' => 'aktif',
                'email_verified_at' => $now, 'created_at' => $now, 'updated_at' => $now,
            ],
            // Advokat demo
            [
                'nia' => '98.12345', 'nama_lengkap' => 'Budi Santoso', 'gelar' => 'S.H.',
                'email' => 'advokat@peradi.or.id', 'no_wa' => '084444444444',
                'password_hash' => password_hash('Advokat@2026', PASSWORD_ARGON2ID),
                'role' => 'advokat', 'pbh_cabang_id' => $jkt->id, 'status' => 'aktif',
                'email_verified_at' => $now, 'created_at' => $now, 'updated_at' => $now,
            ],
        ];

        $this->db->table('users')->insertBatch($users);
    }
}