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
    <h1>Arsip Log Aktivitas</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="<?= base_url('advokat/dashboard') ?>">Dashboard</a></li>
        <li class="current">Arsip Log Aktivitas</li>
      </ol>
    </nav>
  </div>
</div>

<section class="contact section light-background">
  <div class="container" data-aos="fade-up">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h6 class="fw-bold mb-0">Arsip Log Aktivitas</h6>
        <small class="text-muted">Total: <?= number_format($total, 0, ',', '.') ?> baris arsip</small>
      </div>
      <div class="d-flex gap-2">
        <a href="<?= base_url('admin/log-archive/download') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i> Download Semua CSV</a>
        <a href="<?= base_url('admin/log-action') ?>" class="btn btn-sm btn-outline-secondary">← Kembali ke Log Aktif</a>
      </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form method="get" class="row g-2">
          <div class="col-md-2"><input type="text" name="q" class="form-control form-control-sm" placeholder="Cari deskripsi" value="<?= esc($filters['q']) ?>"></div>
          <div class="col-md-2">
            <select name="action" class="form-select form-select-sm">
              <option value="">Semua Action</option>
              <?php foreach (['login_success', 'login_failed', 'logout', 'submit_pengajuan', 'verify_approve'] as $a): ?>
                <option value="<?= $a ?>" <?= $filters['action'] === $a ? 'selected' : '' ?>><?= $a ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <select name="module" class="form-select form-select-sm">
              <option value="">Semua Module</option>
              <?php foreach (['auth', 'pengajuan', 'verifikasi', 'security'] as $m): ?>
                <option value="<?= $m ?>" <?= $filters['module'] === $m ? 'selected' : '' ?>><?= $m ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2"><input type="date" name="dari" class="form-control form-control-sm" value="<?= esc($filters['dari']) ?>"></div>
          <div class="col-md-2"><input type="date" name="sampai" class="form-control form-control-sm" value="<?= esc($filters['sampai']) ?>"></div>
          <div class="col-md-2"><button class="btn btn-primary btn-sm w-100">Filter</button></div>
        </form>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm table-hover">
            <thead class="table-light">
              <tr>
                <th>Waktu Asli</th>
                <th>Diarsipkan</th>
                <th>Role</th>
                <th>Action</th>
                <th>Module</th>
                <th>Deskripsi</th>
                <th>IP</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($logs)): ?>
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">Arsip kosong.</td>
                </tr>
                <?php else: foreach ($logs as $l): ?>
                  <tr>
                    <td style="font-size:12px;"><?= date('d M Y H:i', strtotime($l['created_at'])) ?></td>
                    <td style="font-size:11px; color:#6b7280;"><?= date('d M Y', strtotime($l['archived_at'])) ?></td>
                    <td><span class="badge bg-secondary" style="font-size:10px;"><?= esc($l['user_role'] ?? 'guest') ?></span></td>
                    <td><code style="font-size:11px;"><?= esc($l['action']) ?></code></td>
                    <td style="font-size:12px;"><?= esc($l['module']) ?></td>
                    <td style="font-size:12px; max-width:400px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= esc($l['description']) ?></td>
                    <td><code style="font-size:11px;"><?= esc($l['ip_address']) ?></code></td>
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