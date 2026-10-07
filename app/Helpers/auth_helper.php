<?php
if (!function_exists('current_user')) {
    function current_user(): ?array
    {
        $userId = session()->get('user_id');
        if (!$userId) return null;
        return model('UserModel')->find($userId);
    }
}

if (!function_exists('is_logged_in')) {
    function is_logged_in(): bool
    {
        return (bool) session()->get('user_id');
    }
}

if (!function_exists('current_role')) {
    function current_role(): ?string
    {
        return session()->get('role');
    }
}

if (!function_exists('has_role')) {
    function has_role(string ...$roles): bool
    {
        return in_array(current_role(), $roles, true);
    }
}

if (!function_exists('is_admin')) {
    function is_admin(): bool
    {
        return has_role('admin', 'super_admin');
    }
}

if (!function_exists('current_cabang_id')) {
    function current_cabang_id(): ?int
    {
        return session()->get('cabang_id');
    }
}

if (!function_exists('is_owner')) {
    function is_owner(int $userId): bool
    {
        return session()->get('user_id') === $userId;
    }
}

if (!function_exists('dashboard_url')) {
    function dashboard_url(): string
    {
        return match (current_role()) {
            'advokat'              => base_url('advokat/dashboard'),
            'cabang'               => base_url('cabang/dashboard'),
            'admin', 'super_admin' => base_url('admin/dashboard'),
            default                => base_url('auth/login'),
        };
    }
}

if (!function_exists('mask_email')) {
    function mask_email(string $email): string
    {
        [$user, $domain] = explode('@', $email);
        $len = strlen($user);
        if ($len <= 2) return $user . '***@' . $domain;
        return substr($user, 0, 2) . str_repeat('*', max(1, $len - 2)) . '@' . $domain;
    }
}

if (!function_exists('unread_notif_count')) {
    function unread_notif_count(): int
    {
        if (!is_logged_in()) return 0;
        static $cached = null;
        if ($cached === null) {
            $cached = model('NotificationModel')->countUnread(session()->get('user_id'));
        }
        return $cached;
    }
}

if (!function_exists('recent_notifs')) {
    function recent_notifs(int $limit = 5): array
    {
        if (!is_logged_in()) return [];
        return model('NotificationModel')->getUnread(session()->get('user_id'), $limit);
    }
}