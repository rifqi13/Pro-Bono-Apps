<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<div class="text-center">
  <i class="bi bi-envelope-check" style="font-size:64px; color:#10b981;"></i>
  <h1 class="mt-3">Registrasi Berhasil!</h1>
  <p class="subtitle">Cek email Anda untuk verifikasi akun.</p>

  <p style="font-size:14px; color:#374151;">
    Kami telah mengirim link verifikasi ke email Anda.
    Klik link tersebut untuk mengaktifkan akun Anda.
  </p>

  <div class="alert alert-info mt-3" style="font-size:13px;">
    <strong>Tips:</strong> Cek folder <em>Spam</em> atau <em>Promotions</em> jika email tidak ditemukan dalam 5 menit.
  </div>

  <a href="<?= base_url('auth/login') ?>" class="btn btn-primary mt-3">Kembali ke Login</a>
</div>

<?= $this->endSection() ?>