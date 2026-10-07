<?php
namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Libraries\ActivityLogger;
use App\Libraries\TokenGenerator;
use App\Libraries\Mailer;

class EmailVerificationController extends BaseController
{
    public function notice()
    {
        return view('auth/verify_pending');
    }

    public function verify(string $token)
    {
        $hash = TokenGenerator::hash($token);
        $model = model('EmailVerificationModel');
        $row = $model->where('token_hash', $hash)
                     ->where('expired_at >=', date('Y-m-d H:i:s'))
                     ->where('used_at', null)
                     ->first();

        if (!$row) {
            return redirect()->to(base_url('auth/login'))
                ->with('error', 'Link verifikasi tidak valid atau sudah kadaluarsa.');
        }

        $user = model('UserModel')->find($row['user_id']);
        if (!$user) {
            return redirect()->to(base_url('auth/login'))
                ->with('error', 'User tidak ditemukan.');
        }
        if ($user['status'] === 'aktif') {
            return redirect()->to(base_url('auth/login'))
                ->with('success', 'Email sudah diverifikasi. Silakan login.');
        }

        model('UserModel')->update($user['id'], [
            'status'            => 'aktif',
            'email_verified_at' => date('Y-m-d H:i:s'),
        ]);
        $model->update($row['id'], ['used_at' => date('Y-m-d H:i:s')]);

        ActivityLogger::log('email_verified', 'auth', 'Email terverifikasi: ' . $user['email'],
            ['subject_type' => 'User', 'subject_id' => $user['id']]);

        return redirect()->to(base_url('auth/login'))
            ->with('success', 'Email berhasil diverifikasi. Silakan login.');
    }

    public function resend()
    {
        $email = strtolower(trim($this->request->getPost('email')));
        $user = model('UserModel')->findByEmail($email);

        if ($user && $user['status'] === 'pending') {
            // Hapus token lama
            model('EmailVerificationModel')->where('user_id', $user['id'])->delete();

            $token = TokenGenerator::generate();
            model('EmailVerificationModel')->insert([
                'user_id'    => $user['id'],
                'token_hash' => $token['hash'],
                'expired_at' => date('Y-m-d H:i:s', time() + 86400),
                'ip_address' => $this->request->getIPAddress(),
            ]);

            $link = base_url('auth/verify-email/' . $token['plain']);
            (new Mailer())->send(
                $user['email'],
                'Verifikasi Email - PERADI Pro Bono',
                'emails/verify_email',
                ['user' => $user, 'link' => $link]
            );

            ActivityLogger::log('email_verification_resent', 'auth', 'Kirim ulang verifikasi: ' . $email);
        }

        return redirect()->back()->with('success',
            'Jika email terdaftar dan belum diverifikasi, link verifikasi telah dikirim ulang.');
    }
}