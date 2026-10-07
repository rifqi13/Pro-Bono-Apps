<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Password Berhasil Diubah</title></head>
<body style="font-family:Arial,sans-serif; background:#f4f6f9; margin:0; padding:20px;">
  <table width="100%" cellpadding="0" cellspacing="0">
    <tr><td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="background:#fff; border-radius:12px; overflow:hidden;">
        <tr><td style="background:#0a1f3c; padding:24px; text-align:center;">
          <h1 style="color:#fff; font-size:20px; margin:0;">PERADI Pro Bono</h1>
        </td></tr>
        <tr><td style="padding:32px;">
          <h2 style="color:#0a1f3c; font-size:18px; margin-top:0;">Password Berhasil Diubah</h2>
          <p>Yth. <strong><?= esc($user['nama_lengkap']) ?></strong>,</p>
          <p>Password akun Anda baru saja diubah pada:</p>
          <table style="font-size:14px; color:#374151; margin:16px 0;">
            <tr><td><strong>Waktu:</strong></td><td><?= esc($waktu) ?></td></tr>
            <tr><td><strong>IP Address:</strong></td><td><?= esc($ip) ?></td></tr>
          </table>
          <div style="background:#fee2e2; border-left:4px solid #ef4444; padding:12px; margin-top:20px; font-size:13px;">
            <strong>Jika bukan Anda yang melakukan ini</strong>, segera hubungi admin PERADI untuk mengamankan akun Anda.
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