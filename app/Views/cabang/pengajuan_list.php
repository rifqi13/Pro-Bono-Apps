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
    <h1>List Bantuan Hukum</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="<?= base_url('advokat/dashboard') ?>">Dashboard</a></li>
        <li class="current">List Bantuan Hukum</li>
      </ol>
    </nav>
  </div>
</div>

<section class="contact section light-background">
  <div class="container" data-aos="fade-up">
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form method="get" class="row g-2">
          <div class="col-md-3"><input type="text" name="q" class="form-control form-control-sm" placeholder="Cari No. Reg / Advokat / NIA" value="<?= esc($filters['q']) ?>"></div>
          <div class="col-md-2">
            <select name="status" class="form-select form-select-sm">
              <option value="">Semua Status</option>
              <?php foreach (['submitted', 'review', 'approved', 'rejected'] as $s): ?>
                <option value="<?= $s ?>" <?= $filters['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <select name="jenis_layanan" class="form-select form-select-sm">
              <option value="">Semua Jenis</option>
              <option value="litigasi" <?= $filters['jenis_layanan'] === 'litigasi' ? 'selected' : '' ?>>Litigasi</option>
              <option value="non-litigasi" <?= $filters['jenis_layanan'] === 'non-litigasi' ? 'selected' : '' ?>>Non-Litigasi</option>
            </select>
          </div>
          <div class="col-md-2">
            <select name="tahun" class="form-select form-select-sm">
              <option value="">Semua Tahun</option>
              <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                <option value="<?= $y ?>" <?= $filters['tahun'] == $y ? 'selected' : '' ?>><?= $y ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="col-md-2"><button class="btn btn-primary btn-sm w-100">Filter</button></div>
        </form>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <h6 class="fw-bold mb-3">Riwayat Bantuan Hukum Cabang</h6>
        <div class="table-responsive">
          <table class="table table-sm table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>No. Registrasi</th>
                <th>Tahun</th>
                <th>Advokat</th>
                <th>Jenis Perkara</th>
                <th>Durasi</th>
                <th>Status</th>
                <th>Warning</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($list)): ?>
                <tr>
                  <td colspan="8" class="text-center text-muted py-4">Belum ada pengajuan.</td>
                </tr>
                <?php else: foreach ($list as $p): ?>
                  <?php $badge = match ($p['status']) {
                    'approved' => 'success',
                    'rejected' => 'danger',
                    'review', 'submitted' => 'warning',
                    default => 'secondary'
                  }; ?>
                  <tr>
                    <td><code style="font-size:11px;"><?= esc($p['no_registrasi']) ?></code></td>
                    <td><?= esc($p['tahun']) ?></td>
                    <td style="font-size:13px;"><?= esc($p['advokat_nama']) ?><br><small class="text-muted"><?= esc($p['advokat_nia']) ?></small></td>
                    <td><?= esc($p['jenis_layanan']) ?><?= $p['jenis_non_litigasi'] ? ' - ' . esc($p['jenis_non_litigasi']) : '' ?></td>
                    <td style="font-size:12px;"><?= $p['submitted_at'] ? date('d M Y', strtotime($p['submitted_at'])) : '-' ?></td>
                    <td><span class="badge bg-<?= $badge ?>"><?= ucfirst($p['status']) ?></span></td>
                    <td><?php if ($p['has_warning']): ?><span class="badge bg-warning text-dark">⚠️ <?= $p['warning_count'] ?></span><?php else: ?>-<?php endif; ?></td>
                    <td><a href="<?= base_url('cabang/pengajuan/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary">Lihat</a></td>
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