<?php
namespace Config;

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ============================================================
// PUBLIC
// ============================================================
$routes->get('/', 'PublicController::index');
$routes->get('cara-kerja', 'PublicController::caraKerja');
$routes->get('faq', 'PublicController::faq');
$routes->get('statistik', 'PublicController::statistik');
$routes->get('api/statistik', 'PublicController::apiStatistik');

// ============================================================
// AUTH
// ============================================================
$routes->group('auth', ['namespace' => 'App\Controllers\Auth'], static function ($routes) {
    // Login
    $routes->get('login', 'LoginController::index');
    $routes->post('login', 'LoginController::attempt', ['filter' => 'ratelimit:5,1']);
    $routes->get('logout', 'LoginController::logout', ['filter' => 'auth']);

    // Register
    $routes->get('register', 'RegisterController::index');
    $routes->post('register', 'RegisterController::store', ['filter' => 'ratelimit:3,60']);
    $routes->get('register/success', 'RegisterController::success');

    // Email Verification
    $routes->get('verify-email', 'EmailVerificationController::notice');
    $routes->get('verify-email/(:segment)', 'EmailVerificationController::verify/$1');
    $routes->post('resend-verification', 'EmailVerificationController::resend', ['filter' => 'ratelimit:3,15']);

    // Password Reset
    $routes->get('forgot', 'PasswordResetController::forgot');
    $routes->post('forgot', 'PasswordResetController::sendLink', ['filter' => 'ratelimit:3,60']);
    $routes->get('reset/(:segment)', 'PasswordResetController::reset/$1');
    $routes->post('reset', 'PasswordResetController::updatePassword');
});

// ============================================================
// ADVOKAT
// ============================================================
$routes->group('advokat', ['filter' => 'role:advokat', 'namespace' => 'App\Controllers\Advokat'], static function ($routes) {
    $routes->get('dashboard', 'DashboardController::index');

    $routes->get('pengajuan', 'PengajuanController::index');
    $routes->get('pengajuan/create', 'PengajuanController::create');
    $routes->post('pengajuan/store', 'PengajuanController::store');
    $routes->get('pengajuan/(:num)', 'PengajuanController::show/$1');
    $routes->get('pengajuan/(:num)/preview', 'PengajuanController::preview/$1');
    $routes->get('pengajuan/(:num)/edit', 'PengajuanController::edit/$1');
    $routes->post('pengajuan/(:num)/update', 'PengajuanController::update/$1');
    $routes->post('pengajuan/(:num)/delete', 'PengajuanController::delete/$1');

    $routes->post('pengajuan/feedback/(:num)/tangani', 'PengajuanController::tanganiFeedback/$1');
    $routes->get('pengajuan/(:num)/revisi', 'PengajuanController::revisi/$1');
    $routes->post('pengajuan/(:num)/revisi', 'PengajuanController::revisiStore/$1');

    // Sertifikat
$routes->get('sertifikat/(:num)', 'SertifikatController::detail/$1');
$routes->get('sertifikat/(:num)/export', 'SertifikatController::export/$1');

    // Auto-save draft
    $routes->post('pengajuan/draft', 'PengajuanController::saveDraft');
    $routes->get('pengajuan/draft/(:num)', 'PengajuanController::loadDraft/$1');

        // ⚠️ Route spesifik DULU
    $routes->get('pengajuan/create', 'PengajuanController::create');
    $routes->post('pengajuan/store', 'PengajuanController::store');
    $routes->post('pengajuan/draft', 'PengajuanController::saveDraft');
    $routes->get('pengajuan/draft/(:num)', 'PengajuanController::loadDraft/$1');
    
    // ⚠️ Route generic (:num) BELAKANGAN
    $routes->get('pengajuan/(:num)', 'PengajuanController::show/$1');
    $routes->get('pengajuan/(:num)/preview', 'PengajuanController::preview/$1');
    $routes->get('pengajuan/(:num)/edit', 'PengajuanController::edit/$1');
    $routes->post('pengajuan/(:num)/update', 'PengajuanController::update/$1');
    $routes->get('pengajuan/(:num)/delete', 'PengajuanController::delete/$1');
});

// ============================================================
// ADMIN
// ============================================================
$routes->group('admin', ['filter' => 'role:admin,super_admin', 'namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('dashboard', 'DashboardController::index');

    // Verifikasi
    $routes->get('verifikasi', 'VerifikasiController::index');
    $routes->get('verifikasi/(:num)', 'VerifikasiController::show/$1');
    $routes->post('verifikasi/(:num)/approve', 'VerifikasiController::approve/$1');
    $routes->post('verifikasi/(:num)/reject', 'VerifikasiController::reject/$1');
    $routes->post('verifikasi/bulk-approve', 'VerifikasiController::bulkApprove');
    $routes->post('verifikasi/bulk-reject', 'VerifikasiController::bulkReject');

    // Export
    $routes->get('export', 'ExportController::form');
    $routes->post('export/excel', 'ExportController::excel');
    $routes->post('export/pdf', 'ExportController::pdf');

    // User management
    $routes->get('users', 'UserManagementController::index');
    $routes->get('users/(:num)', 'UserManagementController::show/$1');
    $routes->post('users/(:num)/activate', 'UserManagementController::activate/$1');
    $routes->post('users/(:num)/suspend', 'UserManagementController::suspend/$1');

    // Log Action — HANYA ADMIN
    $routes->get('log-action', 'LogActionController::index');
    $routes->get('log-action/export', 'LogActionController::export');
    $routes->get('log-action/chart-data', 'LogActionController::chartData');
    $routes->get('log-action/(:num)', 'LogActionController::show/$1');
});

// ============================================================
// CABANG
// ============================================================
$routes->group('cabang', ['filter' => 'role:cabang,admin,super_admin', 'namespace' => 'App\Controllers\Cabang'], static function ($routes) {
    $routes->get('dashboard', 'DashboardController::index');
    $routes->get('advokat', 'DashboardController::advokatList');
    $routes->get('pengajuan', 'DashboardController::pengajuanList');
    $routes->get('pengajuan/(:num)', 'DashboardController::pengajuanDetail/$1');
});

// ============================================================
// FILE (protected)
// ============================================================
// Route spesifik DULU
$routes->get('file/ktp/(:num)', 'FileController::ktp/$1', ['filter' => 'auth']);
$routes->get('file/dokumen/(:num)', 'FileController::dokumen/$1', ['filter' => 'auth']);
$routes->get('file/foto/(:num)', 'FileController::foto/$1', ['filter' => 'auth']);

$routes->get('file/(:num)', 'FileController::serve/$1', ['filter' => 'auth']);
$routes->get('file/ktp/(:num)', 'FileController::ktp/$1', ['filter' => 'auth']);
$routes->get('file/dokumen/(:num)', 'FileController::dokumen/$1', ['filter' => 'auth']);
$routes->get('file/foto/(:num)', 'FileController::foto/$1', ['filter' => 'auth']);

$routes->group('notifications', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'NotificationController::index');
    $routes->post('(:num)/read', 'NotificationController::markRead/$1');
    $routes->post('mark-all-read', 'NotificationController::markAllRead');
});


$routes->group('admin', ['filter' => 'role:admin,super_admin', 'namespace' => 'App\Controllers\Admin'], static function ($routes) {
    // ... routes yang sudah ada ...

    // Feedback verifikasi
    $routes->post('verifikasi/(:num)/feedback', 'VerifikasiController::feedback/$1');

    // User management
    $routes->get('users', 'UserManagementController::index');
    $routes->get('users/(:num)', 'UserManagementController::show/$1');
    $routes->post('users/(:num)/activate', 'UserManagementController::activate/$1');
    $routes->post('users/(:num)/suspend', 'UserManagementController::suspend/$1');
    $routes->post('users/(:num)/reset-password', 'UserManagementController::resetPassword/$1');
});

$routes->group('cabang', ['filter' => 'role:cabang,admin,super_admin', 'namespace' => 'App\Controllers\Cabang'], static function ($routes) {
    $routes->get('dashboard', 'DashboardController::index');
    $routes->get('advokat', 'DashboardController::advokatList');
    $routes->get('pengajuan', 'DashboardController::pengajuanList');
    $routes->get('pengajuan/(:num)', 'DashboardController::pengajuanDetail/$1');
    $routes->get('export', 'DashboardController::exportForm');
    $routes->post('export/excel', 'DashboardController::exportExcel');
    $routes->post('export/pdf', 'DashboardController::exportPdf');
});

$routes->group('admin', ['filter' => 'role:admin,super_admin', 'namespace' => 'App\Controllers\Admin'], static function ($routes) {
    // ... route yang sudah ada

    // Log Action
    $routes->get('log-action', 'LogActionController::index');
    $routes->get('log-action/export', 'LogActionController::export');
    $routes->post('log-action/backup', 'LogActionController::backup');
    $routes->post('log-action/cleanup', 'LogActionController::cleanup');
    $routes->post('log-action/archive-now', 'LogActionController::archiveNow');
    $routes->get('log-action/(:num)', 'LogActionController::show/$1');

    // Log Archive (super_admin only)
    $routes->group('log-archive', ['filter' => 'role:super_admin'], static function ($routes) {
        $routes->get('/', 'LogArchiveController::index');
        $routes->get('download', 'LogArchiveController::download');
    });

    // Blocked IP (super_admin only)
    $routes->group('blocked-ip', ['filter' => 'role:super_admin'], static function ($routes) {
        $routes->get('/', 'BlockedIpController::index');
        $routes->post('block', 'BlockedIpController::block');
        $routes->post('unblock', 'BlockedIpController::unblock');
        $routes->post('whitelist', 'BlockedIpController::whitelist');
        $routes->post('remove-whitelist/(:num)', 'BlockedIpController::removeWhitelist/$1');
    });
});

$routes->get('/', 'PublicController::index');
$routes->get('cara-kerja', 'PublicController::caraKerja');
$routes->get('faq', 'PublicController::faq');
$routes->get('api/statistik', 'PublicController::apiStatistik');
$routes->get('file/perbaikan/(:num)', 'FileController::perbaikan/$1', ['filter' => 'auth']);