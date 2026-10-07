<?= $this->extend('layouts/cabang') ?>
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
    <h1>List Advokat Cabang</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="<?= base_url('advokat/dashboard') ?>">Dashboard</a></li>
        <li class="current">List Advokat</li>
      </ol>
    </nav>
  </div>
</div>

<section class="contact section light-background">
  <div class="container" data-aos="fade-up">
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form method="get" class="row g-2">
          <div class="col-md-4"><input type="text" name="q" class="form-control form-control-sm" placeholder="Cari nama / NIA / email" value="<?= esc($filters['q']) ?>"></div>
          <div class="col-md-3">
            <select name="status" class="form-select form-select-sm">
              <option value="">Semua Status</option>
              <?php foreach (['pending', 'aktif', 'nonaktif', 'suspend'] as $s): ?>
                <option value="<?= $s ?>" <?= $filters['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2"><button class="btn btn-primary btn-sm w-100">Filter</button></div>
        </form>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <h6 class="fw-bold mb-3">Daftar Advokat Cabang <span class="badge bg-secondary"><?= count($list) ?></span></h6>
        <div class="table-responsive">
          <table class="table table-sm table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>Nama</th>
                <th>NIA</th>
                <th>Email</th>
                <th>No. WA</th>
                <th>Status</th>
                <th>Terdaftar</th>
                <th>Terakhir Login</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($list)): ?>
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">Belum ada advokat terdaftar di cabang ini.</td>
                </tr>
                <?php else: foreach ($list as $u): ?>
                  <tr>
                    <td><?= esc($u['nama_lengkap']) ?><?= $u['gelar'] ? ', ' . esc($u['gelar']) : '' ?></td>
                    <td><code><?= esc($u['nia'] ?? '-') ?></code></td>
                    <td style="font-size:13px;"><?= esc($u['email']) ?></td>
                    <td style="font-size:13px;"><?= esc($u['no_wa']) ?></td>
                    <td>
                      <?php $badge = match ($u['status']) {
                        'aktif' => 'success',
                        'pending' => 'warning',
                        'suspend' => 'danger',
                        default => 'secondary'
                      }; ?>
                      <span class="badge bg-<?= $badge ?>"><?= esc($u['status']) ?></span>
                    </td>
                    <td style="font-size:12px;"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                    <td style="font-size:12px;"><?= $u['last_login_at'] ? date('d M Y H:i', strtotime($u['last_login_at'])) : '-' ?></td>
                  </tr>
              <?php endforeach;
              endif; ?>
            </tbody>
          </table>
        </div>
        <?= $pager->links() ?>
      </div>
    </div>
  </div>
</section>




<?= $this->endSection() ?>