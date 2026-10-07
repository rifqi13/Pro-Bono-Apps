<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('user_id')) {
            return redirect()->to(base_url('auth/login'))
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // Cek user masih ada & aktif
        $user = model('UserModel')->find(session()->get('user_id'));
        if (!$user || $user['status'] !== 'aktif') {
            session()->destroy();
            return redirect()->to(base_url('auth/login'))
                ->with('error', 'Akun tidak aktif atau tidak ditemukan.');
        }

        // Session timeout (2 jam idle)
        $last = session()->get('last_activity');
        if ($last && (time() - $last) > 7200) {
            session()->destroy();
            return redirect()->to(base_url('auth/login'))
                ->with('error', 'Sesi berakhir karena tidak ada aktivitas. Silakan login kembali.');
        }
        session()->set('last_activity', time());
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}