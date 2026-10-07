<!DOCTYPE html>
<html><head><meta charset="utf-8"></head>
<body style="font-family:Arial,sans-serif; background:#f4f6f9; padding:20px;">
<table width="100%"><tr><td align="center">
<table width="600" style="background:#fff; border-radius:12px; overflow:hidden;">
<tr><td style="background:#0a1f3c; padding:24px; text-align:center;"><h1 style="color:#fff; font-size:20px; margin:0;">PERADI Pro Bono</h1></td></tr>
<tr><td style="padding:32px;">
  <h2 style="color:#0a1f3c; font-size:18px;">Akun Anda Telah Diaktifkan</h2>
  <p>Yth. <strong><?= esc($user['nama_lengkap']) ?></strong>,</p>
  <p>Akun Anda di sistem PERADI Pro Bono telah diaktifkan. Silakan login menggunakan email dan password yang telah Anda daftarkan.</p>
  <p style="text-align:center; margin:30px 0;">
    <a href="<?= esc($loginUrl) ?>" style="background:#0a1f3c; color:#fff; padding:12px 32px; text-decoration:none; border-radius:8px; font-weight:600;">Login Sekarang</a>
  </p>
</td></tr>
</table>
</td></tr></table>
</body></html>