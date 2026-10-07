<?php
namespace App\Controllers;

use CodeIgniter\Database\BaseConnection;
use App\Libraries\ActivityLogger;

class FileController extends BaseController
{
    protected BaseConnection $db;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        $this->db = \Config\Database::connect();
    }

    /**
     * Serve file KTP penerima manfaat.
     * URL: /file/ktp/{id}
     */
    public function ktp(int $id)
    {
        $pm = model('PenerimaManfaatModel')->find($id);
        if (!$pm) {
            return $this->response->setStatusCode(404)->setBody('File tidak ditemukan.');
        }

        // Authorization: admin/super_admin/cabang bisa lihat semua,
        // advokat hanya file miliknya sendiri
        $this->authorizeAccess((int) ($pm['pengajuan_id'] ?? 0));

        if (empty($pm['file_ktp'])) {
            return $this->response->setStatusCode(404)->setBody('File KTP tidak ada.');
        }

        return $this->serveFile($pm['file_ktp'], 'KTP-' . $pm['id']);
    }

    /**
     * Serve dokumen pengajuan.
     * URL: /file/dokumen/{id}
     */
    public function dokumen(int $id)
    {
        $dok = model('DokumenPengajuanModel')->find($id);
        if (!$dok) {
            return $this->response->setStatusCode(404)->setBody('Dokumen tidak ditemukan.');
        }

        $this->authorizeAccess((int) ($dok['pengajuan_id'] ?? 0));

        if (empty($dok['file_path'])) {
            return $this->response->setStatusCode(404)->setBody('File dokumen tidak ada.');
        }

        return $this->serveFile($dok['file_path'], $dok['jenis_dokumen'] ?? 'dokumen');
    }

    /**
     * Serve foto kegiatan.
     * URL: /file/foto/{id}
     */
    public function foto(int $id)
    {
        $foto = model('FotoKegiatanModel')->find($id);
        if (!$foto) {
            return $this->response->setStatusCode(404)->setBody('Foto tidak ditemukan.');
        }

        $this->authorizeAccess((int) ($foto['pengajuan_id'] ?? 0));

        if (empty($foto['file_path'])) {
            return $this->response->setStatusCode(404)->setBody('File foto tidak ada.');
        }

        return $this->serveFile($foto['file_path'], 'foto-' . $foto['id']);
    }

    /**
     * Serve file umum.
     * URL: /file/{id}
     */
    public function serve(int $id)
    {
        // Coba cari di dokumen pengajuan
        $dok = model('DokumenPengajuanModel')->find($id);
        if ($dok) {
            return $this->dokumen($id);
        }

        return $this->response->setStatusCode(404)->setBody('File tidak ditemukan.');
    }

    /**
     * Logika serve file + header yang aman.
     */
    protected function serveFile(string $relativePath, string $label = 'file')
    {
        // Path file di writable/uploads/
        $basePath = WRITEPATH . 'uploads/';
        $fullPath = $basePath . ltrim($relativePath, '/');

        // Kalau path sudah include 'uploads/', jangan double
        if (strpos($relativePath, 'uploads/') === 0) {
            $fullPath = WRITEPATH . $relativePath;
        }

        if (!is_file($fullPath)) {
            return $this->response
                ->setStatusCode(404)
                ->setBody('File fisik tidak ditemukan: ' . basename($fullPath));
        }

        // Ambil MIME
        $mime = mime_content_type($fullPath) ?: 'application/octet-stream';

        // Nama file untuk download
        $filename = $label . '.' . pathinfo($fullPath, PATHINFO_EXTENSION);

        // Log akses
        try {
            ActivityLogger::log('file_access', 'file', "Akses file: {$relativePath}");
        } catch (\Throwable $e) {
            // Ignore log error
        }

        // Kirim file ke browser (inline, bukan download)
        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Length', (string) filesize($fullPath))
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setHeader('Cache-Control', 'private, max-age=300')
            ->setBody(file_get_contents($fullPath));
    }

    /**
     * Authorization: cek apakah user boleh akses file dari pengajuan tertentu.
     */
    protected function authorizeAccess(int $pengajuanId): void
    {
        $role = session()->get('role');
        $userId = session()->get('user_id');

        // Admin & super_admin: bebas
        if (in_array($role, ['admin', 'super_admin'], true)) {
            return;
        }

        // Cabang: hanya boleh lihat pengajuan di cabangnya
        if ($role === 'cabang') {
            $pengajuan = model('PengajuanModel')->find($pengajuanId);
            if (!$pengajuan || (int) $pengajuan['pbh_cabang_id'] !== (int) session()->get('cabang_id')) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Akses ditolak.');
            }
            return;
        }

        // Advokat: hanya boleh lihat pengajuan miliknya
        if ($role === 'advokat') {
            $pengajuan = model('PengajuanModel')->find($pengajuanId);
            if (!$pengajuan || (int) $pengajuan['user_id'] !== (int) $userId) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Akses ditolak.');
            }
            return;
        }

        // Role lain: tolak
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Akses ditolak.');
    }

   public function perbaikan(int $feedbackId)
{
    $fb = model('VerificationFeedbackModel')->find($feedbackId);
    if (!$fb || empty($fb['file_perbaikan'])) {
        return $this->response->setStatusCode(404)->setBody('File perbaikan tidak ditemukan.');
    }

    $this->authorizeAccess((int) $fb['pengajuan_id']);
     $role = session()->get('role');
    
    // Admin & super_admin: bebas
    if (in_array($role, ['admin', 'super_admin'], true)) {
        return;
    }
    return $this->serveFile($fb['file_perbaikan'], 'perbaikan-' . $feedbackId);
}
}