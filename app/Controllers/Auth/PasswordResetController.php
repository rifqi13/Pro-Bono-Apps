<?php
namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Libraries\ActivityLogger;
use App\Libraries\TokenGenerator;
use App\Libraries\Mailer;

class PasswordResetController extends BaseController
{
    public function forgot()
    {
        return view('auth/forgot');
    }

    public function sendLink()
    {
        $email = strtolower(trim($this->request->getPost('email')));
        $user = model('UserModel')->findByEmail($email);

        // Selalu tampilkan pesan sukses (jangan bocorkan email terdaftar/tidak)
        if (!$user) {
            return redirect()->back()->with('success',
                'Jika email terdaftar, link reset telah dikirim ke email Anda.');
        }

        $token = TokenGenerator::generate();
        $model = model('PasswordResetModel');
        $model->where('email', $email)->delete();
        $model->insert([
            'email'      => $email,
            'token_hash' => $token['hash'],
            'expired_at' => date('Y-m-d H:i:s', time() + 3600), // 60 menit
            'ip_address' => $this->request->getIPAddress(),
        ]);

        $link = base_url('auth/reset/' . $token['plain'] . '?email=' . urlencode($email));
        (new Mailer())->send(
            $email,
            'Reset Password - PERADI Pro Bono',
            'emails/reset_password',
            ['user' => $user, 'link' => $link, 'expire' => 60]
        );

        ActivityLogger::log('password_reset_request', 'auth', 'Reset password diminta: ' . $email);

        return redirect()->back()->with('success',
            'Jika email terdaftar, link reset telah dikirim ke email Anda.');
    }

    public function reset(string $token)
    {
        $email = $this->request->getGet('email');
        $hash = TokenGenerator::hash($token);

        $row = model('PasswordResetModel')
            ->where('email', $email)
            ->where('token_hash', $hash)
            ->where('expired_at >=', date('Y-m-d H:i:s'))
            ->where('used_at', null)
            ->first();

        if (!$row) {
            return redirect()->to(base_url('auth/login'))
                ->with('error', 'Link reset tidak valid atau sudah kadaluarsa.');
        }

        return view('auth/reset', ['token' => $token, 'email' => $email]);
    }

    public function updatePassword()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'token'    => 'required',
            'password' => 'required|min_length[8]|regex_match[/^(?=.*[A-Za-z])(?=.*\d).+$/]',
            'confirm'  => 'required|matches[password]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = strtolower(trim($this->request->getPost('email')));
        $token = $this->request->getPost('token');
        $hash = TokenGenerator::hash($token);

        $row = model('PasswordResetModel')
            ->where('email', $email)
            ->where('token_hash', $hash)
            ->where('expired_at >=', date('Y-m-d H:i:s'))
            ->where('used_at', null)
            ->first();

        if (!$row) {
            return redirect()->to(base_url('auth/login'))->with('error', 'Token tidak valid.');
        }

        $user = model('UserModel')->findByEmail($email);
        if (!$user) {
            return redirect()->to(base_url('auth/login'))->with('error', 'User tidak ditemukan.');
        }

        model('UserModel')->update($user['id'], [
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_ARGON2ID),
        ]);
        model('PasswordResetModel')->update($row['id'], ['used_at' => date('Y-m-d H:i:s')]);

        ActivityLogger::log('password_reset_success', 'auth', 'Password direset: ' . $email,
            ['subject_type' => 'User', 'subject_id' => $user['id']]);

        // Kirim notifikasi password berhasil diubah
        (new Mailer())->send(
            $email,
            'Password Berhasil Diubah - PERADI Pro Bono',
            'emails/password_changed',
            [
                'user' => $user,
                'waktu' => date('d M Y H:i:s'),
                'ip'    => $this->request->getIPAddress(),
            ]
        );

        // Hapus session lama user (kalau ada) → paksa login ulang
        // (Opsional: butuh session driver DB, bisa query hapus berdasarkan user_id di session data)

        return redirect()->to(base_url('auth/login'))
            ->with('success', 'Password berhasil diubah. Silakan login dengan password baru.');
    }
}