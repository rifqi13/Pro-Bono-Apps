<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<div class="text-center">
  <i class="bi bi-hourglass-split" style="font-size:64px; color:#f59e0b;"></i>
  <h1 class="mt-3">Verifikasi Email</h1>
  <p class="subtitle">Akun Anda belum diverifikasi.</p>

  <p style="font-size:14px; color:#374151;">
    Masukkan email yang terdaftar untuk menerima ulang link verifikasi.
  </p>

  <form action="<?= base_url('auth/resend-verification') ?>" method="post" class="mt-3">
    <?= csrf_field() ?>
    <div class="mb-3">
      <input type="email" name="email" class="form-control" required placeholder="nama@email.com">
    </div>
    <button type="submit" class="btn btn-primary">Kirim Ulang Verifikasi</button>
  </form>

  <a href="<?= base_url('auth/login') ?>" class="d-block mt-3" style="font-size:13px;">← Kembali ke Login</a>
</div>

<?= $this->endSection() ?>