<?php
namespace App\Libraries;

use App\Models\MasterAdvokatModel;

class NiaValidator
{
    /**
     * Validasi NIA dengan 3 lapis:
     * 1. Format (regex)
     * 2. Cek duplikat di tabel users (sudah terdaftar?)
     * 3. Cek di master_advokat (NIA valid & aktif?)
     *
     * Return: ['valid' => bool, 'message' => string, 'master' => array|null]
     */
    public static function validate(string $nia): array
    {
        $nia = trim($nia);
        $config = config('Auth');

        // 1. Format
        if (!preg_match($config->niaFormat, $nia)) {
            return ['valid' => false, 'message' => 'Format NIA tidak valid. Contoh: 98.12345', 'master' => null];
        }

        // 2. Cek sudah terdaftar di users
        $existing = model('UserModel')->where('nia', $nia)->first();
        if ($existing) {
            return ['valid' => false, 'message' => 'NIA sudah terdaftar. Silakan login atau reset password.', 'master' => null];
        }

        // 3. Cek master advokat
        if ($config->checkMasterDb) {
            $master = model('MasterAdvokatModel')->findByNia($nia);
            if (!$master) {
                return ['valid' => false, 'message' => 'NIA tidak ditemukan di database advokat PERADI.', 'master' => null];
            }
            if ($master['status_advokat'] !== 'aktif') {
                return ['valid' => false, 'message' => 'Status advokat tidak aktif. Hubungi DPN PERADI.', 'master' => $master];
            }
            if ((int) $master['is_registered'] === 1) {
                return ['valid' => false, 'message' => 'NIA sudah pernah registrasi. Silakan login.', 'master' => $master];
            }
            return ['valid' => true, 'message' => 'NIA valid.', 'master' => $master];
        }

        return ['valid' => true, 'message' => 'NIA valid (tanpa cek master).', 'master' => null];
    }
}