<?php
namespace App\Libraries;

use App\Models\PengajuanModel;

class DraftManager
{
    /**
     * Simpan draft ke server (dipanggil dari AJAX tiap 30 detik).
     * Return: ['success' => bool, 'pengajuan_id' => int]
     */
    public static function save(int $userId, ?int $pengajuanId, array $payload): array
    {
        $model = new PengajuanModel();

        // Kalau belum ada pengajuan_id, buat baru dengan status draft
        if (!$pengajuanId) {
            $cabangId = session()->get('cabang_id');
            $tahun = date('Y');
            $noReg = $model->generateNoRegistrasi($tahun, $cabangId);

            $pengajuanId = $model->insert([
                'no_registrasi' => $noReg,
                'user_id'       => $userId,
                'pbh_cabang_id' => $cabangId,
                'jenis_layanan' => $payload['jenis_layanan'] ?? 'litigasi',
                'tahun'         => $tahun,
                'status'        => 'draft',
                'draft_data'    => json_encode($payload, JSON_UNESCAPED_UNICODE),
            ], true);
        } else {
            // Pastikan milik user yang login & masih draft
            $existing = $model->where('id', $pengajuanId)->where('user_id', $userId)->first();
            if (!$existing || $existing['status'] !== 'draft') {
                return ['success' => false, 'message' => 'Draft tidak ditemukan atau sudah disubmit.'];
            }
            $model->update($pengajuanId, [
                'jenis_layanan' => $payload['jenis_layanan'] ?? $existing['jenis_layanan'],
                'draft_data'    => json_encode($payload, JSON_UNESCAPED_UNICODE),
            ]);
        }

        return ['success' => true, 'pengajuan_id' => $pengajuanId, 'saved_at' => date('H:i:s')];
    }

    public static function load(int $userId, int $pengajuanId): ?array
    {
        $row = model('PengajuanModel')
            ->where('id', $pengajuanId)
            ->where('user_id', $userId)
            ->where('status', 'draft')
            ->first();
        if (!$row) return null;
        return [
            'pengajuan_id' => $row['id'],
            'no_registrasi' => $row['no_registrasi'],
            'jenis_layanan' => $row['jenis_layanan'],
            'draft_data'   => json_decode($row['draft_data'] ?? '{}', true),
        ];
    }

    public static function delete(int $userId, int $pengajuanId): bool
    {
        return (bool) model('PengajuanModel')
            ->where('id', $pengajuanId)
            ->where('user_id', $userId)
            ->where('status', 'draft')
            ->delete();
    }
}