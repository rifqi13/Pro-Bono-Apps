<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        
        helper('auth');

        $role = session()->get('role');
        if (!in_array($role, $arguments ?? [], true)) {
            \App\Libraries\ActivityLogger::log(
                'access_denied',
                'security',
                'Akses ditolak ke ' . current_url() . ' (role: ' . ($role ?? 'guest') . ')'
            );
            return redirect()->to(dashboard_url())
                ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}