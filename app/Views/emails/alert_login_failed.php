<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Peringatan Login Gagal</title></head>
<body style="font-family:Arial,sans-serif; background:#f4f6f9; margin:0; padding:20px;">
  <table width="100%" cellpadding="0" cellspacing="0">
    <tr><td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="background:#fff; border-radius:12px; overflow:hidden;">
        <tr><td style="background:#dc2626; padding:24px; text-align:center;">
          <h1 style="color:#fff; font-size:20px; margin:0;">⚠️ Peringatan Keamanan</h1>
        </td></tr>
        <tr><td style="padding:32px;">
          <h2 style="color:#dc2626; font-size:18px; margin-top:0;">Percobaan Login Gagal Berulang</h2>
          <p>Sistem mendeteksi percobaan login gagal yang mencurigakan:</p>
          <table style="font-size:14px; color:#374151; margin:16px 0; width:100%; border-collapse:collapse;">
            <tr><td style="padding:6px 0;"><strong>Email yang dicoba:</strong></td><td><?= esc($email_attempted) ?></td></tr>
            <tr><td style="padding:6px 0;"><strong>IP Address:</strong></td><td><?= esc($ip) ?></td></tr>
            <tr><td style="padding:6px 0;"><strong>User Agent:</strong></td><td style="word-break:break-all;"><?= esc($user_agent) ?></td></tr>
            <tr><td style="padding:6px 0;"><strong>Waktu:</strong></td><td><?= esc($waktu) ?></td></tr>
            <tr><td style="padding:6px 0;"><strong>Gagal dari IP ini:</strong></td><td><?= esc($failed_ip) ?> kali dalam <?= esc($window_ip) ?> menit</td></tr>
            <tr><td style="padding:6px 0;"><strong>Gagal untuk email ini:</strong></td><td><?= esc($failed_email) ?> kali dalam <?= esc($window_email) ?> menit</td></tr>
          </table>
          <div style="background:#fef3c7; border-left:4px solid #f59e0b; padding:12px; margin-top:20px; font-size:13px;">
            <strong>Tindakan yang disarankan:</strong>
            <ul style="margin:8px 0 0 0; padding-left:20px;">
              <li>Cek apakah ini serangan brute force</li>
              <li>Blokir IP tersebut jika perlu</li>
              <li>Hubungi pemilik akun untuk verifikasi</li>
              <li>Lihat log lengkap di <a href="<?= base_url('admin/log-action') ?>">Log Action</a></li>
            </ul>
          </div>
        </td></tr>
        <tr><td style="background:#f9fafb; padding:16px; text-align:center; font-size:12px; color:#6b7280;">
          Email otomatis dari Sistem PERADI Pro Bono. Jangan balas email ini.
        </td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>