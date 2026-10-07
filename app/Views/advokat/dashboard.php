<?= $this->extend('layouts/advokat') ?>
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
    <div class="row align-items-center mb-5">
      <div class="col-lg-8">
        <div class="hero-content" data-aos="fade-up" data-aos-delay="200">
          <h1 class="mb-2">Selamat Datang,</h1>
          <h3 class="text-secondary mb-4">Rekan Anggota PERADI</h3>
          <p class="text-muted">
            Melalui panel ini, Anda dapat melaporkan kegiatan pemberian
            bantuan hukum secara cuma-cuma (ProBono) Setiap telah memenuhi
            50 jam akan diberikan sertifikat melalui akun dan email anda.
          </p>
          <div class="hero-buttons">
            <a
              href="<?= base_url('advokat/pengajuan/create') ?>"
              class="btn btn-primary me-0 me-sm-2 mx-1">
              <i class="bi bi-plus-circle me-1"></i> Submit Laporan
            </a>
          </div>
        </div>
      </div>
    </div>



    <div class="row mt-4" data-aos="fade-up" data-aos-delay="400">
      <div class="col-12">
        <!-- ============================================================ -->
        <!-- REKAPITULASI SERTIFIKAT PRO BONO                             -->
        <!-- ============================================================ -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">

            <!-- Header -->
            <div class="d-flex align-items-center mb-2">
              <i class="bi bi-award-fill text-primary me-2" style="font-size: 22px;"></i>
              <h5 class="fw-bold mb-0">Rekapitulasi Perolehan Sertifikat Pro Bono</h5>
            </div>
            <p class="text-muted mb-4" style="font-size: 13px;">
              *Setiap akumulasi <strong>50 jam</strong> penanganan perkara menghasilkan <strong>1 sertifikat</strong> resmi.
            </p>

            <?php if (empty($rekapSertifikat)): ?>
              <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle"></i>
                Belum ada perkara yang disetujui. Sertifikat akan muncul setelah pengajuan Anda di-approve admin.
              </div>
            <?php else: ?>
              <div class="row g-3">

                <?php foreach ($rekapSertifikat as $r): ?>
                  <?php
                  // Warna angka besar berdasarkan status
                  $numberColor = match ($r['status']) {
                    'terbit'      => '#10b981', // hijau
                    'siap_dibuat' => '#f59e0b', // kuning
                    'kosong'      => '#9ca3af', // abu-abu
                    default       => '#0a1f3c', // biru gelap
                  };
                  ?>

                  <div class="col-md-4">
                    <div class="card h-100 border-0" style="background: #f9fafb; border-radius: 14px;">
                      <div class="card-body p-4">

                        <!-- Baris 1: Badge Tahun + Total Jam -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                          <span class="badge rounded-pill"
                            style="background: #dbeafe; color: #1e40af; font-size: 13px; font-weight: 600; padding: 6px 14px;">
                            Tahun <?= esc($r['tahun']) ?>
                          </span>
                          <span style="font-size: 14px; color: #374151; font-weight: 500;">
                            Total: <strong><?= esc($r['total_jam']) ?> Jam</strong>
                          </span>
                        </div>

                        <!-- Baris 2: Angka Sertifikat + Label -->
                        <div class="d-flex align-items-center mb-3" style="gap: 14px;">
                          <span style="font-size: 40px; font-weight: 800; line-height: 1; color: <?= $numberColor ?>;">
                            <?= esc($r['sertifikat_terbit']) ?>
                          </span>
                          <span style="font-size: 15px; color: #374151; font-weight: 500;">
                            <?= esc($r['label']) ?>
                          </span>
                        </div>

                        <!-- Baris 3: Progress Bar -->
                        <div class="progress mb-2" style="height: 8px; border-radius: 6px; background: #e5e7eb;">
                          <div class="progress-bar bg-<?= esc($r['bar_color']) ?>"
                            role="progressbar"
                            style="width: <?= esc($r['progress_persen']) ?>%; border-radius: 6px; transition: width 0.6s ease;">
                          </div>
                        </div>

                        <!-- Baris 4: Teks Sisa + Persen -->
                        <div class="d-flex justify-content-between mb-3" style="font-size: 12px; color: #6b7280;">
                          <span><?= esc($r['text_sisa']) ?></span>
                          <span class="fw-bold"><?= esc($r['progress_persen']) ?>%</span>
                        </div>

                        <!-- Baris 5: Tombol Detail -->
                        <a href="<?= base_url('advokat/sertifikat/' . $r['tahun']) ?>"
                          class="btn btn-primary w-100"
                          style="border-radius: 8px; font-size: 14px; font-weight: 600; padding: 10px 0;">
                          <i class="bi bi-award me-1"></i> Detail Sertifikat
                        </a>

                      </div>
                    </div>
                  </div>

                <?php endforeach; ?>

              </div>
            <?php endif; ?>

          </div>
        </div>
      </div>
    </div>


    <div class="row mt-4" data-aos="fade-up" data-aos-delay="400">
      <div class="col-12">
        <div class="card shadow-sm border-0 rounded-3">
          <div
            class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
            <h5 class="mb-0 fw-bold text-dark">
              <i class="bi bi-file-earmark-text me-2 text-primary"></i>Riwayat Bantuan Hukum Tahun 2025
            </h5>
          </div>
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h6 class="fw-bold mb-0">Riwayat Bantuan Hukum</h6>
                <form method="get" class="d-flex gap-2 flex-wrap">
                  <select name="tahun" class="form-select form-select-sm" style="width:auto;">
                    <option value="">Semua Tahun</option>
                    <?php foreach ($tahunList as $t): ?>
                      <option value="<?= $t['tahun'] ?>" <?= $filters['tahun'] == $t['tahun'] ? 'selected' : '' ?>><?= $t['tahun'] ?></option>
                    <?php endforeach; ?>
                  </select>
                  <select name="status" class="form-select form-select-sm" style="width:auto;">
                    <option value="">Semua Status</option>
                    <?php foreach (['draft', 'submitted', 'review', 'approved', 'rejected'] as $s): ?>
                      <option value="<?= $s ?>" <?= $filters['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <button class="btn btn-primary btn-sm">Filter</button>
                </form>
              </div>

              <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                  <thead class="table-light">
                    <tr>
                      <th>No. Registrasi</th>
                      <th>Tahun</th>
                      <th>Klien</th>
                      <th>Jenis</th>
                      <th>Status</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($pengajuan)): ?>
                      <tr>
                        <td colspan="6" class="text-center text-muted py-4">Belum ada pengajuan.</td>
                      </tr>
                      <?php else: foreach ($pengajuan as $p): ?>
                        <tr>
                          <td><code><?= esc($p['no_registrasi']) ?></code></td>
                          <td><?= esc($p['tahun']) ?></td>
                          <td><?= esc($p['jenis_layanan'] === 'litigasi' ? 'Litigasi' : ucfirst($p['jenis_non_litigasi'] ?? '-')) ?></td>
                          <td><?= esc($p['jenis_layanan']) ?></td>
                          <td>
                            <?php
                            $badge = match ($p['status']) {
                              'approved' => 'success',
                              'rejected' => 'danger',
                              'review', 'submitted' => 'warning',
                              default => 'secondary'
                            };
                            ?>
                            <span class="badge bg-<?= $badge ?>"><?= ucfirst($p['status']) ?></span>
                          </td>
                          <td>
                            <?php if ($p['status'] === 'draft'): ?>
                              <a href="<?= base_url('advokat/pengajuan/' . $p['id'] . '/edit') ?>" class="btn btn-sm btn-warning">Lanjutkan</a>

                              <button type="button" class="btn btn-sm btn-outline-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteModal"
                                data-id="<?= $p['id'] ?>"
                                data-no="<?= esc($p['no_registrasi']) ?>">
                                Hapus
                              </button>
                            <?php else: ?>
                              <a href="<?= base_url('advokat/pengajuan/' . $p['id']) ?>" class="btn btn-sm btn-primary">Lihat</a>
                            <?php endif; ?>
                          </td>
                        </tr>
                    <?php endforeach;
                    endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


    <style>
      /* Styling Kartu Rekapitulasi */
      .metric-card {
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        transition:
          transform 0.2s,
          box-shadow 0.2s;
      }

      .metric-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05) !important;
      }

      .year-badge {
        font-size: 0.85rem;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 600;
        background-color: rgba(7, 74, 230, 0.1);
        color: #074ae6;
      }

      .cert-count {
        font-size: 1.8rem;
        font-weight: 700;
        color: #198754;
      }

      /* Progress Bar Custom */
      .progress-sm {
        height: 6px;
      }

      /* Merapikan Tabel Pro Bono */
      .table th {
        font-weight: 600;
        font-size: 0.88rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
      }

      .table tbody tr {
        transition: background-color 0.2s;
      }

      .table-responsive {
        border-radius: 0 0 8px 8px;
      }

      /* Menyeragamkan Tinggi & Desain Tombol Aksi */
      .action-btns .btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-weight: 500;
      }
    </style>
  </div>
</section>










<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" id="deleteForm">
        <?= csrf_field() ?>
        <div class="modal-header">
          <h5 class="modal-title text-danger">Hapus Draft</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p>Yakin hapus draft <strong id="deleteNoReg"></strong>?</p>
          <p class="text-muted" style="font-size:13px;">Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Ya, Hapus</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.getElementById('deleteModal').addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    document.getElementById('deleteForm').action = '<?= base_url('advokat/pengajuan') ?>/' + btn.dataset.id + '/delete';
    document.getElementById('deleteNoReg').textContent = btn.dataset.no;
  });
</script>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>


<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" id="deleteForm">
        <?= csrf_field() ?>
        <div class="modal-header">
          <h5 class="modal-title text-danger">Hapus Draft</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p>Yakin hapus draft <strong id="deleteNoReg"></strong>?</p>
          <p class="text-muted" style="font-size:13px;">Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Ya, Hapus</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.getElementById('deleteModal').addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    document.getElementById('deleteForm').action = '<?= base_url('advokat/pengajuan') ?>/' + btn.dataset.id + '/delete';
    document.getElementById('deleteNoReg').textContent = btn.dataset.no;
  });
</script>
<?= $this->endSection() ?>