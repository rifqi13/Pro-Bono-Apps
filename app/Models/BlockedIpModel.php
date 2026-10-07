<?php
namespace App\Models;
use CodeIgniter\Model;

class BlockedIpModel extends Model
{
    protected $table      = 'blocked_ips';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'ip_address','reason','reason_type','attempt_count','blocked_by','expired_at',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    /**
     * Cek apakah IP sedang diblokir.
     */
    public function isBlocked(string $ip): ?array
    {
        $row = $this->where('ip_address', $ip)
            ->groupStart()
                ->where('expired_at', null)
                ->orWhere('expired_at >=', date('Y-m-d H:i:s'))
            ->groupEnd()
            ->first();
        return $row ?: null;
    }

    /**
     * Blokir IP (upsert).
     */
    public function block(string $ip, string $reason, string $type = 'auto', ?int $blockedBy = null, ?int $expireHours = null): int
    {
        $existing = $this->where('ip_address', $ip)->first();
        $expired = $expireHours ? date('Y-m-d H:i:s', time() + $expireHours * 3600) : null;

        if ($existing) {
            $this->update($existing['id'], [
                'reason'        => $reason,
                'reason_type'   => $type,
                'expired_at'    => $expired,
                'blocked_by'    => $blockedBy,
                'attempt_count' => ($existing['attempt_count'] ?? 0) + 1,
            ]);
            return (int) $existing['id'];
        }

        return (int) $this->insert([
            'ip_address'    => $ip,
            'reason'        => $reason,
            'reason_type'   => $type,
            'expired_at'    => $expired,
            'blocked_by'    => $blockedBy,
            'attempt_count' => 1,
        ], true);
    }

    public function unblock(string $ip): bool
    {
        return (bool) $this->where('ip_address', $ip)->delete();
    }

    public function getActive(int $perPage = 50)
    {
        return $this->groupStart()
            ->where('expired_at', null)
            ->orWhere('expired_at >=', date('Y-m-d H:i:s'))
            ->groupEnd()
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage);
    }
}