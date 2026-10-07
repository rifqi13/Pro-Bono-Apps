<!DOCTYPE html>
<html><head><meta charset="utf-8"></head>
<body style="font-family:Arial,sans-serif; background:#f4f6f9; padding:20px;">
<table width="100%"><tr><td align="center">
<table width="600" style="background:#fff; border-radius:12px; overflow:hidden;">
<tr><td style="background:#dc2626; padding:24px; text-align:center;">
  <h1 style="color:#fff; font-size:20px; margin:0;">🛡️ IP Otomatis Diblokir</h1>
</td></tr>
<tr><td style="padding:32px;">
  <p>Sistem mendeteksi percobaan login berulang dari IP berikut dan <strong>memblokirnya otomatis</strong>:</p>
  <table style="font-size:14px; color:#374151; margin:16px 0;">
    <tr><td><strong>IP Address:</strong></td><td><code><?= esc($ip) ?></code></td></tr>
    <tr><td><strong>Jumlah percobaan:</strong></td><td><?= esc($attempts) ?> kali</td></tr>
    <tr><td><strong>Waktu:</strong></td><td><?= esc($waktu) ?></td></tr>
    <tr><td><strong>Durasi blokir:</strong></td><td><?= esc($durasi) ?></td></tr>
  </table>
  <p style="text-align:center; margin:30px 0;">
    <a href="<?= esc($url_unblock) ?>" style="background:#0a1f3c; color:#fff; padding:12px 32px; text-decoration:none; border-radius:8px; font-weight:600;">Kelola IP</a>
  </p>
  <div style="background:#fef3c7; border-left:4px solid #f59e0b; padding:12px; font-size:13px;">
    Jika ini kesalahan (misal IP kantor Anda), segera tambahkan ke whitelist.
  </div>
</td></tr>
</table>
</td></tr></table>
</body></html>