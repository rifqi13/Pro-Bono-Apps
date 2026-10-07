<?php
namespace App\Libraries;

use App\Models\ActivityLogModel;

class ActivityLogger
{
    /**
     * Catat aktivitas ke tabel activity_logs.
     *
     * @param string $action    login_success, login_failed, submit_pengajuan, verify_approve, dll
     * @param string $module    auth, pengajuan, verifikasi, user, admin, dll
     * @param string $description Deskripsi bebas
     * @param array  $options   subject_type, subject_id, old, new
     */
    public static function log(
        string $action,
        string $module,
        string $description = '',
        array $options = []
    ): void {
        try {
            $request = service('request');
            $model = new ActivityLogModel();
            $model->insert([
                'user_id'      => session()->get('user_id'),
                'user_role'    => session()->get('role'),
                'action'       => $action,
                'module'       => $module,
                'description'  => $description,
                'subject_type' => $options['subject_type'] ?? null,
                'subject_id'   => $options['subject_id'] ?? null,
                'old_values'   => isset($options['old']) ? json_encode($options['old'], JSON_UNESCAPED_UNICODE) : null,
                'new_values'   => isset($options['new']) ? json_encode($options['new'], JSON_UNESCAPED_UNICODE) : null,
                'ip_address'   => $request->getIPAddress(),
                'user_agent'   => substr((string) $request->getUserAgent(), 0, 255),
            ]);
        } catch (\Throwable $e) {
            // Jangan sampai logging error mengganggu aplikasi
            log_message('error', '[ActivityLogger] ' . $e->getMessage());
        }
    }
}