<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\ActivityLogger;
use App\Libraries\LogArchiver;
use App\Models\ActivityLogModel;

class LogActionController extends BaseController
{
    public function index()
    {
        $model = new ActivityLogModel();
        $filters = $this->getFilters();

        $builder = $model->orderBy('created_at', 'DESC');
        $this->applyFilters($builder, $filters);

        $logs = $builder->paginate(50);

        // Charts
        $chartDaily = $model->chartDaily(30);
        $chartTop   = $model->chartTopUsers(10, 90);
        $chartAction= $model->chartActionDistribution(90);

        // Widget login gagal
        $failedLogins = $model->recentFailedLogins(10);

        // Stats
        $stats = LogArchiver::stats();

        // Reminder backup
        $backupModel = model('LogBackupModel');
        $daysSinceBackup = $backupModel->daysSinceLastBackup();
        $needBackup = $daysSinceBackup === null || $daysSinceBackup > 180;

        return view('admin/log_action', [
            'title'           => 'Log Aktivitas Sistem',
            'logs'            => $logs,
            'pager'           => $model->pager,
            'filters'         => $filters,
            'chartDaily'      => $chartDaily,
            'chartTop'        => $chartTop,
            'chartAction'     => $chartAction,
            'failedLogins'    => $failedLogins,
            'stats'           => $stats,
            'daysSinceBackup' => $daysSinceBackup,
            'needBackup'      => $needBackup,
        ]);
    }

    public function show(int $id)
    {
        $log = model('ActivityLogModel')->find($id);
        if (!$log) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Log tidak ditemukan.']);
        }
        return $this->response->setJSON([
            'success' => true,
            'html'    => view('admin/log_action_detail', ['log' => $log]),
        ]);
    }

    /**
     * Export semua log (dengan filter) ke CSV.
     */
    public function export()
    {
        $model = new ActivityLogModel();
        $filters = $this->getFilters();
        $builder = $model->orderBy('created_at', 'DESC');
        $this->applyFilters($builder, $filters);
        $logs = $builder->findAll();

        $path = LogArchiver::exportCsv($logs);

        ActivityLogger::log('log_export_csv', 'admin',
            "Export log CSV: " . count($logs) . " baris");

        return $this->response->download($path, null)->setFileName(basename($path));
    }

    /**
     * Backup log ke CSV (dan simpan metadata ke log_backups).
     */
    public function backup()
    {
        $model = new ActivityLogModel();
        $logs = $model->orderBy('created_at', 'DESC')->findAll();

        if (empty($logs)) {
            return redirect()->back()->with('error', 'Tidak ada log untuk dibackup.');
        }

        $path = LogArchiver::backupToCsv($logs, session()->get('user_id'));

        return $this->response->download($path, null)->setFileName(basename($path));
    }

    /**
     * Cleanup manual — hapus log lebih tua dari X hari.
     * WAJIB isi alasan.
     */
    public function cleanup()
    {
        $days = (int) $this->request->getPost('days');
        $alasan = trim($this->request->getPost('alasan') ?? '');
        $konfirmasi = $this->request->getPost('konfirmasi');

        if ($konfirmasi !== 'HAPUS') {
            return redirect()->back()->with('error', 'Ketik HAPUS pada kolom konfirmasi.');
        }
        if (empty($alasan) || strlen($alasan) < 10) {
            return redirect()->back()->with('error', 'Alasan wajib diisi minimal 10 karakter.');
        }
        if ($days < 30) {
            return redirect()->back()->with('error', 'Minimal cutoff 30 hari.');
        }

        // Log SEBELUM hapus (supaya tercatat)
        ActivityLogger::log('log_cleanup_manual_start', 'admin',
            "Mulai cleanup manual: cutoff {$days} hari. Alasan: {$alasan}");

        $count = LogArchiver::purge($days);

        return redirect()->back()->with('success', "{$count} log berhasil dihapus permanen.");
    }

    /**
     * Arsip manual — pindahkan ke tabel arsip.
     */
    public function archiveNow()
    {
        $result = LogArchiver::archive(90);
        return redirect()->back()->with('success',
            "{$result['archived']} log berhasil diarsipkan.");
    }

    private function getFilters(): array
    {
        return [
            'action'  => $this->request->getGet('action'),
            'module'  => $this->request->getGet('module'),
            'user_id' => $this->request->getGet('user_id'),
            'dari'    => $this->request->getGet('dari'),
            'sampai'  => $this->request->getGet('sampai'),
            'q'       => $this->request->getGet('q'),
        ];
    }

    private function applyFilters($builder, array $f): void
    {
        if (!empty($f['action']))  $builder->where('action', $f['action']);
        if (!empty($f['module']))  $builder->where('module', $f['module']);
        if (!empty($f['user_id'])) $builder->where('user_id', $f['user_id']);
        if (!empty($f['q']))       $builder->like('description', $f['q']);
        if (!empty($f['dari']))    $builder->where('created_at >=', $f['dari'] . ' 00:00:00');
        if (!empty($f['sampai']))  $builder->where('created_at <=', $f['sampai'] . ' 23:59:59');
    }
}