<?php
namespace App\Libraries;

use App\Models\ActivityLogModel;
use App\Models\ActivityLogArchiveModel;

class LogArchiver
{
    /**
     * Arsipkan log lebih tua dari X hari ke tabel arsip.
     * Return: ['archived' => int, 'errors' => array]
     */
    public static function archive(int $days = 90, int $batchSize = 1000): array
    {
        $logModel = new ActivityLogModel();
        $archiveModel = new ActivityLogArchiveModel();
        $db = \Config\Database::connect();

        $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        $total = $logModel->countOldLogs($days);
        $archived = 0;
        $errors = [];

        while (true) {
            $logs = $logModel->where('created_at <', $cutoff)
                ->orderBy('created_at', 'ASC')
                ->limit($batchSize)
                ->findAll();

            if (empty($logs)) break;

            $db->transStart();

            $insertData = [];
            $idsToDelete = [];
            foreach ($logs as $log) {
                $insertData[] = [
                    'original_id'  => $log['id'],
                    'user_id'      => $log['user_id'],
                    'user_role'    => $log['user_role'],
                    'action'       => $log['action'],
                    'module'       => $log['module'],
                    'description'  => $log['description'],
                    'subject_type' => $log['subject_type'],
                    'subject_id'   => $log['subject_id'],
                    'old_values'   => $log['old_values'],
                    'new_values'   => $log['new_values'],
                    'ip_address'   => $log['ip_address'],
                    'user_agent'   => $log['user_agent'],
                    'archived_at'  => date('Y-m-d H:i:s'),
                    'created_at'   => $log['created_at'],
                ];
                $idsToDelete[] = $log['id'];
            }

            if (!$archiveModel->insertBatch($insertData)) {
                $errors[] = 'Gagal insert batch';
                $db->transRollback();
                break;
            }

            // Hard delete dari tabel utama
            $db->table('activity_logs')->whereIn('id', $idsToDelete)->delete();

            $db->transComplete();

            if (!$db->transStatus()) {
                $errors[] = 'Transaksi gagal';
                break;
            }

            $archived += count($logs);

            if (count($logs) < $batchSize) break; // sudah habis
        }

        // Log aktivitas arsip
        ActivityLogger::log('log_archive_run', 'admin',
            "Arsip log: {$archived} baris dipindah (cutoff: {$days} hari)");

        return ['archived' => $archived, 'total_before' => $total, 'errors' => $errors];
    }

    /**
     * Hapus permanen log lebih tua dari X hari.
     */
    public static function purge(int $days): int
    {
        $logModel = new ActivityLogModel();
        $count = $logModel->countOldLogs($days);
        $logModel->deleteOld($days);

        ActivityLogger::log('log_purge', 'admin',
            "Purge log: {$count} baris dihapus permanen (cutoff: {$days} hari)");

        return $count;
    }

    /**
     * Statistik arsip.
     */
    public static function stats(): array
    {
        $logModel = new ActivityLogModel();
        $archiveModel = new ActivityLogArchiveModel();

        return [
            'total_active' => $logModel->countAll(),
            'total_archive' => $archiveModel->countAll(),
            'oldest_active' => $logModel->orderBy('created_at', 'ASC')->first()['created_at'] ?? null,
            'oldest_archive' => $archiveModel->orderBy('created_at', 'ASC')->first()['created_at'] ?? null,
            'will_archive' => $logModel->countOldLogs(90),
        ];
    }

    /**
     * Export arsip ke CSV.
     */
    public static function exportCsv(array $logs, string $filename = null): string
    {
        $filename = $filename ?? 'log-archive-' . date('Ymd-His') . '.csv';
        $dir = WRITEPATH . 'exports/';
        if (!is_dir($dir)) mkdir($dir, 0775, true);
        $path = $dir . $filename;

        $fp = fopen($path, 'w');
        // BOM untuk Excel
        fwrite($fp, "\xEF\xBB\xBF");
        fputcsv($fp, [
            'ID','Original ID','Waktu','User ID','Role','Action','Module',
            'Deskripsi','Subject Type','Subject ID','IP Address','User Agent',
            'Old Values','New Values','Archived At'
        ]);
        foreach ($logs as $l) {
            fputcsv($fp, [
                $l['id'], $l['original_id'], $l['created_at'], $l['user_id'], $l['user_role'],
                $l['action'], $l['module'], $l['description'],
                $l['subject_type'], $l['subject_id'], $l['ip_address'], $l['user_agent'],
                $l['old_values'], $l['new_values'], $l['archived_at'],
            ]);
        }
        fclose($fp);

        return $path;
    }

    /**
     * Backup semua log aktif ke CSV.
     */
    public static function backupToCsv(array $logs, int $userId): string
    {
        $filename = 'log-backup-' . date('Ymd-His') . '.csv';
        $path = self::exportCsv($logs, $filename);

        $fileSize = filesize($path);
        $rowCount = count($logs);

        // Simpan ke tabel log_backups
        model('LogBackupModel')->insert([
            'filename'       => $filename,
            'file_path'      => str_replace(WRITEPATH, '', $path),
            'file_size'      => $fileSize,
            'row_count'      => $rowCount,
            'periode_dari'   => $logs ? min(array_column($logs, 'created_at')) : null,
            'periode_sampai' => $logs ? max(array_column($logs, 'created_at')) : null,
            'tipe'           => 'manual',
            'created_by'     => $userId,
        ]);

        ActivityLogger::log('log_backup_manual', 'admin',
            "Backup log manual: {$rowCount} baris", [
                'subject_type' => 'LogBackup',
            ]);

        return $path;
    }
}