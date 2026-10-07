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
    <h1>List Verifikasi</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="<?= base_url('advokat/dashboard') ?>">Dashboard</a></li>
        <li class="current">List Verifikasi</li>
      </ol>
    </nav>
  </div>
</div>

<section class="contact section light-background">
  <div class="container" data-aos="fade-up">
    <!-- Pending Advokat Alert -->
    <?php if (!empty($pendingAdvokat)): ?>
      <div class="alert alert-info">
        <strong><i class="bi bi-person-plus"></i> <?= count($pendingAdvokat) ?> advokat menunggu aktivasi.</strong>
        <a href="<?= base_url('admin/users?status=pending') ?>" class="alert-link ms-2">Lihat</a>
      </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form method="get" class="row g-2">
          <div class="col-md-2"><input type="text" name="q" class="form-control form-control-sm" placeholder="Cari No.Reg/Nama/NIA" value="<?= esc($filters['q']) ?>"></div>
          <div class="col-md-2">
            <select name="cabang_id" class="form-select form-select-sm">
              <option value="">Semua Cabang</option>
              <?php foreach ($cabangList as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $filters['cabang_id'] == $c['id'] ? 'selected' : '' ?>><?= esc($c['nama']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
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
          <div class="col-md-2 d-flex gap-1">
            <button class="btn btn-primary btn-sm flex-grow-1">Filter</button>
            <label class="btn btn-outline-warning btn-sm" title="Hanya yang ada warning">
              <input type="checkbox" name="warning" value="1" <?= $filters['warning'] ? 'checked' : '' ?> onchange="this.form.submit()"> ⚠️
            </label>
          </div>
        </form>
      </div>
    </div>

    <form method="post" action="<?= base_url('admin/verifikasi/bulk-approve') ?>" id="bulkForm">
      <?= csrf_field() ?>
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0">Daftar Pengajuan</h6>
            <div>
              <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#bulkModal">
                <i class="bi bi-check2-all"></i> Bulk Approve Terpilih
              </button>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th width="30"><input type="checkbox" id="checkAll"></th>
                  <th>No. Registrasi</th>
                  <th>Advokat</th>
                  <th>Cabang</th>
                  <th>Jenis</th>
                  <th>Submitted</th>
                  <th>Status</th>
                  <th>Warning</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($list)): ?>
                  <tr>
                    <td colspan="9" class="text-center text-muted py-4">Tidak ada data.</td>
                  </tr>
                  <?php else: foreach ($list as $p): ?>
                    <tr>
                      <td><input type="checkbox" name="ids[]" value="<?= $p['id'] ?>" class="row-check"></td>
                      <td><code><?= esc($p['no_registrasi']) ?></code></td>
                      <td><?= esc($p['advokat_nama']) ?><br><small class="text-muted"><?= esc($p['advokat_nia']) ?></small></td>
                      <td><?= esc($p['cabang_nama']) ?></td>
                      <td><?= esc($p['jenis_layanan']) ?><?= $p['jenis_non_litigasi'] ? ' - ' . esc($p['jenis_non_litigasi']) : '' ?></td>
                      <td><?= $p['submitted_at'] ? date('d M Y H:i', strtotime($p['submitted_at'])) : '-' ?></td>
                      <td>
                        <?php $badge = match ($p['status']) {
                          'approved' => 'success',
                          'rejected' => 'danger',
                          'review', 'submitted' => 'warning',
                          default => 'secondary'
                        }; ?>
                        <span class="badge bg-<?= $badge ?>"><?= ucfirst($p['status']) ?></span>
                      </td>
                      <td>
                        <?php if ($p['has_warning']): ?>
                          <span class="badge bg-warning text-dark">⚠️ <?= $p['warning_count'] ?></span>
                          <?php else: ?>-<?php endif; ?>
                      </td>
                      <td><a href="<?= base_url('admin/verifikasi/' . $p['id']) ?>" class="btn btn-sm btn-primary">Detail</a></td>
                    </tr>
                <?php endforeach;
                endif; ?>
              </tbody>
            </table>
          </div>
          <?= $pager->links() ?>
        </div>
      </div>

      <!-- Bulk Modal -->
      <!-- Modal Bulk Approve -->
      <div class="modal fade" id="bulkModal" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Bulk Approve</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <p>Anda akan menyetujui <strong id="bulkCount">0</strong> pengajuan.</p>

              <div class="mb-3">
                <label class="form-label fw-semibold">
                  Durasi Penanganan Default (Jam) <span class="text-danger">*</span>
                </label>
                <input type="number" name="durasi_jam_default" class="form-control"
                  min="1" max="9999" value="24" required>
                <small class="text-muted">Akan diterapkan ke semua pengajuan yang dipilih.</small>
              </div>

              <div class="mb-3">
                <label class="form-label">Catatan Verifikator (opsional)</label>
                <textarea name="catatan_verifikator" class="form-control" rows="3"></textarea>
              </div>

              <div class="form-check">
                <input type="checkbox" name="skip_invalid" value="1" class="form-check-input" id="skipInvalid" checked>
                <label class="form-check-label" for="skipInvalid" style="font-size:13px;">
                  Lewati pengajuan yang dokumennya tidak lengkap
                </label>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-success">Proses</button>
            </div>
          </div>
        </div>
      </div>
    </form>

    <?php if (session()->getFlashdata('bulk_errors')): ?>
      <div class="alert alert-warning mt-3">
        <strong>Sebagian pengajuan dilewati:</strong>
        <ul class="mb-0">
          <?php foreach ((array) session()->getFlashdata('bulk_errors') as $err): ?>
            <li><?= esc($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
  </div>
</section>


<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  document.getElementById('checkAll').addEventListener('change', function() {
    document.querySelectorAll('.row-check').forEach(c => c.checked = this.checked);
    updateCount();
  });
  document.querySelectorAll('.row-check').forEach(c => c.addEventListener('change', updateCount));

  function updateCount() {
    document.getElementById('bulkCount').textContent = document.querySelectorAll('.row-check:checked').length;
  }
</script>
<?= $this->endSection() ?>