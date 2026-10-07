<?php
namespace App\Controllers\Advokat;

use App\Controllers\BaseController;
use CodeIgniter\Database\BaseConnection;

class SertifikatController extends BaseController
{
    protected BaseConnection $db;

    /**
     * Init controller — inject DB connection.
     * Wajib supaya $this->db bisa dipakai di method lain.
     */
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        $this->db = \Config\Database::connect();
    }

    /**
     * Halaman detail sertifikat per tahun.
     *
     * @param int $tahun Tahun yang mau dilihat (contoh: 2026)
     */
    public function detail(int $tahun)
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to(base_url('auth/login'))
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // Ambil semua pengajuan approved di tahun ini
        $perkara = $this->db->table('pengajuan')
            ->select('pengajuan.*, 
                      users.nama_lengkap as advokat_nama, 
                      users.nia as advokat_nia, 
                      pbh_cabang.nama as cabang_nama')
            ->join('users', 'users.id = pengajuan.user_id')
            ->join('pbh_cabang', 'pbh_cabang.id = pengajuan.pbh_cabang_id', 'left')
            ->where('pengajuan.user_id', $userId)
            ->where('pengajuan.tahun', $tahun)
            ->where('pengajuan.status', 'approved')
            ->orderBy('pengajuan.verified_at', 'DESC')
            ->get()->getResultArray();

        // Hitung total jam (dari durasi_jam yang diisi verifikator)
        $totalJam = 0;
        foreach ($perkara as $p) {
            $totalJam += (int) ($p['durasi_jam'] ?? 0);
        }

        // Hitung sertifikat & sisa
        $JAM_PER_SERTIFIKAT = 50;
        $sertifikatTerbit   = (int) floor($totalJam / $JAM_PER_SERTIFIKAT);
        $sisaJam            = $JAM_PER_SERTIFIKAT - ($totalJam % $JAM_PER_SERTIFIKAT);
        if ($sisaJam === $JAM_PER_SERTIFIKAT) $sisaJam = 0;

        // Detail per perkara (ambil penerima manfaat untuk tampilan)
        $pmModel = model('PenerimaManfaatModel');
        foreach ($perkara as &$p) {
            $p['penerima_manfaat'] = $pmModel->where('pengajuan_id', $p['id'])->findAll();
        }
        unset($p);

        // Log akses
        \App\Libraries\ActivityLogger::log(
            'view_sertifikat',
            'sertifikat',
            "Advokat melihat detail sertifikat tahun {$tahun}",
            ['subject_type' => 'Pengajuan']
        );

        return view('advokat/sertifikat_detail', [
            'title'            => 'Detail Sertifikat ' . $tahun,
            'tahun'            => $tahun,
            'perkara'          => $perkara,
            'totalJam'         => $totalJam,
            'sertifikatTerbit' => $sertifikatTerbit,
            'sisaJam'          => $sisaJam,
            'targetJam'        => $JAM_PER_SERTIFIKAT,
        ]);
    }

    /**
     * Export daftar perkara sertifikat ke CSV (opsional, untuk kebutuhan advokat).
     */
    public function export(int $tahun)
    {
        $userId = session()->get('user_id');

        $perkara = $this->db->table('pengajuan')
            ->select('no_registrasi, jenis_layanan, jenis_non_litigasi, durasi_jam, verified_at')
            ->where('user_id', $userId)
            ->where('tahun', $tahun)
            ->where('status', 'approved')
            ->orderBy('verified_at', 'DESC')
            ->get()->getResultArray();

        $filename = 'sertifikat-' . $tahun . '-' . date('Ymd-His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $out = fopen('php://output', 'w');
        // BOM untuk Excel
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, ['No. Registrasi', 'Jenis Layanan', 'Jenis Perkara', 'Durasi (Jam)', 'Tanggal Disetujui']);

        foreach ($perkara as $p) {
            fputcsv($out, [
                $p['no_registrasi'],
                $p['jenis_layanan'],
                $p['jenis_non_litigasi'] ?? '-',
                $p['durasi_jam'] ?? 0,
                $p['verified_at'] ? date('d/m/Y', strtotime($p['verified_at'])) : '-',
            ]);
        }
        fclose($out);
        exit;
    }
}