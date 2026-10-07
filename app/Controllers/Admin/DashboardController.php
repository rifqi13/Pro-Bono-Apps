<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PengajuanModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $model = new PengajuanModel();
        $db = \Config\Database::connect();

        // Data baru untuk chart
        $dataPerCabang = $this->getDataPerCabang();
        $dataKategoriPerkara = $this->getDataKategoriPerkara();

        // Total keseluruhan
        $totalAdvokat = array_sum(array_column($dataPerCabang, 'total_advokat'));
        $totalMasuk = array_sum(array_column($dataPerCabang, 'total_masuk'));
        $totalSukses = array_sum(array_column($dataPerCabang, 'total_sukses'));
        $totalTingkatSukses = $totalMasuk > 0 ? round(($totalSukses / $totalMasuk) * 100) : 0;
        
        // Statistik global
        $stats = [
            'total'      => $model->where('status !=', 'draft')->countAllResults(),
            'submitted'  => $model->where('status', 'submitted')->countAllResults(),
            'review'     => $model->where('status', 'review')->countAllResults(),
            'approved'   => $model->where('status', 'approved')->countAllResults(),
            'rejected'   => $model->where('status', 'rejected')->countAllResults(),
            'warning'    => $model->where('has_warning', 1)->countAllResults(),
        ];

        // Statistik per cabang
        $perCabang = $db->table('pengajuan')
            ->select('pbh_cabang.nama as cabang, COUNT(pengajuan.id) as total, 
                      SUM(CASE WHEN pengajuan.status="approved" THEN 1 ELSE 0 END) as approved,
                      SUM(CASE WHEN pengajuan.status="rejected" THEN 1 ELSE 0 END) as rejected,
                      SUM(CASE WHEN pengajuan.status IN ("submitted","review") THEN 1 ELSE 0 END) as pending')
            ->join('pbh_cabang', 'pbh_cabang.id = pengajuan.pbh_cabang_id')
            ->where('pengajuan.status !=', 'draft')
            ->groupBy('pbh_cabang.id')
            ->orderBy('total', 'DESC')
            ->get()->getResultArray();

        // Chart bulanan (12 bulan terakhir, semua cabang)
        $chartMonthly = $this->getMonthlyChart();

        // Chart pie distribusi status
        $chartStatus = [
            'approved'  => $stats['approved'],
            'review'    => $stats['review'],
            'submitted' => $stats['submitted'],
            'rejected'  => $stats['rejected'],
        ];

        // Chart bar per cabang
        $chartCabang = [
            'labels'  => array_column($perCabang, 'cabang'),
            'data'    => array_map('intval', array_column($perCabang, 'total')),
            'approved'=> array_map('intval', array_column($perCabang, 'approved')),
        ];

        // Top 10 advokat
        $topAdvokat = $db->table('pengajuan')
            ->select('users.nama_lengkap, users.nia, pbh_cabang.nama as cabang, COUNT(pengajuan.id) as total')
            ->join('users', 'users.id = pengajuan.user_id')
            ->join('pbh_cabang', 'pbh_cabang.id = pengajuan.pbh_cabang_id')
            ->where('pengajuan.status', 'approved')
            ->groupBy('pengajuan.user_id')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->get()->getResultArray();

        // Pending verifikasi (5 terbaru)
        $pendingList = $model->select('pengajuan.*, users.nama_lengkap as advokat_nama, pbh_cabang.nama as cabang_nama')
            ->join('users', 'users.id = pengajuan.user_id')
            ->join('pbh_cabang', 'pbh_cabang.id = pengajuan.pbh_cabang_id')
            ->whereIn('pengajuan.status', ['submitted','review'])
            ->orderBy('pengajuan.submitted_at', 'ASC')
            ->limit(5)->findAll();

        return view('admin/dashboard', [
            'title'       => 'Dashboard Admin',
            'stats'       => $stats,
            'perCabang'   => $perCabang,
            'chartMonthly'=> $chartMonthly,
            'chartStatus' => $chartStatus,
            'chartCabang' => $chartCabang,
            'topAdvokat'  => $topAdvokat,
            'pendingList' => $pendingList,
            'dataPerCabang'      => $dataPerCabang,
            'dataKategoriPerkara'=> $dataKategoriPerkara,
            'totalAdvokat'       => $totalAdvokat,
            'totalMasuk'         => $totalMasuk,
            'totalSukses'        => $totalSukses,
            'totalTingkatSukses' => $totalTingkatSukses,
        ]);
    }

    private function getMonthlyChart(): array
    {
        $db = \Config\Database::connect();
        $rows = $db->table('pengajuan')
            ->select("DATE_FORMAT(created_at, '%Y-%m') as bulan, COUNT(*) as total")
            ->where('status !=', 'draft')
            ->where('created_at >=', date('Y-m-01', strtotime('-11 months')))
            ->groupBy("DATE_FORMAT(created_at, '%Y-%m')")
            ->orderBy('bulan', 'ASC')
            ->get()->getResultArray();

        $labels = [];
        $values = [];
        $map = array_column($rows, 'total', 'bulan');
        for ($i = 11; $i >= 0; $i--) {
            $d = date('Y-m', strtotime("-{$i} months"));
            $labels[] = date('M Y', strtotime($d . '-01'));
            $values[] = (int) ($map[$d] ?? 0);
        }
        return ['labels' => $labels, 'values' => $values];
    }

    private function getDataPerCabang(): array
{
    $db = \Config\Database::connect();

    // 1. Ambil semua cabang DPC yang aktif
    $cabangList = $db->table('pbh_cabang')
        ->where('tipe', 'DPC')
        ->where('is_active', 1)
        ->orderBy('nama', 'ASC')
        ->get()->getResultArray();

    $result = [];

    foreach ($cabangList as $cabang) {
        $cabangId = (int) $cabang['id'];

        // Jumlah advokat aktif di cabang ini
        $totalAdvokat = $db->table('users')
            ->where('role', 'advokat')
            ->where('pbh_cabang_id', $cabangId)
            ->where('status', 'aktif')
            ->countAllResults();

        // Pengaduan masuk (semua pengajuan bukan draft)
        $totalMasuk = $db->table('pengajuan')
            ->where('pbh_cabang_id', $cabangId)
            ->where('status !=', 'draft')
            ->countAllResults();

        // Pengaduan sukses (approved)
        $totalSukses = $db->table('pengajuan')
            ->where('pbh_cabang_id', $cabangId)
            ->where('status', 'approved')
            ->countAllResults();

        // Tingkat sukses = sukses / masuk × 100
        $tingkatSukses = $totalMasuk > 0
            ? round(($totalSukses / $totalMasuk) * 100)
            : 0;

        // Status: Sangat Baik (≥80%), Baik (60-79%), Sedang (40-59%), Rendah (<40%)
        $status = 'Rendah';
        $statusBadge = 'danger';
        if ($tingkatSukses >= 80) {
            $status = 'Sangat Baik'; $statusBadge = 'success';
        } elseif ($tingkatSukses >= 60) {
            $status = 'Baik'; $statusBadge = 'primary';
        } elseif ($tingkatSukses >= 40) {
            $status = 'Sedang'; $statusBadge = 'warning';
        }

        $result[] = [
            'id'             => $cabangId,
            'nama'           => $cabang['nama'],
            'kode'           => $cabang['kode'],
            'total_advokat'  => $totalAdvokat,
            'total_masuk'    => $totalMasuk,
            'total_sukses'   => $totalSukses,
            'tingkat_sukses' => $tingkatSukses,
            'status'         => $status,
            'status_badge'   => $statusBadge,
        ];
    }

    // Urutkan berdasarkan total advokat DESC
    usort($result, fn($a, $b) => $b['total_advokat'] <=> $a['total_advokat']);

    return $result;
}

private function getDataKategoriPerkara(): array
{
    $db = \Config\Database::connect();

    // ====================================================
    // 1. LITIGASI — hitung per jenis_perkara
    // ====================================================
    $litigasiRows = $db->table('pengajuan')
        ->select('detail_litigasi.jenis_perkara, COUNT(*) as total')
        ->join('detail_litigasi', 'detail_litigasi.pengajuan_id = pengajuan.id', 'inner')
        ->where('pengajuan.status !=', 'draft')
        ->where('pengajuan.jenis_layanan', 'litigasi')
        ->groupBy('detail_litigasi.jenis_perkara')
        ->get()->getResultArray();

    // ====================================================
    // 2. NON-LITIGASI — hitung per jenis_non_litigasi
    // ====================================================
    $nonLitigasiRows = $db->table('pengajuan')
        ->select('jenis_non_litigasi, COUNT(*) as total')
        ->where('jenis_layanan', 'non-litigasi')
        ->where('jenis_non_litigasi IS NOT NULL')
        ->where('status !=', 'draft')
        ->groupBy('jenis_non_litigasi')
        ->get()->getResultArray();

    // ====================================================
    // 3. Mapping label & warna
    // ====================================================
    // Warna Litigasi: biru (dari gelap → terang)
    $litigasiMap = [
        'perdata_perburuhan' => ['label' => 'Perdata Perburuhan', 'warna' => '#0a1f3c'],
        'perdata_pertanahan' => ['label' => 'Perdata Pertanahan', 'warna' => '#1e3a8a'],
        'perdata_keluarga'   => ['label' => 'Perdata Keluarga',   'warna' => '#1862b5'],
        'perdata_umum'       => ['label' => 'Perdata Umum',       'warna' => '#3b82f6'],
        'pidana_khusus'      => ['label' => 'Pidana Khusus',      'warna' => '#60a5fa'],
        'pidana_umum'        => ['label' => 'Pidana Umum',        'warna' => '#93c5fd'],
        'tata_usaha_negara'  => ['label' => 'Tata Usaha Negara',  'warna' => '#bfdbfe'],
    ];

    // Warna Non-Litigasi: hijau (dari gelap → terang)
    $nonLitigasiMap = [
        'seminar'      => ['label' => 'Seminar',      'warna' => '#065f46'],
        'penyuluhan'   => ['label' => 'Penyuluhan',   'warna' => '#10b981'],
        'pendampingan' => ['label' => 'Pendampingan', 'warna' => '#6ee7b7'],
    ];

    // ====================================================
    // 4. Gabung
    // ====================================================
    $allData = [];

    foreach ($litigasiRows as $r) {
        $jp = $r['jenis_perkara'];
        $allData[] = [
            'kode'      => $jp,
            'label'     => $litigasiMap[$jp]['label'] ?? ucwords(str_replace('_', ' ', $jp)),
            'warna'     => $litigasiMap[$jp]['warna'] ?? '#6b7280',
            'kategori'  => 'litigasi',
            'total'     => (int) $r['total'],
        ];
    }

    foreach ($nonLitigasiRows as $r) {
        $jn = $r['jenis_non_litigasi'];
        $allData[] = [
            'kode'      => $jn,
            'label'     => $nonLitigasiMap[$jn]['label'] ?? ucfirst($jn),
            'warna'     => $nonLitigasiMap[$jn]['warna'] ?? '#6b7280',
            'kategori'  => 'non-litigasi',
            'total'     => (int) $r['total'],
        ];
    }

    // ====================================================
    // 5. Hitung persentase & indikator
    // ====================================================
    $totalAll = array_sum(array_column($allData, 'total'));

    foreach ($allData as &$d) {
        $persen = $totalAll > 0 ? round(($d['total'] / $totalAll) * 100, 1) : 0;
        $d['persentase'] = $persen;

        // Indikator
        if ($persen >= 20) {
            $d['indikator'] = 'Tinggi';
            $d['indikator_badge'] = 'danger';
        } elseif ($persen >= 10) {
            $d['indikator'] = 'Sedang';
            $d['indikator_badge'] = 'warning';
        } else {
            $d['indikator'] = 'Rendah';
            $d['indikator_badge'] = 'info';
        }
    }
    unset($d);

    // ====================================================
    // 6. Urutkan: Litigasi dulu, lalu Non-Litigasi
    // ====================================================
    usort($allData, function ($a, $b) {
        if ($a['kategori'] !== $b['kategori']) {
            return $a['kategori'] === 'litigasi' ? -1 : 1;
        }
        return $b['total'] <=> $a['total'];
    });

    return $allData;
}
}