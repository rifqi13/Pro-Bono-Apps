<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>


<section class="contact section light-background">
  <div class="container" data-aos="fade-up">
    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show"><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
  </div>
</section>

<div class="page-title light-background">
  <div class="container">
    <h1>Manajemen User</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="<?= base_url('advokat/dashboard') ?>">Dashboard</a></li>
        <li class="current">Manajemen User</li>
      </ol>
    </nav>
  </div>
</div>

<section class="contact section light-background">
  <div class="container" data-aos="fade-up">

    <div class="row g-3">
      <div class="col-md-6">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h6 class="fw-bold">Informasi User</h6>
            <table class="table table-sm">
              <tr>
                <th>Nama</th>
                <td><?= esc($user['nama_lengkap']) ?></td>
              </tr>
              <tr>
                <th>NIA</th>
                <td><?= esc($user['nia'] ?? '-') ?></td>
              </tr>
              <tr>
                <th>Email</th>
                <td><?= esc($user['email']) ?></td>
              </tr>
              <tr>
                <th>No. WA</th>
                <td><?= esc($user['no_wa']) ?></td>
              </tr>
              <tr>
                <th>Role</th>
                <td><?= esc($user['role']) ?></td>
              </tr>
              <tr>
                <th>Cabang</th>
                <td><?= esc($user['cabang_nama'] ?? '-') ?></td>
              </tr>
              <tr>
                <th>Status</th>
                <td><?= esc($user['status']) ?></td>
              </tr>
              <tr>
                <th>Terakhir Login</th>
                <td><?= esc($user['last_login_at'] ?? '-') ?></td>
              </tr>
            </table>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card border-0 shadow-sm mb-3">
          <a href="<?= base_url('admin/users') ?>" class="btn btn-sm btn-outline-secondary">← Kembali</a>
          <div class="card-body">
            <h6 class="fw-bold">Aksi</h6>

            <?php if ($user['status'] === 'pending'): ?>
              <form method="post" action="<?= base_url('admin/users/' . $user['id'] . '/activate') ?>" class="mb-2">
                <?= csrf_field() ?>
                <button class="btn btn-success w-100"><i class="bi bi-check-circle"></i> Aktifkan Akun</button>
              </form>
            <?php endif; ?>

            <?php if ($user['status'] === 'aktif' && $user['role'] !== 'super_admin'): ?>
              <form method="post" action="<?= base_url('admin/users/' . $user['id'] . '/suspend') ?>" class="mb-2" onsubmit="return confirm('Suspend user ini?')">
                <?= csrf_field() ?>
                <button class="btn btn-danger w-100"><i class="bi bi-pause-circle"></i> Suspend</button>
              </form>
            <?php endif; ?>
          </div>
        </div>

        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h6 class="fw-bold">Reset Password</h6>
            <form method="post" action="<?= base_url('admin/users/' . $user['id'] . '/reset-password') ?>">
              <?= csrf_field() ?>
              <div class="mb-2">
                <label class="form-check">
                  <input type="radio" name="method" value="generate" class="form-check-input" checked>
                  <span class="form-check-label" style="font-size:13px;">Generate password baru (tampil sekali)</span>
                </label>
                <label class="form-check">
                  <input type="radio" name="method" value="email_link" class="form-check-input">
                  <span class="form-check-label" style="font-size:13px;">Kirim link reset via email</span>
                </label>
              </div>
              <button class="btn btn-warning w-100" onclick="return confirm('Reset password user ini?')">
                <i class="bi bi-key"></i> Reset Password
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>



<?= $this->endSection() ?>