<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Reset Password</title></head>
<body style="font-family:Arial,sans-serif; background:#f4f6f9; margin:0; padding:20px;">
  <table width="100%" cellpadding="0" cellspacing="0">
    <tr><td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="background:#fff; border-radius:12px; overflow:hidden;">
        <tr><td style="background:#0a1f3c; padding:24px; text-align:center;">
          <h1 style="color:#fff; font-size:20px; margin:0;">PERADI Pro Bono</h1>
        </td></tr>
        <tr><td style="padding:32px;">
          <h2 style="color:#0a1f3c; font-size:18px; margin-top:0;">Reset Password</h2>
          <p>Yth. <strong><?= esc($user['nama_lengkap']) ?></strong>,</p>
          <p>Kami menerima permintaan reset password untuk akun Anda.</p>
          <p>Klik tombol berikut untuk membuat password baru:</p>
          <p style="text-align:center; margin:30px 0;">
            <a href="<?= esc($link) ?>" style="background:#0a1f3c; color:#fff; padding:12px 32px; text-decoration:none; border-radius:8px; font-weight:600; display:inline-block;">Reset Password</a>
          </p>
          <p style="font-size:13px; color:#6b7280;">Link ini berlaku selama <?= esc($expire) ?> menit.</p>
          <div style="background:#fef3c7; border-left:4px solid #f59e0b; padding:12px; margin-top:20px; font-size:13px;">
            Jika Anda tidak meminta reset password, abaikan email ini. Password Anda tidak akan berubah.
          </div>
        </td></tr>
        <tr><td style="background:#f9fafb; padding:16px; text-align:center; font-size:12px; color:#6b7280;">
          © <?= date('Y') ?> DPN PERADI. All Rights Reserved.
        </td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>