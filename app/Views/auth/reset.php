<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<h1>Reset Password</h1>
<p class="subtitle">Masukkan password baru Anda.</p>

<form action="<?= base_url('auth/reset') ?>" method="post" novalidate>
  <?= csrf_field() ?>
  <input type="hidden" name="email" value="<?= esc($email) ?>">
  <input type="hidden" name="token" value="<?= esc($token) ?>">

  <div class="mb-3">
    <label class="form-label">Password Baru <span class="text-danger">*</span></label>
    <input type="password" name="password" class="form-control" required minlength="8">
    <small class="text-muted">Minimal 8 karakter, kombinasi huruf dan angka</small>
  </div>

  <div class="mb-3">
    <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
    <input type="password" name="confirm" class="form-control" required minlength="8">
  </div>

  <button type="submit" class="btn btn-primary">Simpan Password Baru</button>
</form>

<?= $this->endSection() ?>