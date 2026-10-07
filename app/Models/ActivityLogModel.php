<?php
namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table      = 'activity_logs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_id', 'user_role', 'action', 'module', 'description',
        'subject_type', 'subject_id', 'old_values', 'new_values',
        'ip_address', 'user_agent',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    // ============================================================
    // QUERY FILTER (dipakai di halaman Log Action)
    // ============================================================
    public function getFiltered(array $filters, int $perPage = 50)
    {
        $builder = $this->orderBy('created_at', 'DESC');
        if (!empty($filters['action']))  $builder->where('action', $filters['action']);
        if (!empty($filters['module']))  $builder->where('module', $filters['module']);
        if (!empty($filters['user_id'])) $builder->where('user_id', $filters['user_id']);
        if (!empty($filters['q']))       $builder->like('description', $filters['q']);
        if (!empty($filters['dari']))    $builder->where('created_at >=', $filters['dari'] . ' 00:00:00');
        if (!empty($filters['sampai']))  $builder->where('created_at <=', $filters['sampai'] . ' 23:59:59');
        return $builder->paginate($perPage);
    }

    // ============================================================
    // ARSIP LOG (BAGIAN G)
    // ============================================================
    public function getOldLogs(int $days, int $limit = 5000): array
    {
        return $this->where('created_at <', date('Y-m-d H:i:s', strtotime("-{$days} days")))
                    ->orderBy('created_at', 'ASC')
                    ->limit($limit)
                    ->findAll();
    }

    public function countOldLogs(int $days): int
    {
        return $this->where('created_at <', date('Y-m-d H:i:s', strtotime("-{$days} days")))
                    ->countAllResults();
    }

    public function countAll(): int
    {
        return $this->countAllResults();
    }

    public function deleteOld(int $days): int
    {
        return $this->where('created_at <', date('Y-m-d H:i:s', strtotime("-{$days} days")))
                    ->delete(null, true);
    }

    // ============================================================
    // CHART (BAGIAN G)
    // ============================================================

    /**
     * Chart: top 10 user paling aktif (dalam X hari terakhir).
     */
    public function chartTopUsers(int $limit = 10, int $days = 90): array
    {
        return $this->select('activity_logs.user_id, users.nama_lengkap, users.role, COUNT(*) as total')
                    ->join('users', 'users.id = activity_logs.user_id', 'left')
                    ->where('activity_logs.user_id IS NOT NULL')
                    ->where('activity_logs.created_at >=', date('Y-m-d H:i:s', strtotime("-{$days} days")))
                    ->groupBy('activity_logs.user_id')
                    ->orderBy('total', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Chart: distribusi action.
     */
    public function chartActionDistribution(int $days = 90): array
    {
        return $this->select('action, COUNT(*) as total')
                    ->where('created_at >=', date('Y-m-d H:i:s', strtotime("-{$days} days")))
                    ->groupBy('action')
                    ->orderBy('total', 'DESC')
                    ->limit(15)
                    ->findAll();
    }

    /**
     * Chart: aktivitas harian (30 hari terakhir).
     */
    public function chartDaily(int $days = 30): array
    {
        $rows = $this->select('DATE(created_at) as tanggal, COUNT(*) as total')
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime("-{$days} days")))
            ->groupBy('DATE(created_at)')
            ->orderBy('tanggal', 'ASC')
            ->findAll();

        $labels = [];
        $values = [];
        $map = array_column($rows, 'total', 'tanggal');
        for ($i = $days - 1; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $labels[] = date('d M', strtotime($d));
            $values[] = (int) ($map[$d] ?? 0);
        }
        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * Widget: login gagal terbaru.
     */
    public function recentFailedLogins(int $limit = 10): array
    {
        return $this->where('action', 'login_failed')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Chart alternatif: top user dengan method berbeda (kalau diperlukan).
     * Tidak dipakai — hanya dokumentasi method yang boleh.
     */
    public function topUsers(int $limit = 10, int $days = 90): array
    {
        // Alias untuk chartTopUsers
        return $this->chartTopUsers($limit, $days);
    }
}