<!DOCTYPE html>
<html>
<head><title>429 - Terlalu Banyak Permintaan</title>
<link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height:100vh; background:#f4f6f9;">
  <div class="text-center">
    <h1 style="font-size:72px; color:#0a1f3c;">429</h1>
    <p class="text-muted">Terlalu banyak permintaan. Silakan coba lagi dalam <?= esc($retryAfter ?? 60) ?> detik.</p>
    <a href="<?= base_url('/') ?>" class="btn btn-primary">Kembali ke Beranda</a>
  </div>
</body>
</html>