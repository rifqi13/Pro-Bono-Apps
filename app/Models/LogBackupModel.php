<?php
namespace App\Models;
use CodeIgniter\Model;

class LogBackupModel extends Model
{
    protected $table      = 'log_backups';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'filename','file_path','file_size','row_count',
        'periode_dari','periode_sampai','tipe','created_by',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public function latest(): ?array
    {
        return $this->orderBy('created_at', 'DESC')->first();
    }

    public function daysSinceLastBackup(): ?int
    {
        $last = $this->latest();
        if (!$last) return null;
        return (int) floor((time() - strtotime($last['created_at'])) / 86400);
    }
}