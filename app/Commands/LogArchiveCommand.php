<?php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\LogArchiver;

class LogArchiveCommand extends BaseCommand
{
    protected $group       = 'Maintenance';
    protected $name        = 'log:archive';
    protected $description = 'Arsipkan activity_logs lebih tua dari 90 hari ke activity_logs_archive.';
    protected $usage       = 'log:archive [days]';
    protected $arguments   = [
        'days' => 'Jumlah hari cutoff (default 90)',
    ];

    public function run(array $params)
    {
        $days = (int) ($params[0] ?? 90);
        CLI::write("Memulai arsip log lebih tua dari {$days} hari...", 'yellow');

        $start = microtime(true);
        $result = LogArchiver::archive($days);
        $elapsed = round(microtime(true) - $start, 2);

        CLI::write("Total baris lama: {$result['total_before']}", 'white');
        CLI::write("Berhasil diarsipkan: {$result['archived']} baris", 'green');
        CLI::write("Waktu eksekusi: {$elapsed} detik", 'white');

        if (!empty($result['errors'])) {
            CLI::write("Error:", 'red');
            foreach ($result['errors'] as $e) CLI::write("  - {$e}", 'red');
        }

        CLI::write('Selesai.', 'green');
    }
}