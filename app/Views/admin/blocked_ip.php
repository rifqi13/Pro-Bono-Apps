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
    <h1>IP Terblokir</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="<?= base_url('advokat/dashboard') ?>">Dashboard</a></li>
        <li class="current">IP Terblokir</li>
      </ol>
    </nav>
  </div>
</div>

<section class="contact section light-background">
  <div class="container" data-aos="fade-up">
<div class="row g-3">
  <!-- IP Diblokir -->
  <div class="col-md-7">
    <div class="card border-0 shadow-sm"><div class="card-body">
      <div class="d-flex justify-content-between mb-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-shield-x text-danger"></i> IP Diblokir Aktif</h6>
        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#blockModal">
          <i class="bi bi-plus-circle"></i> Blokir IP Manual
        </button>
      </div>
      <div class="table-responsive">
        <table class="table table-sm">
          <thead class="table-light"><tr>
            <th>IP</th><th>Alasan</th><th>Tipe</th><th>Percobaan</th><th>Expired</th><th>Aksi</th>
          </tr></thead>
          <tbody>
          <?php if (empty($blocked)): ?>
            <tr><td colspan="6" class="text-center text-muted py-3">Tidak ada IP diblokir.</td></tr>
          <?php else: foreach ($blocked as $b): ?>
            <tr>
              <td><code><?= esc($b['ip_address']) ?></code></td>
              <td style="font-size:12px;"><?= esc($b['reason']) ?></td>
              <td>
                <span class="badge bg-<?= $b['reason_type'] === 'auto' ? 'warning' : 'danger' ?>" style="font-size:10px;">
                  <?= esc($b['reason_type']) ?>
                </span>
              </td>
              <td><?= $b['attempt_count'] ?>x</td>
              <td style="font-size:12px;">
                <?= $b['expired_at'] ? date('d M Y H:i', strtotime($b['expired_at'])) : '<span class="text-danger">Permanen</span>' ?>
              </td>
              <td>
                <form method="post" action="<?= base_url('admin/blocked-ip/unblock') ?>" class="d-inline">
                  <?= csrf_field() ?>
                  <input type="hidden" name="ip_address" value="<?= esc($b['ip_address']) ?>">
                  <button class="btn btn-sm btn-outline-success" onclick="return confirm('Unblock IP ini?')">Unblock</button>
                </form>
                <form method="post" action="<?= base_url('admin/blocked-ip/whitelist') ?>" class="d-inline">
                  <?= csrf_field() ?>
                  <input type="hidden" name="ip_address" value="<?= esc($b['ip_address']) ?>">
                  <input type="hidden" name="keterangan" value="Ditambahkan dari halaman blokir">
                  <button class="btn btn-sm btn-outline-info" onclick="return confirm('Masukkan IP ke whitelist?')">Whitelist</button>
                </form>
              </td>
            </tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
      <?= $pager->links() ?>
    </div></div>
  </div>

  <!-- Whitelist -->
  <div class="col-md-5">
    <div class="card border-0 shadow-sm"><div class="card-body">
      <div class="d-flex justify-content-between mb-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-shield-check text-success"></i> IP Whitelist</h6>
        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#whitelistModal">
          <i class="bi bi-plus-circle"></i> Tambah
        </button>
      </div>
      <div class="table-responsive">
        <table class="table table-sm">
          <thead class="table-light"><tr><th>IP</th><th>Keterangan</th><th>Aksi</th></tr></thead>
          <tbody>
          <?php if (empty($whitelist)): ?>
            <tr><td colspan="3" class="text-center text-muted py-3">Whitelist kosong.</td></tr>
          <?php else: foreach ($whitelist as $w): ?>
            <tr>
              <td><code><?= esc($w['ip_address']) ?></code></td>
              <td style="font-size:12px;"><?= esc($w['keterangan'] ?? '-') ?></td>
              <td>
                <form method="post" action="<?= base_url('admin/blocked-ip/remove-whitelist/' . $w['id']) ?>" class="d-inline">
                  <?= csrf_field() ?>
                  <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus dari whitelist?')"><i class="bi bi-trash"></i></button>
                </form>
              </td>
            </tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
      <?= $pagerW->links() ?>
    </div></div>
  </div>
</div>

<!-- Modal Blokir Manual -->
<div class="modal fade" id="blockModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="<?= base_url('admin/blocked-ip/block') ?>">
        <?= csrf_field() ?>
        <div class="modal-header"><h5 class="modal-title">Blokir IP Manual</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">IP Address</label>
            <input type="text" name="ip_address" class="form-control" required placeholder="192.168.1.1">
          </div>
          <div class="mb-3">
            <label class="form-label">Alasan</label>
            <input type="text" name="reason" class="form-control" required placeholder="Contoh: Serangan brute force">
          </div>
          <div class="mb-3">
            <label class="form-label">Durasi (jam) — kosongkan untuk permanen</label>
            <input type="number" name="expire_hours" class="form-control" min="1" max="8760" placeholder="24">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Blokir</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Whitelist -->
<div class="modal fade" id="whitelistModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="<?= base_url('admin/blocked-ip/whitelist') ?>">
        <?= csrf_field() ?>
        <div class="modal-header"><h5 class="modal-title">Tambah ke Whitelist</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">IP Address</label>
            <input type="text" name="ip_address" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Keterangan</label>
            <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Kantor DPN">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Tambah</button>
        </div>
      </form>
    </div>
  </div>
</div>
  </div>
</section>



<?= $this->endSection() ?>