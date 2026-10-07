<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title ?? 'Login') ?> - PERADI Pro Bono</title>

  <link href="<?= base_url('assets/img/favicon.png') ?>" rel="icon">
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Inter:wght@300;400;500;600;700&family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">

  <link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/vendor/aos/aos.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/main.css') ?>" rel="stylesheet">

  <style>
    body { background: #f4f6f9; min-height: 100vh; display: flex; flex-direction: column; }
    .auth-wrapper { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 15px; }
    .auth-card { max-width: 480px; width: 100%; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.06); padding: 40px; }
    .auth-card .logo { text-align: center; margin-bottom: 24px; }
    .auth-card .logo img { max-height: 50px; }
    .auth-card h1 { font-size: 22px; text-align: center; color: #0a1f3c; margin-bottom: 8px; font-weight: 700; }
    .auth-card p.subtitle { text-align: center; color: #6b7280; font-size: 14px; margin-bottom: 28px; }
    .auth-card .form-label { font-size: 14px; font-weight: 600; color: #374151; }
    .auth-card .form-control { padding: 10px 14px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 14px; }
    .auth-card .form-control:focus { border-color: #0a1f3c; box-shadow: 0 0 0 3px rgba(10,31,60,0.1); }
    .auth-card .btn-primary { background: #0a1f3c; border: none; padding: 11px; border-radius: 8px; font-weight: 600; width: 100%; }
    .auth-card .btn-primary:hover { background: #1862b5; }
    .auth-card .btn-primary:disabled { background: #b0b7c3; cursor: not-allowed; }
    .auth-footer { text-align: center; margin-top: 20px; font-size: 14px; color: #6b7280; }
    .auth-footer a { color: #0a1f3c; font-weight: 600; text-decoration: none; }
    .auth-footer a:hover { text-decoration: underline; }
    .alert { font-size: 14px; border-radius: 8px; }
  </style>
</head>
<body>

<main class="auth-wrapper">
  <div class="auth-card" data-aos="fade-up">
    <div class="logo">
      <img src="<?= base_url('assets/img/Peradi_logo_v3.png') ?>" alt="PERADI">
    </div>

    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('errors')): ?>
      <div class="alert alert-danger">
        <ul class="mb-0 ps-3">
          <?php foreach ((array) session()->getFlashdata('errors') as $err): ?>
            <li><?= esc($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?= $this->renderSection('content') ?>
  </div>
</main>

<script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/aos/aos.js') ?>"></script>
<script>AOS.init();</script>
</body>
</html>