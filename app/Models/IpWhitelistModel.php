<?php
namespace App\Models;
use CodeIgniter\Model;

class IpWhitelistModel extends Model
{
    protected $table      = 'ip_whitelist';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['ip_address','keterangan','created_by'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public function isWhitelisted(string $ip): bool
    {
        return (bool) $this->where('ip_address', $ip)->first();
    }

    public function add(string $ip, ?string $ket = null, ?int $by = null): int
    {
        if ($this->isWhitelisted($ip)) {
            return (int) $this->where('ip_address', $ip)->first()['id'];
        }
        return (int) $this->insert([
            'ip_address' => $ip,
            'keterangan' => $ket,
            'created_by' => $by,
        ], true);
    }
}