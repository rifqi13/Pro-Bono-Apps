<?php
namespace App\Controllers\Auth;
use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Libraries\ActivityLogger;

class LoginController extends BaseController
{
    public function index()
{
    // Kalau sudah login → redirect ke dashboard
    if (is_logged_in()) {
        return redirect()->to(dashboard_url());
    }
    return view('auth/login');
}

    public function attempt()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[8]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            ActivityLogger::log('login_failed', 'auth', 'Percobaan login gagal: ' . $email);
            return redirect()->back()->with('error', 'Email atau password salah.');
        }

        if ($user['status'] !== 'aktif') {
            return redirect()->back()->with('error', 'Akun belum diverifikasi atau tidak aktif.');
        }

        // Regenerate session
        session()->regenerate(true);
        session()->set([
            'user_id'    => $user['id'],
            'nama'       => $user['nama_lengkap'],
            'email'      => $user['email'],
            'role'       => $user['role'],
            'cabang_id'  => $user['pbh_cabang_id'],
            'logged_in'  => true,
            'last_activity' => time(),
        ]);

        // Update last login
        $userModel->update($user['id'], [
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => $this->request->getIPAddress(),
        ]);

        ActivityLogger::log('login_success', 'auth', 'Login berhasil: ' . $email);

        return $this->redirectByRole($user['role']);
    }

    public function logout()
    {
        ActivityLogger::log('logout', 'auth', 'Logout: ' . session()->get('email'));
        session()->destroy();
        return redirect()->to('/auth/login')->with('success', 'Anda telah logout.');
    }

    private function redirectByRole(string $role)
{
    return match ($role) {
        'advokat'              => redirect()->to(base_url('advokat/dashboard')),
        'cabang'               => redirect()->to(base_url('cabang/dashboard')), // <-- ini
        'admin', 'super_admin' => redirect()->to(base_url('admin/dashboard')),
        default                => redirect()->to(base_url('auth/login')),
    };
}
}