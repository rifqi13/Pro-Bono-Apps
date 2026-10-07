<?php
namespace App\Libraries;

use App\Models\SettingModel;

class PasswordPolicy
{
    /**
     * Validasi kekuatan password.
     * Return: ['valid' => bool, 'errors' => array]
     */
    public static function validate(string $password, ?string $email = null): array
    {
        $errors = [];

        $minLength = (int) SettingModel::get('password_min_length', 8);
        $checkBlacklist = (bool) SettingModel::get('password_check_blacklist', true);

        if (strlen($password) < $minLength) {
            $errors[] = "Password minimal {$minLength} karakter.";
        }

        if (!preg_match('/[A-Za-z]/', $password)) {
            $errors[] = 'Password harus mengandung huruf.';
        }

        if (!preg_match('/\d/', $password)) {
            $errors[] = 'Password harus mengandung angka.';
        }

        // Cek password umum
        if ($checkBlacklist) {
            if (self::isBlacklisted($password)) {
                $errors[] = 'Password terlalu umum. Gunakan kombinasi lain.';
            }
        }

        // Cek password tidak sama dengan email
        if ($email) {
            $localPart = explode('@', $email)[0];
            if (strcasecmp($password, $localPart) === 0) {
                $errors[] = 'Password tidak boleh sama dengan bagian email Anda.';
            }
        }

        // Cek tidak terlalu banyak karakter berulang
        if (preg_match('/(.)\1{3,}/', $password)) {
            $errors[] = 'Password mengandung karakter yang sama 4x berturut-turut.';
        }

        return ['valid' => empty($errors), 'errors' => $errors];
    }

    public static function isBlacklisted(string $password): bool
    {
        $lower = strtolower($password);
        $model = new \App\Models\PasswordBlacklistModel();
        return (bool) $model->where('password', $lower)->first();
    }

    /**
     * Hitung skor kekuatan password (0-100).
     */
    public static function score(string $password): int
    {
        $score = 0;

        // Panjang
        $len = strlen($password);
        $score += min(40, $len * 3);

        // Variasi karakter
        if (preg_match('/[a-z]/', $password)) $score += 10;
        if (preg_match('/[A-Z]/', $password)) $score += 15;
        if (preg_match('/\d/', $password))    $score += 15;
        if (preg_match('/[^A-Za-z0-9]/', $password)) $score += 20;

        // Penalti kalau pattern umum
        if (preg_match('/(.)\1{2,}/', $password)) $score -= 10;
        if (preg_match('/^[0-9]+$/', $password))  $score -= 20;
        if (self::isBlacklisted($password))       $score  = 0;

        return max(0, min(100, $score));
    }
}