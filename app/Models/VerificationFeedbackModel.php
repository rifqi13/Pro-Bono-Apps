<?php
namespace App\Models;
use CodeIgniter\Model;

class VerificationFeedbackModel extends Model
{
    protected $table      = 'verification_feedbacks';
    protected $primaryKey = 'id';           // ← WAJIB
    protected $returnType = 'array';
    protected $allowedFields = [
        'pengajuan_id', 'admin_id', 'kategori', 'catatan', 
    'file_perbaikan',       // ← baru
    'catatan_advokat',      // ← baru
    'resolved_by_advokat',  // ← baru
    'resolved_at', 'resolved_by',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public function getByPengajuan(int $pengajuanId): array
{
    return $this->select('verification_feedbacks.*, users.nama_lengkap as admin_nama')
                ->join('users', 'users.id = verification_feedbacks.admin_id', 'left')
                ->where('pengajuan_id', $pengajuanId)
                ->orderBy('created_at', 'DESC')
                ->findAll();
}

    public function unresolvedCount(int $pengajuanId): int
    {
        return $this->where('pengajuan_id', $pengajuanId)->where('resolved_at', null)->countAllResults();
    }

    public function unresolvedByAdvokat(int $userId): array
    {
        return $this->select('verification_feedbacks.*, pengajuan.no_registrasi')
                    ->join('pengajuan', 'pengajuan.id = verification_feedbacks.pengajuan_id')
                    ->where('pengajuan.user_id', $userId)
                    ->where('verification_feedbacks.resolved_at', null)
                    ->orderBy('verification_feedbacks.created_at', 'DESC')
                    ->findAll();
    }

    /**
 * Ambil feedback yang belum direspon advokat.
 */
public function getUnresolved(int $pengajuanId): array
{
    return $this->where('pengajuan_id', $pengajuanId)
                ->where('resolved_at', null)
                ->orderBy('created_at', 'DESC')
                ->findAll();
}

/**
 * Cek apakah semua feedback sudah ditangani.
 */
public function allResolved(int $pengajuanId): bool
{
    $count = $this->where('pengajuan_id', $pengajuanId)
                  ->where('resolved_at', null)
                  ->countAllResults();
    return $count === 0;
}
}