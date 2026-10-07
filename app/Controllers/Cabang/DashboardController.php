<?php
namespace App\Controllers\Cabang;

use App\Controllers\BaseController;
use App\Libraries\ActivityLogger;
use App\Libraries\CabangScope;
use App\Libraries\ExcelExporter;
use App\Libraries\PdfExporter;
use App\Models\PengajuanModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $cabangId = CabangScope::id();
        $cabang = model('PbhCabangModel')->find($cabangId);

        $db = \Config\Database::connect();

        // ---------- Statistik Cabang ----------
        $pengajuanModel = new PengajuanModel();
        $stats = [
            'total_advokat'    => model('UserModel')->where('role', 'advokat')
                                    ->where('pbh_cabang_id', $cabangId)
                                    ->where('status', 'aktif')
                                    ->countAllResults(),
            'total_advokat_all'=> model('UserModel')->where('role', 'advokat')
                                    ->where('pbh_cabang_id', $cabangId)
                                    ->countAllResults(),
            'total_pengajuan'  => $pengajuanModel->where('pbh_cabang_id', $cabangId)
                                    ->where('status !=', 'draft')->countAllResults(),
            'approved'         => $pengajuanModel->where('pbh_cabang_id', $cabangId)
                                    ->where('status', 'approved')->countAllResults(),
            'review'           => $pengajuanModel->where('pbh_cabang_id', $cabangId)
                                    ->whereIn('status', ['submitted','review'])->countAllResults(),
            'rejected'         => $pengajuanModel->where('pbh_cabang_id', $cabangId)
                                    ->where('status', 'rejected')->countAllResults(),
            'warning'          => $pengajuanModel->where('pbh_cabang_id', $cabangId)
                                    ->where('has_warning', 1)->countAllResults(),
        ];

        // ---------- Chart Bulanan ----------
        $chartMonthly = $this->getMonthlyChart($cabangId);

        // ---------- Chart Distribusi Jenis Layanan ----------
        $chartJenis = $this->getDistribusiJenisLayanan($cabangId);

        // ---------- Chart Top Advokat (di cabang) ----------
        $topAdvokat = $db->table('pengajuan')
            ->select('users.nama_lengkap, users.nia, COUNT(pengajuan.id) as total,
                      SUM(CASE WHEN pengajuan.status="approved" THEN 1 ELSE 0 END) as approved')
            ->join('users', 'users.id = pengajuan.user_id')
            ->where('pengajuan.pbh_cabang_id', $cabangId)
            ->where('pengajuan.status !=', 'draft')
            ->groupBy('pengajuan.user_id')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->get()->getResultArray();

        // ---------- Riwayat Pengajuan Terbaru ----------
        $pengajuan = $db->table('pengajuan')
            ->select('pengajuan.*, users.nama_lengkap as advokat_nama, users.nia as advokat_nia')
            ->join('users', 'users.id = pengajuan.user_id')
            ->where('pengajuan.pbh_cabang_id', $cabangId)
            ->where('pengajuan.status !=', 'draft')
            ->orderBy('pengajuan.created_at', 'DESC')
            ->limit(10)
            ->get()->getResultArray();

        return view('cabang/dashboard', [
            'title'        => 'Dashboard PBH Cabang',
            'cabang'       => $cabang,
            'stats'        => $stats,
            'chartMonthly' => $chartMonthly,
            'chartJenis'   => $chartJenis,
            'topAdvokat'   => $topAdvokat,
            'pengajuan'    => $pengajuan,
        ]);
    }

    public function advokatList()
    {
        $cabangId = CabangScope::id();
        $filters = [
            'status' => $this->request->getGet('status'),
            'q'      => $this->request->getGet('q'),
        ];

        $model = model('UserModel');
        $builder = $model->where('role', 'advokat')
                         ->where('pbh_cabang_id', $cabangId)
                         ->orderBy('created_at', 'DESC');

        if (!empty($filters['status'])) $builder->where('status', $filters['status']);
        if (!empty($filters['q'])) {
            $builder->groupStart()
                ->like('nama_lengkap', $filters['q'])
                ->orLike('nia', $filters['q'])
                ->orLike('email', $filters['q'])
            ->groupEnd();
        }

        $list = $builder->paginate(20);

        return view('cabang/advokat_list', [
            'title'   => 'Daftar Advokat Cabang',
            'list'    => $list,
            'pager'   => $model->pager,
            'filters' => $filters,
        ]);
    }

    public function pengajuanList()
    {
        $cabangId = CabangScope::id();
        $filters = [
            'status'        => $this->request->getGet('status'),
            'jenis_layanan' => $this->request->getGet('jenis_layanan'),
            'tahun'         => $this->request->getGet('tahun'),
            'q'             => $this->request->getGet('q'),
        ];

        $db = \Config\Database::connect();
        $builder = $db->table('pengajuan')
            ->select('pengajuan.*, users.nama_lengkap as advokat_nama, users.nia as advokat_nia')
            ->join('users', 'users.id = pengajuan.user_id')
            ->where('pengajuan.pbh_cabang_id', $cabangId)
            ->where('pengajuan.status !=', 'draft')
            ->orderBy('pengajuan.created_at', 'DESC');

        if (!empty($filters['status']))        $builder->where('pengajuan.status', $filters['status']);
        if (!empty($filters['jenis_layanan'])) $builder->where('pengajuan.jenis_layanan', $filters['jenis_layanan']);
        if (!empty($filters['tahun']))         $builder->where('pengajuan.tahun', $filters['tahun']);
        if (!empty($filters['q'])) {
            $builder->groupStart()
                ->like('pengajuan.no_registrasi', $filters['q'])
                ->orLike('users.nama_lengkap', $filters['q'])
                ->orLike('users.nia', $filters['q'])
            ->groupEnd();
        }

        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 20;
        $total = $builder->countAllResults(false);
        $list = $builder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();

        $pager = \Config\Services::pager();
        $pager->makeLinks($page, $perPage, $total, 'default_full');

        return view('cabang/pengajuan_list', [
            'title'   => 'Daftar Pengajuan Cabang',
            'list'    => $list,
            'pager'   => $pager,
            'filters' => $filters,
        ]);
    }

    public function pengajuanDetail(int $id)
    {
        $pengajuan = model('PengajuanModel')->getWithRelations($id);
        if (!$pengajuan) {
            return redirect()->to(base_url('cabang/pengajuan'))->with('error', 'Pengajuan tidak ditemukan.');
        }

        // AUTHORIZE: hanya boleh akses cabangnya sendiri
        CabangScope::authorize($pengajuan, 'pbh_cabang_id');

        // Log akses (read-only audit)
        ActivityLogger::log('cabang_view_pengajuan', 'cabang',
            'Cabang melihat pengajuan: ' . $pengajuan['no_registrasi'],
            ['subject_type' => 'Pengajuan', 'subject_id' => $id]);

        return view('cabang/pengajuan_detail', [
            'title'     => 'Detail Pengajuan (Read-Only)',
            'pengajuan' => $pengajuan,
        ]);
    }

    // ============================================================
    // EXPORT — hanya data cabangnya sendiri
    // ============================================================
    public function exportForm()
    {
        return view('cabang/export_form', ['title' => 'Export Laporan Cabang']);
    }

    public function exportExcel()
    {
        $params = $this->getExportParams();
        $data = $this->getExportData($params);

        $cabang = model('PbhCabangModel')->find(CabangScope::id());
        $filepath = (new ExcelExporter())->export($data, [
            'judul'   => 'Laporan Data Pro Bono — ' . $cabang['nama'],
            'periode' => $this->describePeriode($params),
        ]);

        ActivityLogger::log('cabang_export_excel', 'cabang',
            'Cabang export Excel: ' . count($data) . ' baris');

        return $this->response->download($filepath, null)->setFileName(basename($filepath));
    }

    public function exportPdf()
    {
        $params = $this->getExportParams();
        $data = $this->getExportData($params);

        $cabang = model('PbhCabangModel')->find(CabangScope::id());
        $filepath = (new PdfExporter())->export($data, [
            'judul'   => 'Laporan Data Pro Bono — ' . $cabang['nama'],
            'periode' => $this->describePeriode($params),
        ]);

        ActivityLogger::log('cabang_export_pdf', 'cabang',
            'Cabang export PDF: ' . count($data) . ' baris');

        return $this->response->download($filepath, null)->setFileName(basename($filepath));
    }

    private function getExportParams(): array
    {
        return [
            'dari'   => $this->request->getPost('dari'),
            'sampai' => $this->request->getPost('sampai'),
            'tahun'  => $this->request->getPost('tahun'),
            'status' => $this->request->getPost('status'),
        ];
    }

    private function getExportData(array $params): array
    {
        $cabangId = CabangScope::id();
        $db = \Config\Database::connect();

        $builder = $db->table('pengajuan')
            ->select('pengajuan.*, users.nama_lengkap as advokat_nama, users.nia as advokat_nia,
                      users.gelar as advokat_gelar, pbh_cabang.nama as cabang_nama,
                      verifikator.nama_lengkap as verifikator_nama')
            ->join('users', 'users.id = pengajuan.user_id')
            ->join('pbh_cabang', 'pbh_cabang.id = pengajuan.pbh_cabang_id')
            ->join('users as verifikator', 'verifikator.id = pengajuan.verified_by', 'left')
            ->where('pengajuan.pbh_cabang_id', $cabangId) // <-- SCOPE
            ->where('pengajuan.status !=', 'draft')
            ->orderBy('pengajuan.submitted_at', 'DESC');

        if (!empty($params['status'])) $builder->where('pengajuan.status', $params['status']);
        if (!empty($params['tahun']))  $builder->where('pengajuan.tahun', $params['tahun']);
        if (!empty($params['dari']))   $builder->where('pengajuan.submitted_at >=', $params['dari'] . ' 00:00:00');
        if (!empty($params['sampai'])) $builder->where('pengajuan.submitted_at <=', $params['sampai'] . ' 23:59:59');

        $rows = $builder->get()->getResultArray();

        // Enrich dengan relasi
        $pmModel = model('PenerimaManfaatModel');
        $dokModel = model('DokumenPengajuanModel');
        $fotoModel = model('FotoKegiatanModel');
        $litigasiModel = model('DetailLitigasiModel');
        $seminarModel = model('DetailSeminarModel');
        $pendampinganModel = model('DetailPendampinganModel');

        foreach ($rows as &$row) {
            $id = (int) $row['id'];
            $row['penerima_manfaat'] = $pmModel->where('pengajuan_id', $id)->findAll();
            $row['dokumen']          = $dokModel->where('pengajuan_id', $id)->findAll();
            $row['foto']             = $fotoModel->where('pengajuan_id', $id)->findAll();

            if ($row['jenis_layanan'] === 'litigasi') {
                $row['detail'] = $litigasiModel->where('pengajuan_id', $id)->first();
            } elseif (($row['jenis_non_litigasi'] ?? '') === 'pendampingan') {
                $row['detail'] = $pendampinganModel->where('pengajuan_id', $id)->first();
            } else {
                $row['detail'] = $seminarModel->where('pengajuan_id', $id)->first();
            }
        }
        return $rows;
    }

    private function describePeriode(array $params): string
    {
        $parts = [];
        if (!empty($params['dari']) && !empty($params['sampai'])) {
            $parts[] = date('d/m/Y', strtotime($params['dari'])) . ' s/d ' . date('d/m/Y', strtotime($params['sampai']));
        } elseif (!empty($params['tahun'])) {
            $parts[] = 'Tahun ' . $params['tahun'];
        } else {
            $parts[] = 'Semua Periode';
        }
        return implode(' | ', $parts);
    }

    private function getMonthlyChart(int $cabangId): array
    {
        $db = \Config\Database::connect();
        $rows = $db->table('pengajuan')
            ->select("DATE_FORMAT(created_at, '%Y-%m') as bulan, COUNT(*) as total")
            ->where('pbh_cabang_id', $cabangId)
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

    private function getDistribusiJenisLayanan(int $cabangId): array
{
    $db = \Config\Database::connect();

    // 1. LITIGASI — hitung per jenis_perkara
    $litigasi = $db->table('pengajuan')
        ->select('detail_litigasi.jenis_perkara, COUNT(*) as total')
        ->join('detail_litigasi', 'detail_litigasi.pengajuan_id = pengajuan.id', 'inner')
        ->where('pengajuan.pbh_cabang_id', $cabangId)
        ->where('pengajuan.jenis_layanan', 'litigasi')
        ->where('pengajuan.status !=', 'draft')
        ->groupBy('detail_litigasi.jenis_perkara')
        ->get()->getResultArray();

    // 2. NON-LITIGASI — hitung per jenis_non_litigasi
    $nonLitigasi = $db->table('pengajuan')
        ->select('jenis_non_litigasi, COUNT(*) as total')
        ->where('pbh_cabang_id', $cabangId)
        ->where('jenis_layanan', 'non-litigasi')
        ->where('jenis_non_litigasi IS NOT NULL')
        ->where('status !=', 'draft')
        ->groupBy('jenis_non_litigasi')
        ->get()->getResultArray();

    // 3. Label mapping
    $labelPerkara = [
        'perdata_perburuhan' => 'Perdata Perburuhan',
        'perdata_pertanahan' => 'Perdata Pertanahan',
        'perdata_keluarga'   => 'Perdata Keluarga',
        'perdata_umum'       => 'Perdata Umum',
        'pidana_khusus'      => 'Pidana Khusus',
        'pidana_umum'        => 'Pidana Umum',
        'tata_usaha_negara'  => 'Tata Usaha Negara',
    ];
    $labelNonLitigasi = [
        'seminar'      => 'Seminar',
        'penyuluhan'   => 'Penyuluhan',
        'pendampingan' => 'Pendampingan',
    ];

    // 4. Gabung hasil
    $result = [];
    foreach ($litigasi as $l) {
        $jp = $l['jenis_perkara'];
        $result[] = [
            'label'    => 'Litigasi - ' . ($labelPerkara[$jp] ?? ucwords(str_replace('_', ' ', $jp))),
            'total'    => (int) $l['total'],
            'kategori' => 'litigasi',
        ];
    }
    foreach ($nonLitigasi as $n) {
        $jn = $n['jenis_non_litigasi'];
        $result[] = [
            'label'    => 'Non-Litigasi - ' . ($labelNonLitigasi[$jn] ?? ucfirst($jn)),
            'total'    => (int) $n['total'],
            'kategori' => 'non-litigasi',
        ];
    }

    // 5. Urutkan: Litigasi dulu, lalu Non-Litigasi. Dalam grup, urut berdasarkan total descending.
    usort($result, function ($a, $b) {
        if ($a['kategori'] !== $b['kategori']) {
            return $a['kategori'] === 'litigasi' ? -1 : 1;
        }
        return $b['total'] <=> $a['total'];
    });

    return $result;
}
}