<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\ActivityLogger;
use App\Libraries\Mailer;
use App\Libraries\TokenGenerator;
use App\Models\NotificationModel;

class UserManagementController extends BaseController
{
    public function index()
    {
        $filters = [
            'role'      => $this->request->getGet('role'),
            'status'    => $this->request->getGet('status'),
            'cabang_id' => $this->request->getGet('cabang_id'),
            'q'         => $this->request->getGet('q'),
        ];

        $model = model('UserModel');
        $builder = $model->select('users.*, pbh_cabang.nama as cabang_nama')
                         ->join('pbh_cabang', 'pbh_cabang.id = users.pbh_cabang_id', 'left')
                         ->orderBy('users.created_at', 'DESC');

        if (!empty($filters['role']))      $builder->where('users.role', $filters['role']);
        if (!empty($filters['status']))    $builder->where('users.status', $filters['status']);
        if (!empty($filters['cabang_id'])) $builder->where('users.pbh_cabang_id', $filters['cabang_id']);
        if (!empty($filters['q'])) {
            $builder->groupStart()
                ->like('users.nama_lengkap', $filters['q'])
                ->orLike('users.email', $filters['q'])
                ->orLike('users.nia', $filters['q'])
            ->groupEnd();
        }

        $list = $builder->paginate(20);

        return view('admin/user_list', [
            'title'      => 'Manajemen User',
            'list'       => $list,
            'pager'      => $model->pager,
            'filters'    => $filters,
            'cabangList' => model('PbhCabangModel')->where('tipe', 'DPC')->findAll(),
        ]);
    }

    public function show(int $id)
    {
        $user = model('UserModel')->getWithCabang($id);
        if (!$user) return redirect()->to(base_url('admin/users'))->with('error', 'User tidak ditemukan.');
        return view('admin/user_detail', ['title' => 'Detail User', 'user' => $user]);
    }

    public function activate(int $id)
    {
        $user = model('UserModel')->find($id);
        if (!$user) return redirect()->back()->with('error', 'User tidak ditemukan.');

        model('UserModel')->update($id, [
            'status' => 'aktif',
            'email_verified_at' => $user['email_verified_at'] ?? date('Y-m-d H:i:s'),
        ]);

        // Kirim email notifikasi
        (new Mailer())->send(
            $user['email'],
            'Akun Anda Telah Diaktifkan - PERADI Pro Bono',
            'emails/account_activated',
            ['user' => $user, 'loginUrl' => base_url('auth/login')]
        );

        NotificationModel::push(
            $id,
            'Akun Diaktifkan ✅',
            'Akun Anda telah diaktifkan oleh admin. Silakan login.',
            'success',
            base_url('auth/login')
        );

        ActivityLogger::log('user_activate', 'user', "Aktivasi user: {$user['email']}",
            ['subject_type' => 'User', 'subject_id' => $id]);

        return redirect()->back()->with('success', 'User berhasil diaktifkan.');
    }

    public function suspend(int $id)
    {
        $user = model('UserModel')->find($id);
        if (!$user) return redirect()->back()->with('error', 'User tidak ditemukan.');
        if ($user['role'] === 'super_admin') return redirect()->back()->with('error', 'Super admin tidak bisa disuspend.');

        model('UserModel')->update($id, ['status' => 'suspend']);
        NotificationModel::push($id, 'Akun Disuspend', 'Akun Anda disuspend oleh admin.', 'danger');

        ActivityLogger::log('user_suspend', 'user', "Suspend user: {$user['email']}",
            ['subject_type' => 'User', 'subject_id' => $id]);

        return redirect()->back()->with('success', 'User disuspend.');
    }

    /**
     * Admin reset password user.
     */
    public function resetPassword(int $id)
    {
        $user = model('UserModel')->find($id);
        if (!$user) return redirect()->back()->with('error', 'User tidak ditemukan.');

        $method = $this->request->getPost('method'); // 'generate' atau 'email_link'

        if ($method === 'generate') {
            // Generate password random, tampilkan sekali di flash
            $newPassword = bin2hex(random_bytes(4)); // 8 char hex
            model('UserModel')->update($id, [
                'password_hash' => password_hash($newPassword, PASSWORD_ARGON2ID),
            ]);

            ActivityLogger::log('user_reset_password_generate', 'user',
                "Admin reset password user: {$user['email']}",
                ['subject_type' => 'User', 'subject_id' => $id]);

            return redirect()->back()
                ->with('success', 'Password berhasil direset.')
                ->with('new_password', $newPassword)
                ->with('new_password_email', $user['email']);
        }

        // Kirim link reset via email (sama seperti forgot password)
        $token = TokenGenerator::generate();
        model('PasswordResetModel')->where('email', $user['email'])->delete();
        model('PasswordResetModel')->insert([
            'email'      => $user['email'],
            'token_hash' => $token['hash'],
            'expired_at' => date('Y-m-d H:i:s', time() + 3600),
            'ip_address' => $this->request->getIPAddress(),
        ]);

        $link = base_url('auth/reset/' . $token['plain'] . '?email=' . urlencode($user['email']));
        (new Mailer())->send($user['email'], 'Reset Password - PERADI Pro Bono', 'emails/reset_password',
            ['user' => $user, 'link' => $link, 'expire' => 60]);

        ActivityLogger::log('user_reset_password_email', 'user',
            "Admin kirim link reset ke user: {$user['email']}",
            ['subject_type' => 'User', 'subject_id' => $id]);

        return redirect()->back()->with('success', 'Link reset password telah dikirim ke email user.');
    }
}