<?php
namespace App\Models;
use CodeIgniter\Model;

class PengajuanModel extends Model
{
    protected $table      = 'pengajuan';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'no_registrasi','user_id','pbh_cabang_id','jenis_layanan','jenis_non_litigasi',
        'tahun','status','catatan_verifikator','verified_by','verified_at','durasi_jam',
        'draft_data','submitted_at', 'has_warning', 'warning_count',
    ];
    protected $useTimestamps = true;

    public function getWithRelations(int $id): ?array
    {
        $p = $this->select('pengajuan.*, users.nama_lengkap as advokat_nama, users.nia as advokat_nia, users.gelar as advokat_gelar, pbh_cabang.nama as cabang_nama, pbh_cabang.kode as cabang_kode')
                  ->join('users', 'users.id = pengajuan.user_id')
                  ->join('pbh_cabang', 'pbh_cabang.id = pengajuan.pbh_cabang_id')
                  ->where('pengajuan.id', $id)
                  ->first();
        if (!$p) return null;

        $p['penerima_manfaat'] = model('PenerimaManfaatModel')->where('pengajuan_id', $id)->findAll();
        $p['dokumen'] = model('DokumenPengajuanModel')->where('pengajuan_id', $id)->findAll();
        $p['foto'] = model('FotoKegiatanModel')->where('pengajuan_id', $id)->orderBy('urutan')->findAll();

        if ($p['jenis_layanan'] === 'litigasi') {
            $p['detail'] = model('DetailLitigasiModel')->where('pengajuan_id', $id)->first();
        } elseif ($p['jenis_non_litigasi'] === 'pendampingan') {
            $p['detail'] = model('DetailPendampinganModel')->where('pengajuan_id', $id)->first();
        } else {
            $p['detail'] = model('DetailSeminarModel')->where('pengajuan_id', $id)->first();
        }
        return $p;
    }

    public function generateNoRegistrasi(int $tahun, int $cabangId): string
    {
        $db = \Config\Database::connect();
        $builder = $db->table('pengajuan_counter');
        $row = $builder->where('tahun', $tahun)->where('pbh_cabang_id', $cabangId)->get()->getRow();

        if ($row) {
            $newNumber = $row->last_number + 1;
            $builder->where('id', $row->id)->update(['last_number' => $newNumber, 'updated_at' => date('Y-m-d H:i:s')]);
        } else {
            $newNumber = 1;
            $builder->insert(['tahun' => $tahun, 'pbh_cabang_id' => $cabangId, 'last_number' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
        }

        $cabang = model('PbhCabangModel')->find($cabangId);
        return sprintf('#PB-%s-%s-%04d', $tahun, $cabang['kode'], $newNumber);
    }

    public function getByAdvokat(int $userId, array $filters = []): array
    {
        $builder = $this->where('user_id', $userId)->orderBy('created_at', 'DESC');
        if (!empty($filters['tahun']))       $builder->where('tahun', $filters['tahun']);
        if (!empty($filters['status']))      $builder->where('status', $filters['status']);
        if (!empty($filters['jenis_perkara'])) {
            $builder->join('detail_litigasi', 'detail_litigasi.pengajuan_id = pengajuan.id', 'left')
                    ->where('detail_litigasi.jenis_perkara', $filters['jenis_perkara']);
        }
        return $builder->findAll();
    }

    public function getByCabang(int $cabangId): array
    {
        return $this->where('pbh_cabang_id', $cabangId)->orderBy('created_at', 'DESC')->findAll();
    }

    public function countByStatus(int $userId = null, int $cabangId = null): array
    {
        $builder = $this->select('status, COUNT(*) as total')->groupBy('status');
        if ($userId)   $builder->where('user_id', $userId);
        if ($cabangId) $builder->where('pbh_cabang_id', $cabangId);
        $rows = $builder->findAll();
        $result = ['draft'=>0,'submitted'=>0,'review'=>0,'approved'=>0,'rejected'=>0];
        foreach ($rows as $r) $result[$r['status']] = (int)$r['total'];
        return $result;
    }
}