<?= $this->extend('layouts/admin') ?>

<?= $this->section('styles') ?>
<style>
  .chart-card {
    border-radius: 12px;
    border: none;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  }

  .stat-card {
    border-radius: 12px;
    border: none;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  }

  .stat-card .label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6b7280;
  }

  .stat-card .value {
    font-size: 24px;
    font-weight: 700;
    color: #0a1f3c;
  }

  .action-badge {
    font-size: 10px;
    padding: 3px 8px;
    border-radius: 4px;
  }

  .log-row:hover {
    background: #f9fafb;
    cursor: pointer;
  }
</style>
<?= $this->endSection() ?>

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
    <h1>Log Action Pengguna</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="<?= base_url('advokat/dashboard') ?>">Dashboard</a></li>
        <li class="current">Log Action Pengguna</li>
      </ol>
    </nav>
  </div>
</div>

<section class="contact section light-background">
  <div class="container" data-aos="fade-up">

    <?php if ($needBackup): ?>
      <div class="alert alert-warning d-flex justify-content-between align-items-center">
        <div>
          <i class="bi bi-exclamation-triangle"></i>
          <strong>Pengingat Backup:</strong>
          <?php if ($daysSinceBackup === null): ?>
            Belum pernah ada backup log.
          <?php else: ?>
            Terakhir backup <strong><?= $daysSinceBackup ?> hari</strong> yang lalu.
          <?php endif; ?>
        </div>
        <form method="post" action="<?= base_url('admin/log-action/backup') ?>" class="m-0">
          <?= csrf_field() ?>
          <button class="btn btn-warning btn-sm"><i class="bi bi-download"></i> Backup Sekarang</button>
        </form>
      </div>
    <?php endif; ?>


    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <div class="card stat-card">
          <div class="card-body">
            <div class="label"><i class="bi bi-database"></i> Log Aktif</div>
            <div class="value"><?= number_format($stats['total_active'], 0, ',', '.') ?></div>
            <small class="text-muted">Sejak <?= $stats['oldest_active'] ? date('d M Y', strtotime($stats['oldest_active'])) : '-' ?></small>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card stat-card">
          <div class="card-body">
            <div class="label"><i class="bi bi-archive"></i> Log Arsip</div>
            <div class="value"><?= number_format($stats['total_archive'], 0, ',', '.') ?></div>
            <a href="<?= base_url('admin/log-archive') ?>" class="btn btn-sm btn-link p-0" style="font-size:12px;">Lihat arsip →</a>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card stat-card">
          <div class="card-body">
            <div class="label"><i class="bi bi-clock-history"></i> Akan Diarsipkan</div>
            <div class="value text-warning"><?= number_format($stats['will_archive'], 0, ',', '.') ?></div>
            <small class="text-muted">Log &gt; 90 hari</small>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card stat-card">
          <div class="card-body">
            <div class="label"><i class="bi bi-clock"></i> Backup Terakhir</div>
            <div class="value" style="font-size:18px;">
              <?= $daysSinceBackup === null ? 'Belum ada' : $daysSinceBackup . ' hari lalu' ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts -->
    <div class="row g-3 mb-4">
      <div class="col-md-12">
        <div class="card chart-card">
          <div class="card-body">
            <h6 class="fw-bold mb-3"><i class="bi bi-graph-up"></i> Aktivitas 30 Hari Terakhir</h6>
            <canvas id="chartDaily" height="60"></canvas>
          </div>
        </div>
      </div>
      <div class="col-md-7">
        <div class="card chart-card">
          <div class="card-body">
            <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart"></i> Top 10 User Paling Aktif</h6>
            <canvas id="chartTop" height="140"></canvas>
          </div>
        </div>
      </div>
      <div class="col-md-5">
        <div class="card chart-card">
          <div class="card-body">
            <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart"></i> Distribusi Action</h6>
            <canvas id="chartAction" height="200"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Login Gagal Terbaru -->
    <?php if (!empty($failedLogins)): ?>
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
          <h6 class="fw-bold mb-3"><i class="bi bi-shield-exclamation text-danger"></i> 10 Login Gagal Terbaru</h6>
          <div class="table-responsive">
            <table class="table table-sm mb-0">
              <thead class="table-light">
                <tr>
                  <th>Waktu</th>
                  <th>Email</th>
                  <th>IP</th>
                  <th>User Agent</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($failedLogins as $fl): ?>
                  <tr>
                    <td style="font-size:12px;"><?= date('d M H:i:s', strtotime($fl['created_at'])) ?></td>
                    <td style="font-size:12px;"><?= esc(explode('|', $fl['description'])[0] ?? '-') ?></td>
                    <td><code style="font-size:11px;"><?= esc($fl['ip_address']) ?></code></td>
                    <td style="font-size:11px; color:#6b7280; max-width:250px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= esc($fl['user_agent']) ?></td>
                    <td>
                      <form method="post" action="<?= base_url('admin/blocked-ip/block') ?>" class="d-inline"
                        onsubmit="return confirm('Blokir IP ini?')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="ip_address" value="<?= esc($fl['ip_address']) ?>">
                        <input type="hidden" name="reason" value="Diblokir dari widget login gagal">
                        <input type="hidden" name="expire_hours" value="24">
                        <button class="btn btn-sm btn-outline-danger">Blokir 24 Jam</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <a href="<?= base_url('admin/blocked-ip') ?>" class="btn btn-sm btn-link mt-2">Kelola semua IP →</a>
        </div>
      </div>
    <?php endif; ?>

    <!-- Log Table -->
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
          <h6 class="fw-bold mb-0"><i class="bi bi-list-ul"></i> Log Aktivitas</h6>
          <div class="d-flex gap-2">
            <form method="post" action="<?= base_url('admin/log-action/backup') ?>" class="m-0">
              <?= csrf_field() ?>
              <button class="btn btn-sm btn-outline-success"><i class="bi bi-download"></i> Backup CSV</button>
            </form>
            <a href="<?= base_url('admin/log-action/export?' . http_build_query($filters)) ?>" class="btn btn-sm btn-outline-primary">
              <i class="bi bi-file-earmark-arrow-down"></i> Export CSV
            </a>
            <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#archiveModal">
              <i class="bi bi-archive"></i> Arsipkan Sekarang
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cleanupModal">
              <i class="bi bi-trash"></i> Bersihkan Manual
            </button>
          </div>
        </div>

        <!-- Filter -->
        <form method="get" class="row g-2 mb-3">
          <div class="col-md-2"><input type="text" name="q" class="form-control form-control-sm" placeholder="Cari deskripsi" value="<?= esc($filters['q']) ?>"></div>
          <div class="col-md-2">
            <select name="action" class="form-select form-select-sm">
              <option value="">Semua Action</option>
              <?php foreach (['login_success', 'login_failed', 'logout', 'register_success', 'submit_pengajuan', 'verify_approve', 'verify_reject', 'verify_feedback', 'export_excel', 'export_pdf', 'ip_auto_blocked'] as $a): ?>
                <option value="<?= $a ?>" <?= $filters['action'] === $a ? 'selected' : '' ?>><?= $a ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <select name="module" class="form-select form-select-sm">
              <option value="">Semua Module</option>
              <?php foreach (['auth', 'pengajuan', 'verifikasi', 'user', 'admin', 'security', 'cabang'] as $m): ?>
                <option value="<?= $m ?>" <?= $filters['module'] === $m ? 'selected' : '' ?>><?= $m ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2"><input type="date" name="dari" class="form-control form-control-sm" value="<?= esc($filters['dari']) ?>"></div>
          <div class="col-md-2"><input type="date" name="sampai" class="form-control form-control-sm" value="<?= esc($filters['sampai']) ?>"></div>
          <div class="col-md-2"><button class="btn btn-primary btn-sm w-100">Filter</button></div>
        </form>

        <div class="table-responsive">
          <table class="table table-sm table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th width="130">Waktu</th>
                <th width="90">Role</th>
                <th width="140">Action</th>
                <th width="90">Module</th>
                <th>Deskripsi</th>
                <th width="120">IP</th>
                <th width="60">Detail</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($logs)): ?>
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">Tidak ada log.</td>
                </tr>
                <?php else: foreach ($logs as $l): ?>
                  <tr class="log-row" onclick="showLogDetail(<?= $l['id'] ?>)">
                    <td style="font-size:12px;"><?= date('d M H:i:s', strtotime($l['created_at'])) ?></td>
                    <td><span class="badge bg-secondary" style="font-size:10px;"><?= esc($l['user_role'] ?? 'guest') ?></span></td>
                    <td>
                      <?php
                      $color = 'secondary';
                      if (strpos($l['action'], 'login_failed') !== false) $color = 'danger';
                      elseif (strpos($l['action'], 'login_success') !== false) $color = 'success';
                      elseif (strpos($l['action'], 'block') !== false) $color = 'warning';
                      elseif (strpos($l['action'], 'export') !== false) $color = 'info';
                      ?>
                      <span class="badge bg-<?= $color ?> action-badge"><?= esc($l['action']) ?></span>
                    </td>
                    <td style="font-size:12px;"><?= esc($l['module']) ?></td>
                    <td style="font-size:12px; max-width:400px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= esc($l['description']) ?></td>
                    <td><code style="font-size:11px;"><?= esc($l['ip_address']) ?></code></td>
                    <td><button class="btn btn-sm btn-outline-secondary" onclick="event.stopPropagation();showLogDetail(<?= $l['id'] ?>)"><i class="bi bi-eye"></i></button></td>
                  </tr>
              <?php endforeach;
              endif; ?>
            </tbody>
          </table>
        </div>
        <?= $pager->links() ?>
      </div>
    </div>

    <!-- Modal Detail Log -->
    <div class="modal fade" id="logDetailModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Detail Log</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body" id="logDetailContent">Loading...</div>
        </div>
      </div>
    </div>

    <!-- Modal Arsip -->
    <div class="modal fade" id="archiveModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <form method="post" action="<?= base_url('admin/log-action/archive-now') ?>">
            <?= csrf_field() ?>
            <div class="modal-header">
              <h5 class="modal-title">Arsipkan Log</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <p>Log lebih tua dari <strong>90 hari</strong> akan dipindahkan ke tabel arsip.</p>
              <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle"></i> Saat ini ada <strong><?= number_format($stats['will_archive']) ?></strong> log yang siap diarsipkan.
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-warning">Arsipkan Sekarang</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Cleanup -->
    <div class="modal fade" id="cleanupModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <form method="post" action="<?= base_url('admin/log-action/cleanup') ?>">
            <?= csrf_field() ?>
            <div class="modal-header">
              <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle"></i> Bersihkan Log Manual</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="alert alert-danger">
                <strong>Perhatian!</strong> Aksi ini akan <strong>menghapus permanen</strong> log dari database. Tidak dapat dibatalkan.
              </div>
              <div class="mb-3">
                <label class="form-label">Hapus log lebih tua dari (hari):</label>
                <input type="number" name="days" class="form-control" min="30" value="180" required>
                <small class="text-muted">Minimal 30 hari.</small>
              </div>
              <div class="mb-3">
                <label class="form-label">Alasan (wajib, min 10 karakter):</label>
                <textarea name="alasan" class="form-control" rows="3" required minlength="10" placeholder="Contoh: Backup sudah dilakukan ke arsip eksternal, log lama dihapus untuk menghemat storage."></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Ketik <code>HAPUS</code> untuk konfirmasi:</label>
                <input type="text" name="konfirmasi" class="form-control" required pattern="HAPUS">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-danger">Hapus Permanen</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  // Tunggu DOM siap sebelum init chart
  document.addEventListener('DOMContentLoaded', function() {

    // Cek apakah canvas ada
    const elDaily = document.getElementById('chartDaily');
    const elTop = document.getElementById('chartTop');
    const elAction = document.getElementById('chartAction');

    if (!elDaily || !elTop || !elAction) {
      console.warn('Chart canvas tidak ditemukan');
      return;
    }

    // Cek apakah Chart.js ter-load
    if (typeof Chart === 'undefined') {
      console.error('Chart.js tidak ter-load!');
      return;
    }

    // Chart 1: Daily
    new Chart(elDaily, {
      type: 'line',
      data: {
        labels: <?= json_encode($chartDaily['labels']) ?>,
        datasets: [{
          label: 'Aktivitas',
          data: <?= json_encode($chartDaily['values']) ?>,
          borderColor: '#0a1f3c',
          backgroundColor: 'rgba(10,31,60,0.08)',
          fill: true,
          tension: 0.35,
          pointRadius: 3,
          pointHoverRadius: 5
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              precision: 0
            }
          }
        }
      }
    });

    // Chart 2: Top Users
    new Chart(elTop, {
      type: 'bar',
      data: {
        labels: <?= json_encode(array_map(fn($u) => $u['nama_lengkap'] ?? '(unknown)', $chartTop)) ?>,
        datasets: [{
          label: 'Jumlah Aktivitas',
          data: <?= json_encode(array_map(fn($u) => (int) $u['total'], $chartTop)) ?>,
          backgroundColor: '#1862b5'
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          x: {
            beginAtZero: true,
            ticks: {
              precision: 0
            }
          }
        }
      }
    });

    // Chart 3: Action Distribution
    new Chart(elAction, {
      type: 'doughnut',
      data: {
        labels: <?= json_encode(array_map(fn($a) => $a['action'], $chartAction)) ?>,
        datasets: [{
          data: <?= json_encode(array_map(fn($a) => (int) $a['total'], $chartAction)) ?>,
          backgroundColor: [
            '#0a1f3c', '#1862b5', '#3b82f6', '#60a5fa', '#f59e0b',
            '#10b981', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6',
            '#f97316', '#a855f7', '#06b6d4', '#84cc16', '#eab308'
          ]
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'right',
            labels: {
              font: {
                size: 10
              },
              boxWidth: 12
            }
          }
        }
      }
    });

    // Log sukses untuk debug
    console.log('✅ Charts rendered:', {
      daily: elDaily,
      top: elTop,
      action: elAction
    });
  });
</script>
<?= $this->endSection() ?>