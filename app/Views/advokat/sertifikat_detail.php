<?= $this->extend('layouts/advokat') ?>

<?= $this->section('styles') ?>
<style>
  .sertifikat-card {
    background: linear-gradient(135deg, #0a1f3c 0%, #1862b5 100%);
    color: #fff;
    border-radius: 14px;
    padding: 24px;
    margin-bottom: 20px;
  }

  .sertifikat-card .big-number {
    font-size: 48px;
    font-weight: 800;
    line-height: 1;
  }

  .sertifikat-card .label {
    font-size: 14px;
    opacity: 0.9;
  }

  .perkara-row:hover {
    background: #f9fafb;
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
    <h1>Detail Sertifikat</h1>
    <small class="text-muted">Rincian perolehan jam dari perkara yang telah disetujui</small>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="<?= base_url('advokat/dashboard') ?>">Dashboard</a></li>
        <li class="current">Detail Sertifikat — Tahun <?= esc($tahun) ?></li>
      </ol>
    </nav>
  </div>
</div>

<section class="contact section light-background">
  <div class="container" data-aos="fade-up">
    <!-- Kartu Ringkasan -->
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="sertifikat-card h-100">
          <div class="label mb-2">Total Jam Terkumpul</div>
          <div class="big-number"><?= esc($totalJam) ?></div>
          <div class="label mt-2">jam</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="sertifikat-card h-100" style="background: linear-gradient(135deg, #065f46 0%, #10b981 100%);">
          <div class="label mb-2">Sertifikat Terbit</div>
          <div class="big-number"><?= esc($sertifikatTerbit) ?></div>
          <div class="label mt-2">dari <?= esc($targetJam) ?> jam / sertifikat</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="sertifikat-card h-100" style="background: linear-gradient(135deg, #92400e 0%, #f59e0b 100%);">
          <div class="label mb-2">Sisa Menuju Sertifikat Berikutnya</div>
          <div class="big-number"><?= esc($sisaJam) ?></div>
          <div class="label mt-2">jam lagi</div>
        </div>
      </div>
    </div>

    <!-- Tabel Perkara -->
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <h6 class="fw-bold mb-3">
          <i class="bi bi-list-ul"></i>
          Daftar Perkara Disetujui (<?= count($perkara) ?>)
        </h6>

        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th width="5%">#</th>
                <th>No. Registrasi</th>
                <th>Jenis Layanan</th>
                <th>Jenis Perkara</th>
                <th>Tanggal Disetujui</th>
                <th class="text-end">Durasi (Jam)</th>
                <th width="10%">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($perkara)): ?>
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">
                    Belum ada perkara yang disetujui di tahun ini.
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($perkara as $i => $p): ?>
                  <tr class="perkara-row">
                    <td><?= $i + 1 ?></td>
                    <td><code style="font-size:12px;"><?= esc($p['no_registrasi']) ?></code></td>
                    <td>
                      <span class="badge bg-secondary">
                        <?= esc(ucfirst($p['jenis_layanan'])) ?>
                      </span>
                    </td>
                    <td style="font-size:13px;">
                      <?= esc($p['jenis_non_litigasi'] ?? $p['jenis_layanan']) ?>
                    </td>
                    <td style="font-size:13px;">
                      <?= $p['verified_at'] ? date('d M Y', strtotime($p['verified_at'])) : '-' ?>
                    </td>
                    <td class="text-end">
                      <span class="badge bg-primary">
                        <?= esc($p['durasi_jam'] ?? 0) ?> jam
                      </span>
                    </td>
                    <td>
                      <a href="<?= base_url('advokat/pengajuan/' . $p['id']) ?>"
                        class="btn btn-sm btn-outline-primary">
                        Lihat
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
            <?php if (!empty($perkara)): ?>
              <tfoot class="table-light">
                <tr>
                  <th colspan="5" class="text-end">TOTAL</th>
                  <th class="text-end">
                    <span class="badge bg-success" style="font-size:14px; padding: 6px 12px;">
                      <?= esc($totalJam) ?> jam
                    </span>
                  </th>
                  <th></th>
                </tr>
              </tfoot>
            <?php endif; ?>
          </table>
        </div>
      </div>
    </div>

    <!-- Info Box -->
    <div class="alert alert-info mt-3">
      <i class="bi bi-info-circle"></i>
      <strong>Aturan Sertifikat:</strong>
      Setiap akumulasi <strong>50 jam</strong> penanganan perkara yang telah disetujui verifikator
      menghasilkan <strong>1 sertifikat pro bono</strong> resmi dari DPN PERADI.
    </div>




  </div>
</section>







<?= $this->endSection() ?>