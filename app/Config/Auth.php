<?php
namespace Config;

use CodeIgniter\Config\BaseConfig;

class Auth extends BaseConfig
{
    public string $niaFormat = '/^\d{2}\.\d{5}$/';
    public bool $checkMasterDb = true;
    public bool $checkExternalApi = false;
    public string $externalApiUrl = '';
    public string $externalApiKey = '';

    public int $loginMaxAttemptsPerIp = 5;
    public int $loginWindowIp = 15; // menit
    public int $loginMaxAttemptsPerEmail = 10;
    public int $loginWindowEmail = 60; // menit
    public int $lockoutDuration = 30; // menit
    public string $alertEmailTo = 'admin@peradi.or.id';

    public int $verifyEmailExpire = 24; // jam
    public int $resetPasswordExpire = 60; // menit

    // Auto-block
    public int $autoBlockThreshold = 20;        // gagal dari 1 IP sebelum diblokir
    public int $autoBlockDurationHours = 24;    // durasi block otomatis (jam)

    // Arsip log
    public int $logArchiveDays = 90;            // arsip log > X hari
    public int $logBackupReminderDays = 180;    // reminder backup
}