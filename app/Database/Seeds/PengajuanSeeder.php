<?php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;

class PengajuanSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create('id_ID');
        $advokats = $this->db->table('users')->where('role','advokat')->get()->getResultArray();
        $tahun = date('Y');
        $counter = [];

        for ($i = 0; $i < 50; $i++) {
            $adv = $advokats[array_rand($advokats)];
            $cabangId = $adv['pbh_cabang_id'];
            $cabang = $this->db->table('pbh_cabang')->where('id',$cabangId)->get()->getRow();

            // Counter per cabang
            $key = $tahun . '-' . $cabangId;
            if (!isset($counter[$key])) $counter[$key] = 0;
            $counter[$key]++;
            $noReg = sprintf('#PB-%s-%s-%04d', $tahun, $cabang->kode, $counter[$key]);

            $jenis = $faker->randomElement(['litigasi','non-litigasi']);
            $status = $faker->randomElement(['submitted','review','approved','approved','rejected']);

            $pengajuanId = $this->db->table('pengajuan')->insert([
                'no_registrasi'      => $noReg,
                'user_id'            => $adv['id'],
                'pbh_cabang_id'      => $cabangId,
                'jenis_layanan'      => $jenis,
                'jenis_non_litigasi' => $jenis === 'non-litigasi' ? $faker->randomElement(['seminar','penyuluhan','pendampingan']) : null,
                'tahun'              => $tahun,
                'status'             => $status,
                'catatan_verifikator'=> $status === 'rejected' ? 'Dokumen tidak lengkap.' : null,
                'verified_at'        => in_array($status, ['approved','rejected']) ? $faker->dateTimeThisYear->format('Y-m-d H:i:s') : null,
                'submitted_at'       => $faker->dateTimeThisYear->format('Y-m-d H:i:s'),
                'created_at'         => $faker->dateTimeThisYear->format('Y-m-d H:i:s'),
                'updated_at'         => date('Y-m-d H:i:s'),
            ], true);

            // Penerima manfaat (1-3 orang)
            $jumlahPm = rand(1, 3);
            for ($j = 0; $j < $jumlahPm; $j++) {
                $usia = rand(5, 70);
                $kategori = $usia < 18 ? 'anak' : 'dewasa';
                $this->db->table('penerima_manfaat')->insert([
                    'pengajuan_id'  => $pengajuanId,
                    'usia'          => $usia,
                    'kategori'      => $kategori,
                    'nama'          => $kategori === 'anak' ? $faker->name : null,
                    'jenis_kelamin' => $kategori === 'dewasa' ? $faker->randomElement(['L','P']) : null,
                    'pekerjaan'     => $kategori === 'dewasa' ? $faker->jobTitle : null,
                    'file_ktp'      => 'uploads/ktp/dummy_' . uniqid() . '.pdf',
                    'created_at'    => date('Y-m-d H:i:s'),
                ]);
            }

            // Detail sesuai jenis
            if ($jenis === 'litigasi') {
                $this->db->table('detail_litigasi')->insert([
                    'pengajuan_id' => $pengajuanId,
                    'jenis_perkara'=> $faker->randomElement(['perdata_umum','pidana_umum','tata_usaha_negara','perdata_perburuhan']),
                    'no_perkara'   => 'No. ' . rand(100,999) . '/Pdt.G/' . $tahun . '/PN.Jkt.Pst',
                    'pengadilan'   => 'PN Jakarta Pusat',
                    'created_at'   => date('Y-m-d H:i:s'),
                ]);
            } elseif ($jenis === 'non-litigasi') {
                $sub = $this->db->table('pengajuan')->where('id',$pengajuanId)->get()->getRow()->jenis_non_litigasi;
                if ($sub === 'pendampingan') {
                    $this->db->table('detail_pendampingan')->insert([
                        'pengajuan_id' => $pengajuanId,
                        'lokasi'       => $faker->address,
                        'created_at'   => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    $this->db->table('detail_seminar')->insert([
                        'pengajuan_id'  => $pengajuanId,
                        'nama_acara'    => 'Sosialisasi Hukum ' . $faker->word,
                        'tanggal_acara' => $faker->date,
                        'waktu_acara'   => '09:00:00',
                        'tempat_acara'  => $faker->city,
                        'peserta'       => $faker->randomElement(['Masyarakat Umum','Mahasiswa','Pelajar']),
                        'jumlah_peserta'=> rand(20, 200),
                        'created_at'    => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }
    }
}