<?= $this->extend('layouts/cabang') ?>

<?= $this->section('styles') ?>
<style>
  /* --- Kartu Statistik --- */
  .stat-card {
    border-radius: 12px;
    transition: all 0.3s ease;
    background-color: #ffffff;
    border-left: 4px solid transparent;
  }

  /* Efek Hover */
  .stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
  }

  /* Warna Aksen Kiri Otomatis */
  .stat-card:has(.text-primary) {
    border-left-color: #0d6efd;
  }

  .stat-card:has(.text-info) {
    border-left-color: #0dcaf0;
  }

  .stat-card:has(.text-warning) {
    border-left-color: #ffc107;
  }

  .stat-card:has(.text-success) {
    border-left-color: #198754;
  }

  .stat-card:has(.text-danger) {
    border-left-color: #dc3545;
  }

  /* --- Ikon --- */
  .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
  }

  /* --- Label --- */
  .stat-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
    margin-bottom: 2px;
  }

  /* --- Nilai Angka --- */
  .stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    line-height: 1.2;
    color: #212529;
  }

  /* --- Responsif --- */
  @media (max-width: 576px) {
    .stat-value {
      font-size: 1.5rem;
    }

    .stat-icon {
      width: 40px;
      height: 40px;
      font-size: 18px;
    }
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

<section id="hero" class="hero section mt-5">
  <div class="container" data-aos="fade-up" data-aos-delay="100">

    <!-- Header Selamat Datang -->
    <div class="row align-items-center mb-5">
      <div class="col-lg-8">
        <div class="hero-content">
          <h1 class="mb-2">Selamat Datang,</h1>
          <h3 class="text-secondary mb-4">Rekan Pengurus PBH Cabang PERADI — <?= esc($cabang['nama']) ?></h3>
        </div>
      </div>
    </div>



    <!-- ============================================ -->
    <!-- STATISTIK CARDS — 5 KARTU                    -->
    <!-- ============================================ -->
    <div class="row g-3 mb-4">
      <!-- Alert Warning (kalau ada) -->

      <!-- Advokat Aktif -->
      <div class="col-6 col-md-3">
        <div class="card stat-card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-start gap-3">
            <div class="stat-icon bg-primary bg-opacity-10 text-primary">
              <i class="bi bi-people-fill"></i>
            </div>
            <div class="flex-grow-1">
              <div class="stat-label">Advokat Aktif</div>
              <div class="stat-value text-primary"><?= $stats['total_advokat'] ?></div>
              <small class="text-muted" style="font-size: 11px;">
                dari <?= $stats['total_advokat_all'] ?> terdaftar
              </small>
            </div>
          </div>
        </div>
      </div>

      <!-- Total Pengajuan -->
      <div class="col-6 col-md-3">
        <div class="card stat-card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-start gap-3">
            <div class="stat-icon bg-info bg-opacity-10 text-info">
              <i class="bi bi-file-text"></i>
            </div>
            <div class="flex-grow-1">
              <div class="stat-label">Total Pengajuan</div>
              <div class="stat-value text-info"><?= $stats['total_pengajuan'] ?></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Disetujui -->
      <div class="col-6 col-md-2">
        <div class="card stat-card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-start gap-3">
            <div class="stat-icon bg-success bg-opacity-10 text-success">
              <i class="bi bi-check-circle"></i>
            </div>
            <div class="flex-grow-1">
              <div class="stat-label">Disetujui</div>
              <div class="stat-value text-success"><?= $stats['approved'] ?></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Proses -->
      <div class="col-6 col-md-2">
        <div class="card stat-card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-start gap-3">
            <div class="stat-icon bg-warning bg-opacity-10 text-warning">
              <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="flex-grow-1">
              <div class="stat-label">Proses</div>
              <div class="stat-value text-warning"><?= $stats['review'] ?></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Ditolak -->
      <div class="col-6 col-md-2">
        <div class="card stat-card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-start gap-3">
            <div class="stat-icon bg-danger bg-opacity-10 text-danger">
              <i class="bi bi-x-circle"></i>
            </div>
            <div class="flex-grow-1">
              <div class="stat-label">Ditolak</div>
              <div class="stat-value text-danger"><?= $stats['rejected'] ?></div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <?php if ($stats['warning'] > 0): ?>
      <div class="alert alert-warning mb-4">
        <i class="bi bi-exclamation-triangle"></i>
        <strong><?= $stats['warning'] ?> pengajuan</strong> memiliki catatan warning dari verifikator.
      </div>
    <?php endif; ?>

    <!-- ============================================ -->
    <!-- CHART — 2 KARTU                              -->
    <!-- ============================================ -->
    <div class="row g-3 mb-4">
      <div class="col-md-7">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h6 class="fw-bold mb-3">
              <i class="bi bi-graph-up text-primary me-2"></i>
              Grafik Pertumbuhan Pro Bono (12 Bulan)
            </h6>
            <div style="position: relative; height: 300px; width: 100%;">
              <canvas id="chartMonthly"></canvas>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-5">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h6 class="fw-bold mb-3">
              <i class="bi bi-pie-chart text-warning me-2"></i>
              Distribusi Jenis Layanan
            </h6>
            <div style="position: relative; height: 350px; width: 100%;">
              <canvas id="chartJenis"></canvas>
            </div>
            <div class="mt-3 text-center">
              <small class="text-muted">
                <i class="bi bi-info-circle me-1"></i>
                Total Pengajuan: <strong id="totalJenis">0</strong>
              </small>
            </div>
          </div>
        </div>

      </div>

      <div class="col-md-12 mt-3">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h6 class="fw-bold mb-3">
              <i class="bi bi-bar-chart text-primary me-2"></i>
              Perbandingan Jumlah per Jenis Perkara
            </h6>
            <div style="position: relative; height: 300px;">
              <canvas id="chartJenisBar"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ============================================ -->
    <!-- TOP ADVOKAT & PENGAJUAN TERBARU              -->
    <!-- ============================================ -->
    <div class="row g-3 mb-4">

      <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h6 class="fw-bold mb-3">Top 10 Advokat Cabang</h6>
            <div class="table-responsive">
              <table class="table table-sm mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Advokat</th>
                    <th>NIA</th>
                    <th>Total</th>
                    <th>Disetujui</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($topAdvokat)): ?>
                    <tr>
                      <td colspan="4" class="text-center text-muted py-3">Belum ada data.</td>
                    </tr>
                    <?php else: foreach ($topAdvokat as $a): ?>
                      <tr>
                        <td style="font-size:13px;"><?= esc($a['nama_lengkap']) ?></td>
                        <td style="font-size:12px;"><?= esc($a['nia']) ?></td>
                        <td><span class="badge bg-primary"><?= $a['total'] ?></span></td>
                        <td><span class="badge bg-success"><?= $a['approved'] ?></span></td>
                      </tr>
                  <?php endforeach;
                  endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h6 class="fw-bold mb-3">Pengajuan Terbaru</h6>
            <div class="table-responsive">
              <table class="table table-sm mb-0">
                <thead class="table-light">
                  <tr>
                    <th>No. Reg</th>
                    <th>Advokat</th>
                    <th>Status</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($pengajuan)): ?>
                    <tr>
                      <td colspan="4" class="text-center text-muted py-3">Belum ada pengajuan.</td>
                    </tr>
                    <?php else: foreach ($pengajuan as $p): ?>
                      <?php $badge = match ($p['status']) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'review', 'submitted' => 'warning',
                        default => 'secondary'
                      }; ?>
                      <tr>
                        <td><code style="font-size:11px;"><?= esc($p['no_registrasi']) ?></code></td>
                        <td style="font-size:12px;"><?= esc($p['advokat_nama']) ?></td>
                        <td><span class="badge bg-<?= $badge ?>"><?= ucfirst($p['status']) ?></span></td>
                        <td><a href="<?= base_url('cabang/pengajuan/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary">Lihat</a></td>
                      </tr>
                  <?php endforeach;
                  endif; ?>
                </tbody>
              </table>
            </div>
            <a href="<?= base_url('cabang/pengajuan') ?>" class="btn btn-sm btn-link mt-2">Lihat semua →</a>
          </div>
        </div>
      </div>

    </div>

  </div>
</section>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<?= $this->section('scripts') ?>
<script>
  (function() {
    window.addEventListener('load', function() {
      console.log('🔍 Init chart cabang...');

      // ============================================================
      // CEK DEPENDENSI
      // ============================================================
      if (typeof Chart === 'undefined') {
        console.error('❌ Chart.js tidak ter-load!');
        return;
      }
      console.log('✅ Chart.js ter-load, versi:', Chart.version);

      const elMonthly = document.getElementById('chartMonthly');
      const elJenis = document.getElementById('chartJenis');
      const elJenisBar = document.getElementById('chartJenisBar');

      if (!elMonthly) {
        console.error('❌ #chartMonthly tidak ada');
        return;
      }
      if (!elJenis) {
        console.error('❌ #chartJenis tidak ada');
        return;
      }
      if (!elJenisBar) {
        console.error('❌ #chartJenisBar tidak ada');
        return;
      }
      console.log('✅ Semua canvas ditemukan');

      // ============================================================
      // AMBIL DATA DARI PHP
      // ============================================================
      const dataMonthlyLabels = <?= json_encode($chartMonthly['labels'] ?? []) ?>;
      const dataMonthlyValues = <?= json_encode($chartMonthly['values'] ?? []) ?>;
      const dataJenisRaw = <?= json_encode($chartJenis ?? []) ?>;

      console.log('📊 Data:', {
        monthly: dataMonthlyValues.length + ' titik',
        jenis: dataJenisRaw.length + ' jenis'
      });

      // ============================================================
      // WARNA: Litigasi (biru) vs Non-Litigasi (hijau)
      // ============================================================
      const warnaByKategori = {
        'litigasi': ['#0a1f3c', '#1862b5', '#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe', '#dbeafe'],
        'non-litigasi': ['#065f46', '#10b981', '#34d399', '#6ee7b7']
      };
      let colorIndex = {
        'litigasi': 0,
        'non-litigasi': 0
      };
      const jenisColors = dataJenisRaw.map(j => {
        const idx = colorIndex[j.kategori] ?? 0;
        const color = warnaByKategori[j.kategori]?.[idx] ?? '#6b7280';
        colorIndex[j.kategori] = idx + 1;
        return color;
      });

      // ============================================================
      // CHART 1: LINE — Grafik Pertumbuhan Bulanan
      // ============================================================
      try {
        new Chart(elMonthly, {
          type: 'line',
          data: {
            labels: dataMonthlyLabels,
            datasets: [{
              label: 'Pengajuan',
              data: dataMonthlyValues,
              borderColor: '#0a1f3c',
              backgroundColor: 'rgba(10,31,60,0.1)',
              fill: true,
              tension: 0.3,
              pointRadius: 4,
              pointHoverRadius: 6
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                display: false
              },
              tooltip: {
                padding: 10
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
        console.log('✅ Chart Monthly rendered');
      } catch (e) {
        console.error('❌ Error Chart Monthly:', e);
      }

      // ============================================================
      // CHART 2: PIE — Distribusi Jenis Layanan (Berjenjang)
      // ============================================================
      try {
        if (dataJenisRaw.length === 0) {
          // Tidak ada data → tampil pesan
          elJenis.parentElement.innerHTML =
            '<div class="text-center text-muted py-5">' +
            '<i class="bi bi-inbox" style="font-size:48px;"></i>' +
            '<p class="mt-2 mb-0">Belum ada pengajuan</p>' +
            '</div>';
        } else {
          new Chart(elJenis, {
            type: 'pie',
            data: {
              labels: dataJenisRaw.map(j => j.label),
              datasets: [{
                data: dataJenisRaw.map(j => j.total),
                backgroundColor: jenisColors,
                borderColor: '#fff',
                borderWidth: 2,
                hoverOffset: 8
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  position: 'right',
                  labels: {
                    font: {
                      size: 11
                    },
                    boxWidth: 14,
                    padding: 8,
                    generateLabels: function(chart) {
                      const data = chart.data;
                      const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                      return data.labels.map((label, i) => {
                        const value = data.datasets[0].data[i];
                        const pct = total > 0 ? ((value / total) * 100).toFixed(0) : 0;
                        return {
                          text: label + ' (' + value + ' • ' + pct + '%)',
                          fillStyle: data.datasets[0].backgroundColor[i],
                          strokeStyle: data.datasets[0].backgroundColor[i],
                          lineWidth: 1,
                          index: i
                        };
                      });
                    }
                  }
                },
                tooltip: {
                  callbacks: {
                    label: function(ctx) {
                      const label = ctx.label || '';
                      const value = ctx.parsed || 0;
                      const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                      const pct = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                      return ' ' + label + ': ' + value + ' (' + pct + '%)';
                    }
                  }
                }
              }
            }
          });
          console.log('✅ Chart Jenis (Pie) rendered');

          // Set total di bawah chart
          const totalEl = document.getElementById('totalJenis');
          if (totalEl) {
            totalEl.textContent = dataJenisRaw.reduce((a, b) => a + b.total, 0);
          }
        }
      } catch (e) {
        console.error('❌ Error Chart Jenis:', e);
      }

      // ============================================================
      // CHART 3: BAR — Perbandingan Jumlah per Jenis Perkara
      // ============================================================
      try {
        if (dataJenisRaw.length === 0) {
          elJenisBar.parentElement.innerHTML =
            '<div class="text-center text-muted py-5">' +
            '<i class="bi bi-inbox" style="font-size:48px;"></i>' +
            '<p class="mt-2 mb-0">Belum ada data untuk ditampilkan</p>' +
            '</div>';
        } else {
          new Chart(elJenisBar, {
            type: 'bar',
            data: {
              labels: dataJenisRaw.map(j => j.label),
              datasets: [{
                label: 'Jumlah Pengajuan',
                data: dataJenisRaw.map(j => j.total),
                backgroundColor: jenisColors,
                borderColor: jenisColors.map(c => c),
                borderWidth: 1,
                borderRadius: 4
              }]
            },
            options: {
              indexAxis: 'y', // horizontal bar
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  display: false
                },
                tooltip: {
                  callbacks: {
                    label: function(ctx) {
                      return ' ' + ctx.parsed.x + ' pengajuan';
                    }
                  }
                }
              },
              scales: {
                x: {
                  beginAtZero: true,
                  ticks: {
                    precision: 0
                  },
                  grid: {
                    display: true,
                    color: '#f3f4f6'
                  }
                },
                y: {
                  grid: {
                    display: false
                  },
                  ticks: {
                    font: {
                      size: 11
                    }
                  }
                }
              }
            }
          });
          console.log('✅ Chart Jenis (Bar) rendered');
        }
      } catch (e) {
        console.error('❌ Error Chart Jenis Bar:', e);
      }

      console.log('🎉 Semua chart cabang berhasil dirender!');
    });
  })();
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>