<?php
namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Libraries\ActivityLogger;
use App\Libraries\NiaValidator;
use App\Libraries\TokenGenerator;
use App\Libraries\Mailer;

class RegisterController extends BaseController
{
    public function index()
{
    // Kalau sudah login → redirect ke dashboard
    if (is_logged_in()) {
        return redirect()->to(dashboard_url());
    }

    $data['cabang_list'] = model('PbhCabangModel')->getDpcList();
    return view('auth/register', $data);
}

    public function store()
    {
        $rules = [
            'nia'          => 'required|max_length[10]',
            'nama_lengkap' => 'required|min_length[3]|max_length[150]',
            'gelar'        => 'permit_empty|max_length[50]',
            'email'        => 'required|valid_email|is_unique[users.email]',
            'no_wa'        => 'required|min_length[10]|max_length[20]|regex_match[/^[0-9+\-\s]+$/]',
            'pbh_cabang_id'=> 'required|integer',
            'password'     => 'required|min_length[8]|regex_match[/^(?=.*[A-Za-z])(?=.*\d).+$/]',
            'confirm'      => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validasi NIA
        $niaCheck = NiaValidator::validate($this->request->getPost('nia'));
        if (!$niaCheck['valid']) {
            return redirect()->back()->withInput()->with('error', $niaCheck['message']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $userId = model('UserModel')->insert([
            'nia'           => $this->request->getPost('nia'),
            'nama_lengkap'  => $this->request->getPost('nama_lengkap'),
            'gelar'         => $this->request->getPost('gelar'),
            'email'         => strtolower($this->request->getPost('email')),
            'no_wa'         => $this->request->getPost('no_wa'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_ARGON2ID),
            'role'          => 'advokat',
            'pbh_cabang_id' => (int) $this->request->getPost('pbh_cabang_id'),
            'status'        => 'pending',
        ], true);

        // Update master_advokat (tandai sudah terdaftar)
        if ($niaCheck['master']) {
            model('MasterAdvokatModel')->update($niaCheck['master']['id'], ['is_registered' => 1]);
        }

        // Generate token verifikasi
        $token = TokenGenerator::generate();
        model('EmailVerificationModel')->insert([
            'user_id'    => $userId,
            'token_hash' => $token['hash'],
            'expired_at' => date('Y-m-d H:i:s', time() + 86400), // 24 jam
            'ip_address' => $this->request->getIPAddress(),
        ]);

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data. Coba lagi.');
        }

        // Kirim email verifikasi
        $user = model('UserModel')->find($userId);
        $link = base_url('auth/verify-email/' . $token['plain']);
        (new Mailer())->send(
            $user['email'],
            'Verifikasi Email - PERADI Pro Bono',
            'emails/verify_email',
            ['user' => $user, 'link' => $link]
        );

        ActivityLogger::log('register_success', 'auth', 'Registrasi berhasil: ' . $user['email'] . ' (NIA: ' . $user['nia'] . ')',
            ['subject_type' => 'User', 'subject_id' => $userId]);

        return redirect()->to(base_url('auth/register/success'))->with('email', $user['email']);
    }

    public function success()
    {
        return view('auth/register_success');
    }
}