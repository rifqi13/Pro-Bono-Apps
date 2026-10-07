<?php
namespace App\Controllers\Advokat;

use App\Controllers\BaseController;
use App\Models\PengajuanModel;
use CodeIgniter\Database\BaseConnection;

class DashboardController extends BaseController
{
    protected BaseConnection $db;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        // Inject DB connection
        $this->db = \Config\Database::connect();
    }

    public function index()
{
    $this->db = \Config\Database::connect();
    $userId = session()->get('user_id');
    $filters = [
        'tahun'         => $this->request->getGet('tahun') ?? date('Y'),
        'status'        => $this->request->getGet('status'),
        'jenis_perkara' => $this->request->getGet('jenis_perkara'),
    ];

    $model = new PengajuanModel();

    // Statistik dasar
    $stats = $model->countByStatus($userId);
    $totalPengajuan = array_sum($stats);
    $totalApproved  = $stats['approved'] ?? 0;
    $progressPersen = $totalPengajuan > 0 ? round(($totalApproved / $totalPengajuan) * 100) : 0;

    $progressColor = 'danger';
    if ($progressPersen >= 80)      $progressColor = 'success';
    elseif ($progressPersen >= 50)  $progressColor = 'warning';

    // Riwayat pengajuan
    $pengajuan = $model->getByAdvokat($userId, $filters);

    $tahunList = $model->select('tahun')->where('user_id', $userId)
                       ->groupBy('tahun')->orderBy('tahun', 'DESC')->findAll();

    // ============================================================
    // REKAPITULASI SERTIFIKAT (ganti 3 chart)
    // ============================================================
    $rekapSertifikat = $this->getRekapSertifikat($userId);

    return view('advokat/dashboard', [
        'title'            => 'Dashboard Advokat',
        'stats'            => $stats,
        'totalPengajuan'   => $totalPengajuan,
        'totalApproved'    => $totalApproved,
        'progressPersen'   => $progressPersen,
        'progressColor'    => $progressColor,
        'pengajuan'        => $pengajuan,
        'filters'          => $filters,
        'tahunList'        => $tahunList,
        'rekapSertifikat'  => $rekapSertifikat,   // ← baru
    ]);
}

/**
 * Hitung rekap sertifikat per tahun.
 * Aturan: 50 jam penanganan (perkara approved) = 1 sertifikat.
 *
 * Return array per tahun:
 *   [
 *     'tahun'            => 2025,
 *     'total_jam'        => 45,
 *     'sertifikat_terbit'=> 0,
 *     'sisa_jam'         => 5,        // sisa menuju sertifikat berikutnya
 *     'target_jam'       => 50,       // target per sertifikat
 *     'progress_persen'  => 90,
 *     'status'           => 'in_progress' | 'siap_dibuat' | 'terbit',
 *     'jumlah_perkara'   => 3,
 *   ]
 */
private function getRekapSertifikat(int $userId): array
{
    $JAM_PER_SERTIFIKAT = 50;

    // Ambil SUM(durasi_jam) per tahun — hanya yang approved & durasi_jam terisi
    $rows = $this->db->table('pengajuan')
        ->select('tahun, COUNT(*) as jumlah_perkara, COALESCE(SUM(durasi_jam), 0) as total_jam')
        ->where('user_id', $userId)
        ->where('status', 'approved')
        ->where('durasi_jam IS NOT NULL')     // hanya yang sudah diisi verifikator
        ->groupBy('tahun')
        ->orderBy('tahun', 'ASC')
        ->get()->getResultArray();

    if (empty($rows)) return [];

    $rekap = [];
    foreach ($rows as $r) {
        $tahun         = (int) $r['tahun'];
        $jumlahPerkara = (int) $r['jumlah_perkara'];
        $totalJam      = (int) $r['total_jam'];

        $sertifikatTerbit = (int) floor($totalJam / $JAM_PER_SERTIFIKAT);
        $sisaJam          = $JAM_PER_SERTIFIKAT - ($totalJam % $JAM_PER_SERTIFIKAT);
        if ($sisaJam === $JAM_PER_SERTIFIKAT) $sisaJam = 0;

        $progressPersen = round((($totalJam % $JAM_PER_SERTIFIKAT) / $JAM_PER_SERTIFIKAT) * 100);

        if ($totalJam >= $JAM_PER_SERTIFIKAT && $totalJam % $JAM_PER_SERTIFIKAT === 0) {
            $status = 'siap_dibuat';
        } elseif ($totalJam >= $JAM_PER_SERTIFIKAT) {
            $status = 'terbit';
        } else {
            $status = 'in_progress';
        }

        $barColor = match($status) {
            'terbit'      => 'success',
            'siap_dibuat' => 'warning',
            default       => 'primary',
        };

        $numberColor = match($status) {
            'terbit'      => '#10b981',
            'siap_dibuat' => '#f59e0b',
            default       => '#0a1f3c',
        };

        $label = match($status) {
            'siap_dibuat' => 'Siap Dibuat',
            'terbit'      => 'Sertifikat Terbit',
            default       => 'Sertifikat',
        };

        $textSisa = ($status === 'siap_dibuat')
            ? 'Target Jam Terpenuhi'
            : 'Sisa: ' . $sisaJam . ' Jam menuju sertifikat ke-' . ($sertifikatTerbit + 1);

        $rekap[] = [
            'tahun'             => $tahun,
            'total_jam'         => $totalJam,
            'sertifikat_terbit' => $sertifikatTerbit,
            'sisa_jam'          => $sisaJam,
            'progress_persen'   => $progressPersen,
            'status'            => $status,
            'bar_color'         => $barColor,
            'number_color'      => $numberColor,
            'label'             => $label,
            'text_sisa'         => $textSisa,
            'jumlah_perkara'    => $jumlahPerkara,
        ];
    }

    return $rekap;
}

    private function getMonthlyChart(int $userId, int $tahun): array
    {
        $rows = $this->db->table('pengajuan')
            ->select("MONTH(created_at) as bulan, COUNT(*) as total")
            ->where('user_id', $userId)
            ->where('YEAR(created_at)', $tahun)
            ->where('status !=', 'draft')
            ->groupBy('MONTH(created_at)')
            ->orderBy('bulan', 'ASC')
            ->get()->getResultArray();

        $data = array_fill(1, 12, 0);
        foreach ($rows as $r) $data[(int)$r['bulan']] = (int)$r['total'];
        return array_values($data);
    }

    private function getTrendChart(int $userId): array
    {
        $rows = $this->db->table('pengajuan')
            ->select("DATE(created_at) as tanggal, COUNT(*) as total")
            ->where('user_id', $userId)
            ->where('created_at >=', date('Y-m-d', strtotime('-30 days')))
            ->groupBy('DATE(created_at)')
            ->orderBy('tanggal', 'ASC')
            ->get()->getResultArray();

        $labels = [];
        $values = [];
        $map = array_column($rows, 'total', 'tanggal');
        for ($i = 29; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $labels[] = date('d M', strtotime($d));
            $values[] = (int) ($map[$d] ?? 0);
        }
        return ['labels' => $labels, 'values' => $values];
    }
}