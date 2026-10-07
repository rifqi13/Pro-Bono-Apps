<?php
namespace App\Libraries;

use App\Models\LoginAttemptModel;

class LoginAttemptTracker
{
    /**
     * Catat percobaan login (sukses/gagal).
     */
    public static function record(?string $email, string $ip, bool $success): void
    {
        $model = new LoginAttemptModel();
        $model->insert([
            'email'        => $email,
            'ip_address'   => $ip,
            'user_agent'   => substr((string) service('request')->getUserAgent(), 0, 255),
            'success'      => $success ? 1 : 0,
            'attempted_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Cek apakah IP atau email sudah melewati batas.
     * Return: ['locked' => bool, 'reason' => string]
     */
    public static function checkLockout(?string $email, string $ip): array
    {
        $config = config('Auth');
        $model = new LoginAttemptModel();

        $failedIp = $model->countFailedByIp($ip, $config->loginWindowIp);
        if ($failedIp >= $config->loginMaxAttemptsPerIp) {
            return [
                'locked' => true,
                'reason' => "Terlalu banyak percobaan dari IP ini. Coba lagi dalam {$config->lockoutDuration} menit.",
            ];
        }

        if ($email) {
            $failedEmail = $model->countFailedByEmail($email, $config->loginWindowEmail);
            if ($failedEmail >= $config->loginMaxAttemptsPerEmail) {
                return [
                    'locked' => true,
                    'reason' => "Akun terkunci sementara karena terlalu banyak percobaan. Coba lagi dalam {$config->lockoutDuration} menit.",
                ];
            }
        }

        return ['locked' => false, 'reason' => ''];
    }

    /**
     * Kirim alert ke semua admin jika gagal login melewati threshold.
     * Return: bool (true jika alert terkirim)
     */
    public static function maybeSendAlert(?string $email, string $ip): bool
    {
        $config = config('Auth');
        $model = new LoginAttemptModel();

        $failedIp    = $model->countFailedByIp($ip, $config->loginWindowIp);
        $failedEmail = $email ? $model->countFailedByEmail($email, $config->loginWindowEmail) : 0;

        // Kirim alert HANYA saat tepat melewati threshold (hindari spam)
        $triggerIp    = $failedIp === $config->loginMaxAttemptsPerIp;
        $triggerEmail = $email && $failedEmail === $config->loginMaxAttemptsPerEmail;

        if (!$triggerIp && !$triggerEmail) return false;

        $recipients = Mailer::getAdminRecipients();
        if (empty($recipients)) return false;

        $data = [
            'email_attempted' => $email ?? '(tidak diketahui)',
            'ip'              => $ip,
            'failed_ip'       => $failedIp,
            'failed_email'    => $failedEmail,
            'user_agent'      => (string) service('request')->getUserAgent(),
            'waktu'           => date('d M Y H:i:s'),
            'window_ip'       => $config->loginWindowIp,
            'window_email'    => $config->loginWindowEmail,
        ];

        (new Mailer())->sendBulk(
            $recipients,
            '[PERADI Pro Bono] Peringatan: Percobaan Login Gagal Berulang',
            'emails/alert_login_failed',
            $data
        );

        ActivityLogger::log(
            'alert_login_failed_sent',
            'auth',
            "Alert login gagal dikirim ke " . count($recipients) . " admin. Email: " . ($email ?? '-') . ", IP: {$ip}"
        );

        return true;
    }

    public static function record(?string $email, string $ip, bool $success): void
{
    $model = new LoginAttemptModel();
    $model->insert([
        'email'        => $email,
        'ip_address'   => $ip,
        'user_agent'   => substr((string) service('request')->getUserAgent(), 0, 255),
        'success'      => $success ? 1 : 0,
        'attempted_at' => date('Y-m-d H:i:s'),
    ]);

    // Auto-block IP jika gagal melebihi threshold
    if (!$success) {
        $count = $model->countFailedByIp($ip, 60);
        IpBlocker::autoBlock($ip, $count);
    }
}
}