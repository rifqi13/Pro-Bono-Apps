<?php
namespace Config;

use CodeIgniter\Config\BaseConfig;

class Upload extends BaseConfig
{
    public int $maxSize = 5120; // KB (5MB)
    public array $allowedExt = ['pdf', 'jpg', 'jpeg', 'png'];
    public array $allowedMime = [
        'application/pdf',
        'image/jpeg',
        'image/png',
    ];
    public string $ktpPath = WRITEPATH . 'uploads/ktp/';
    public string $dokumenPath = WRITEPATH . 'uploads/dokumen/';
    public string $fotoPath = WRITEPATH . 'uploads/foto/';
    public string $suratKuasaPath = WRITEPATH . 'uploads/surat_kuasa/';
    public bool $encryptSensitive = true; // enkripsi KTP/KK
}