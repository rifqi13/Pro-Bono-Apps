<?php
namespace App\Libraries;

class FileUploader
{
    protected array $allowedMime = [
        'application/pdf',
        'image/jpeg',
        'image/png',
    ];
    protected int $maxSize = 5242880; // 5 MB

    public function uploadKtp(array $file, int $pengajuanId): ?string
    {
        return $this->handle($file, 'ktp', $pengajuanId, true);
    }

    public function uploadDokumen(array $file, int $pengajuanId, string $jenis): ?string
    {
        return $this->handle($file, 'dokumen/' . $jenis, $pengajuanId, false);
    }

    public function uploadFoto(array $file, int $pengajuanId): ?string
    {
        return $this->handle($file, 'foto', $pengajuanId, false);
    }

    protected function handle(array $file, string $subdir, int $pengajuanId, bool $encrypt): ?string
{
        if (empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        if ($file['size'] > $this->maxSize) {
            throw new \RuntimeException('Ukuran file melebihi 5 MB.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $this->allowedMime, true)) {
            throw new \RuntimeException('Tipe file tidak diizinkan.');
        }

        $ext = match ($mime) {
            'application/pdf' => 'pdf',
            'image/jpeg'      => 'jpg',
            'image/png'       => 'png',
            default           => 'bin',
        };

        $dir = WRITEPATH . 'uploads/' . trim($subdir, '/') . '/' . date('Y/m/');

        if (!is_dir($dir)) {
        if (!mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new \RuntimeException('Gagal membuat folder: ' . $dir);
        }
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $target = $dir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            throw new \RuntimeException('Gagal memindahkan file.');
        }

        // Enkripsi KTP (opsional)
        if ($encrypt && config('Upload')->encryptSensitive) {
            $this->encryptFile($target);
        }

        // Path relatif dari WRITEPATH
        return str_replace(WRITEPATH . 'uploads/', '', $target);
    }

    protected function encryptFile(string $path): void
    {
        try {
            $encrypter = \Config\Services::encrypter();
            $data = file_get_contents($path);
            file_put_contents($path, $encrypter->encrypt($data));
        } catch (\Throwable $e) {
            log_message('error', '[FileUploader] Enkripsi gagal: ' . $e->getMessage());
        }
    }
}