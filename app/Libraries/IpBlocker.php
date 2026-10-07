<?php
namespace App\Libraries;

use App\Models\BlockedIpModel;
use App\Models\IpWhitelistModel;

class IpBlocker
{
    /**
     * Cek apakah IP diblokir. Return: null jika aman, array blocked info jika diblokir.
     */
    public static function check(string $ip): ?array
    {
        // Whitelist selalu lolos
        if ((new IpWhitelistModel())->isWhitelisted($ip)) {
            return null;
        }
        return (new BlockedIpModel())->isBlocked($ip);
    }

    /**
     * Auto-block setelah threshold.
     * Return: true jika IP baru saja diblokir.
     */
    public static function autoBlock(string $ip, int $attemptCount): bool
    {
        $config = config('Auth');
        if ($attemptCount < $config->autoBlockThreshold) return false;

        // Sudah diblokir? skip
        $existing = (new BlockedIpModel())->isBlocked($ip);
        if ($existing) return false;

        // Whitelist? skip
        if ((new IpWhitelistModel())->isWhitelisted($ip)) return false;

        $blockId = (new BlockedIpModel())->block(
            $ip,
            "Auto-block: {$attemptCount} percobaan login gagal",
            'auto',
            null,
            $config->autoBlockDurationHours
        );

        ActivityLogger::log('ip_auto_blocked', 'security',
            "IP {$ip} diblokir otomatis setelah {$attemptCount} percobaan gagal. Block ID: {$blockId}",
            ['subject_type' => 'BlockedIp', 'subject_id' => $blockId]);

        // Kirim email ke semua admin
        self::notifyAdminsBlocked($ip, $attemptCount);

        return true;
    }

    public static function block(string $ip, string $reason, ?int $blockedBy = null, ?int $expireHours = null): int
    {
        $blockId = (new BlockedIpModel())->block($ip, $reason, 'manual', $blockedBy, $expireHours);

        ActivityLogger::log('ip_manual_block', 'security',
            "IP {$ip} diblokir manual. Alasan: {$reason}",
            ['subject_type' => 'BlockedIp', 'subject_id' => $blockId]);

        return $blockId;
    }

    public static function unblock(string $ip, int $byUserId): bool
    {
        $ok = (new BlockedIpModel())->unblock($ip);
        if ($ok) {
            ActivityLogger::log('ip_unblock', 'security',
                "IP {$ip} di-unblock oleh admin",
                ['subject_type' => 'BlockedIp']);
        }
        return $ok;
    }

    public static function whitelist(string $ip, string $ket, int $byUserId): int
    {
        // Hapus dari blocked jika ada
        (new BlockedIpModel())->unblock($ip);

        $id = (new IpWhitelistModel())->add($ip, $ket, $byUserId);

        ActivityLogger::log('ip_whitelist_add', 'security',
            "IP {$ip} ditambahkan ke whitelist. Keterangan: {$ket}",
            ['subject_type' => 'IpWhitelist', 'subject_id' => $id]);

        return $id;
    }

    private static function notifyAdminsBlocked(string $ip, int $attempts): void
    {
        $recipients = Mailer::getAdminRecipients();
        if (empty($recipients)) return;

        (new Mailer())->sendBulk(
            $recipients,
            '[PERADI Pro Bono] IP Otomatis Diblokir',
            'emails/alert_ip_blocked',
            [
                'ip'         => $ip,
                'attempts'   => $attempts,
                'waktu'      => date('d M Y H:i:s'),
                'durasi'     => config('Auth')->autoBlockDurationHours . ' jam',
                'url_unblock'=> base_url('admin/blocked-ip'),
            ]
        );
    }
}