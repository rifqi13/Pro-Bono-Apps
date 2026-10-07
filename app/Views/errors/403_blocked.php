<!DOCTYPE html>
<html>
<head>
  <title>Akses Diblokir</title>
  <link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.css') ?>" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height:100vh; background:#f4f6f9;">
  <div class="text-center" style="max-width:500px;">
    <i class="bi bi-shield-x" style="font-size:80px; color:#dc2626;"></i>
    <h2 class="mt-3">Akses Diblokir</h2>
    <p class="text-muted">IP Anda (<code><?= esc($ip) ?></code>) telah diblokir dari sistem.</p>
    <p class="text-muted" style="font-size:14px;">
      Alasan: <?= esc($reason) ?><br>
      Durasi: <?= esc($expire) ?>
    </p>
    <p style="font-size:13px;">Jika Anda merasa ini kesalahan, hubungi admin PERADI.</p>
    <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary mt-3">Kembali ke Beranda</a>
  </div>
</body>
</html>