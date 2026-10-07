<?php
namespace App\Models;
use CodeIgniter\Model;

class LoginAttemptModel extends Model
{
    protected $table      = 'login_attempts';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['email','ip_address','user_agent','success','attempted_at'];
    protected $useTimestamps = false;

    public function countFailedByIp(string $ip, int $minutes): int
    {
        return $this->where('ip_address', $ip)
                    ->where('success', 0)
                    ->where('attempted_at >=', date('Y-m-d H:i:s', strtotime("-{$minutes} minutes")))
                    ->countAllResults();
    }

    public function countFailedByEmail(string $email, int $minutes): int
    {
        return $this->where('email', $email)
                    ->where('success', 0)
                    ->where('attempted_at >=', date('Y-m-d H:i:s', strtotime("-{$minutes} minutes")))
                    ->countAllResults();
    }

    public function recentFailed(int $limit = 50): array
    {
        return $this->where('success', 0)
                    ->orderBy('attempted_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}