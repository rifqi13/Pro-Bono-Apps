<?php
namespace App\Controllers\Auth;
use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\PasswordResetModel;
use App\Libraries\ActivityLogger;

class PasswordReset extends BaseController
{
    public function forgot()
    {
        return view('auth/forgot');
    }

    public function sendLink()
    {
        $email = $this->request->getPost('email');
        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        // Selalu tampilkan pesan sukses (jangan bocorkan email terdaftar/tidak)
        if (!$user) {
            return redirect()->back()->with('success', 'Jika email terdaftar, link reset telah dikirim.');
        }

        $token = bin2hex(random_bytes(32));
        $hash  = hash('sha256', $token);

        $model = new PasswordResetModel();
        // Hapus token lama
        $model->where('email', $email)->delete();
        $model->insert([
            'email'      => $email,
            'token_hash' => $hash,
            'expired_at' => date('Y-m-d H:i:s', time() + 3600),
            'ip_address' => $this->request->getIPAddress(),
        ]);

        $link = base_url('/auth/reset/' . $token . '?email=' . urlencode($email));

        // Kirim email
        $emailService = \Config\Services::email();
        $emailService->setTo($email);
        $emailService->setSubject('Reset Password - PERADI Pro Bono');
        $emailService->setMessage(view('emails/reset_password', ['link' => $link, 'nama' => $user['nama_lengkap']]));
        $emailService->send();

        ActivityLogger::log('password_reset_request', 'auth', 'Reset password diminta: ' . $email);

        return redirect()->back()->with('success', 'Jika email terdaftar, link reset telah dikirim.');
    }

    public function reset($token)
    {
        $email = $this->request->getGet('email');
        $hash = hash('sha256', $token);
        $model = new PasswordResetModel();
        $row = $model->where('email', $email)
                     ->where('token_hash', $hash)
                     ->where('expired_at >=', date('Y-m-d H:i:s'))
                     ->where('used_at', null)
                     ->first();

        if (!$row) {
            return redirect()->to('/auth/login')->with('error', 'Link reset tidak valid atau sudah kadaluarsa.');
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
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $token = $this->request->getPost('token');
        $hash  = hash('sha256', $token);

        $model = new PasswordResetModel();
        $row = $model->where('email', $email)
                     ->where('token_hash', $hash)
                     ->where('expired_at >=', date('Y-m-d H:i:s'))
                     ->where('used_at', null)
                     ->first();

        if (!$row) {
            return redirect()->to('/auth/login')->with('error', 'Token tidak valid.');
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();
        $userModel->update($user['id'], [
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_ARGON2ID),
        ]);

        $model->update($row['id'], ['used_at' => date('Y-m-d H:i:s')]);

        ActivityLogger::log('password_reset_success', 'auth', 'Password berhasil direset: ' . $email);

        return redirect()->to('/auth/login')->with('success', 'Password berhasil diubah. Silakan login.');
    }
}