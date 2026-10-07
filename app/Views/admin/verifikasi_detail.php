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
    <h1>Detail Verifikasi</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="<?= base_url('advokat/dashboard') ?>">Dashboard</a></li>
        <li class="current">Detail Verifikasi</li>
      </ol>
    </nav>
  </div>
</div>

<section class="contact section light-background">
  <div class="container" data-aos="fade-up">

    <div class="d-flex justify-content-between mb-3">
      <h3 class="fw-bold mb-0">Detail Verifikasi — <code><?= esc($pengajuan['no_registrasi']) ?></code></h3>
      <a href="<?= base_url('admin/verifikasi') ?>" class="btn btn-sm btn-outline-secondary">← Kembali</a>
    </div>

    <?php if (!$validasi['valid']): ?>
      <div class="alert alert-warning">
        <strong><i class="bi bi-exclamation-triangle"></i> Validasi Otomatis: Ada kekurangan</strong>
        <ul class="mb-0 mt-1">
          <?php foreach ($validasi['errors'] as $err): ?><li><?= esc($err) ?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php else: ?>
      <div class="alert alert-success"><i class="bi bi-check-circle"></i> Validasi otomatis: Dokumen lengkap.</div>
    <?php endif; ?>

    <!-- Data Header -->
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3"><small class="text-muted">Advokat</small>
            <div class="fw-bold"><?= esc($pengajuan['advokat_nama']) ?>, <?= esc($pengajuan['advokat_gelar']) ?></div>
          </div>
          <div class="col-md-3"><small class="text-muted">NIA</small>
            <div class="fw-bold"><?= esc($pengajuan['advokat_nia']) ?></div>
          </div>
          <div class="col-md-3"><small class="text-muted">Cabang</small>
            <div class="fw-bold"><?= esc($pengajuan['cabang_nama']) ?></div>
          </div>
          <div class="col-md-3"><small class="text-muted">Jenis</small>
            <div class="fw-bold"><?= esc($pengajuan['jenis_layanan']) ?><?= $pengajuan['jenis_non_litigasi'] ? ' - ' . esc($pengajuan['jenis_non_litigasi']) : '' ?></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Penerima Manfaat -->
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <h6 class="fw-bold">Penerima Manfaat</h6>
        <table class="table table-sm">
          <thead class="table-light">
            <tr>
              <th>Usia</th>
              <th>Kategori</th>
              <th>Nama/JK</th>
              <th>Pekerjaan</th>
              <th>KTP</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($pengajuan['penerima_manfaat'] as $pm): ?>
              <tr>
                <td><?= esc($pm['usia']) ?></td>
                <td><?= esc($pm['kategori']) ?></td>
                <td><?= esc($pm['kategori'] === 'anak' ? $pm['nama'] : ($pm['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan')) ?></td>
                <td><?= esc($pm['pekerjaan'] ?? '-') ?></td>
                <td><?php if ($pm['file_ktp']): ?><a href="<?= base_url('file/ktp/' . $pm['id']) ?>" target="_blank">Lihat</a><?php endif; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Dokumen -->
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <h6 class="fw-bold">Dokumen</h6>
        <ul class="list-group list-group-flush">
          <?php foreach ($pengajuan['dokumen'] as $d): ?>
            <li class="list-group-item d-flex justify-content-between">
              <span><?= esc($d['jenis_dokumen']) ?> — <?= esc($d['file_name']) ?></span>
              <a href="<?= base_url('file/dokumen/' . $d['id']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">Buka</a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <!-- Foto -->
    <?php if (!empty($pengajuan['foto'])): ?>
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
          <h6 class="fw-bold">Bukti Foto (<?= count($pengajuan['foto']) ?>)</h6>
          <div class="row g-2">
            <?php foreach ($pengajuan['foto'] as $f): ?>
              <div class="col-md-3"><a href="<?= base_url('file/foto/' . $f['id']) ?>" target="_blank"><img src="<?= base_url('file/foto/' . $f['id']) ?>" class="img-fluid rounded" style="height:120px; object-fit:cover;"></a></div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- ============================================================ -->
<!-- RIWAYAT FEEDBACK (ADMIN VIEW)                                -->
<!-- ============================================================ -->
<?php if (!empty($feedbacks)): ?>
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
      <h6 class="fw-bold mb-3">
        <i class="bi bi-chat-left-text text-primary me-2"></i>
        Riwayat Feedback
      </h6>

      <?php foreach ($feedbacks as $fb): ?>
        <div class="border-start border-3 ps-3 mb-4 <?= $fb['resolved_at'] ? 'border-success' : 'border-warning' ?>">
          
          <!-- Header: Admin + Waktu -->
          <div class="d-flex justify-content-between flex-wrap gap-2">
            <strong style="font-size: 13px;">
              <i class="bi bi-person-circle text-primary me-1"></i>
              <?= esc($fb['admin_nama'] ?? 'Admin Verifikator') ?>
            </strong>
            <small class="text-muted">
              <i class="bi bi-clock me-1"></i>
              <?= date('d M Y H:i', strtotime($fb['created_at'])) ?>
            </small>
          </div>

          <!-- Catatan Admin -->
          <div class="mt-2 p-2" style="background: #fef3c7; border-radius: 6px; font-size: 14px;">
            <strong>Catatan:</strong> <?= esc($fb['catatan']) ?>
          </div>

          <?php if ($fb['resolved_at']): ?>
            <!-- ✅ SUDAH DIPERBAIKI OLEH ADVOKAT -->
            <div class="alert alert-success mt-2 mb-0 py-2 px-3" style="font-size: 13px;">
              <div class="d-flex justify-content-between flex-wrap gap-2">
                <strong>
                  <i class="bi bi-check-circle-fill"></i>
                  Sudah Diperbaiki oleh Advokat
                </strong>
                <small>
                  <i class="bi bi-clock me-1"></i>
                  <?= date('d M Y H:i', strtotime($fb['resolved_at'])) ?>
                </small>
              </div>

              <!-- ✅ Keterangan dari advokat -->
              <?php if (!empty($fb['catatan_advokat'])): ?>
                <div class="mt-2 p-2" style="background: #d1fae5; border-radius: 4px;">
                  <strong>Keterangan Advokat:</strong>
                  <div><?= esc($fb['catatan_advokat']) ?></div>
                </div>
              <?php endif; ?>

              <!-- ✅ LINK FILE PERBAIKAN -->
              <?php if (!empty($fb['file_perbaikan'])): ?>
                <div class="mt-2">
                  <a href="<?= base_url('file/perbaikan/' . $fb['id']) ?>" 
                     target="_blank"
                     class="btn btn-sm btn-success">
                    <i class="bi bi-paperclip"></i> Lihat File Perbaikan
                  </a>
                  <a href="<?= base_url('file/perbaikan/' . $fb['id']) ?>" 
                     download
                     class="btn btn-sm btn-outline-success ms-2">
                    <i class="bi bi-download"></i> Download
                  </a>
                </div>
              <?php else: ?>
                <div class="mt-2 text-muted" style="font-size: 12px;">
                  <i class="bi bi-info-circle"></i>
                  Advokat hanya memberi keterangan tanpa file.
                </div>
              <?php endif; ?>
            </div>
          <?php else: ?>
            <!-- ⏳ BELUM DIPERBAIKI -->
            <div class="mt-2">
              <span class="badge bg-warning text-dark">
                <i class="bi bi-hourglass-split"></i>
                Menunggu perbaikan advokat
              </span>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>

<!-- ============================================================ -->
<!-- TINDAKAN VERIFIKASI                                          -->
<!-- ============================================================ -->
<?php
  $bisaVerifikasi = in_array($pengajuan['status'], ['submitted', 'review'], true);
?>

<?php if ($bisaVerifikasi): ?>
  <div class="card border-0 shadow-sm mt-3">
    <div class="card-body">
      <h6 class="fw-bold mb-3">
        <i class="bi bi-gear-fill text-primary me-2"></i>
        Tindakan Verifikasi
      </h6>

      <?php if ($pengajuan['has_warning']): ?>
        <div class="alert alert-warning">
          <i class="bi bi-exclamation-triangle"></i>
          <strong>Pengajuan ini punya <?= esc($pengajuan['warning_count']) ?> catatan.</strong>
          Pastikan advokat sudah memperbaiki sebelum menyetujui.
        </div>
      <?php endif; ?>

      <!-- ======================================== -->
      <!-- APPROVE                                   -->
      <!-- ======================================== -->
      <div class="border rounded p-3 mb-3" style="background: #f0fdf4;">
        <h6 class="fw-bold text-success mb-2">
          <i class="bi bi-check-circle-fill"></i> Setujui Pengajuan
        </h6>
        <form method="post" 
              action="<?= base_url('admin/verifikasi/' . $pengajuan['id'] . '/approve') ?>"
              onsubmit="return confirm('Setujui pengajuan ini?')">
          <?= csrf_field() ?>

          <div class="mb-2">
            <label class="form-label fw-semibold">
              Durasi Penanganan (Jam) <span class="text-danger">*</span>
            </label>
            <input type="number" 
                   name="durasi_jam" 
                   class="form-control" 
                   min="1" max="9999" 
                   value="<?= esc($pengajuan['durasi_jam'] ?? 24) ?>"
                   required>
            <small class="text-muted">
              Setiap 50 jam akumulasi = 1 sertifikat pro bono.
            </small>
          </div>

          <div class="mb-3">
            <label class="form-label">Catatan Verifikator (opsional)</label>
            <textarea name="catatan_verifikator" 
                      class="form-control" 
                      rows="2"
                      placeholder="Contoh: Disetujui, dokumen lengkap."></textarea>
          </div>

          <button type="submit" class="btn btn-success">
            <i class="bi bi-check2-circle"></i> Setujui Pengajuan
          </button>
        </form>
      </div>

      <!-- ======================================== -->
      <!-- FEEDBACK BARU                             -->
      <!-- ======================================== -->
      <div class="border rounded p-3 mb-3" style="background: #fffbeb;">
        <h6 class="fw-bold text-warning mb-2">
          <i class="bi bi-chat-left-text-fill"></i> Kirim Catatan Baru
        </h6>
        <form method="post" 
              action="<?= base_url('admin/verifikasi/' . $pengajuan['id'] . '/feedback') ?>">
          <?= csrf_field() ?>

          <div class="mb-2">
            <label class="form-label fw-semibold">Kategori</label>
            <select name="kategori" class="form-select">
              <option value="kekurangan_dokumen">Kekurangan Dokumen</option>
              <option value="revisi_data">Revisi Data</option>
              <option value="lainnya">Lainnya</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">
              Catatan <span class="text-danger">*</span>
            </label>
            <textarea name="catatan" 
                      class="form-control" 
                      rows="3" 
                      required
                      minlength="5"
                      placeholder="Contoh: Resume masih kurang lengkap, mohon tambahkan uraian perkara."></textarea>
          </div>

          <button type="submit" class="btn btn-warning">
            <i class="bi bi-send"></i> Kirim Catatan
          </button>
        </form>
      </div>

      <!-- ======================================== -->
      <!-- TOLAK                                     -->
      <!-- ======================================== -->
      <div class="border rounded p-3" style="background: #fef2f2;">
        <h6 class="fw-bold text-danger mb-2">
          <i class="bi bi-x-circle-fill"></i> Tolak Pengajuan
        </h6>
        <form method="post" 
              action="<?= base_url('admin/verifikasi/' . $pengajuan['id'] . '/reject') ?>"
              onsubmit="return confirm('Yakin TOLAK pengajuan ini? Tindakan ini tidak bisa dibatalkan.')">
          <?= csrf_field() ?>

          <div class="mb-3">
            <label class="form-label fw-semibold">
              Alasan Penolakan <span class="text-danger">*</span>
            </label>
            <textarea name="catatan_verifikator" 
                      class="form-control" 
                      rows="2" 
                      required
                      minlength="10"
                      placeholder="Alasan penolakan..."></textarea>
          </div>

          <button type="submit" class="btn btn-danger">
            <i class="bi bi-x-circle"></i> Tolak Pengajuan
          </button>
        </form>
      </div>

    </div>
  </div>
<?php else: ?>
  <!-- Status sudah final (approved/rejected) -->
  <div class="card border-0 shadow-sm mt-3">
    <div class="card-body text-center py-4">
      <i class="bi bi-check-circle-fill text-success" style="font-size: 48px;"></i>
      <h5 class="fw-bold mt-3">Pengajuan Sudah Diverifikasi</h5>
      <p class="text-muted mb-2">
        Status: 
        <span class="badge bg-<?= $pengajuan['status'] === 'approved' ? 'success' : 'danger' ?>">
          <?= esc(ucfirst($pengajuan['status'])) ?>
        </span>
      </p>
      <?php if (!empty($pengajuan['verified_at'])): ?>
        <small class="text-muted">
          <i class="bi bi-clock"></i>
          <?= date('d M Y H:i', strtotime($pengajuan['verified_at'])) ?>
        </small>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

  </div>
</section>



<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  // ✅ Event Delegation — pasang di document, bukan di elemen
  document.addEventListener('click', function(e) {
    const link = e.target.closest('.notif-link');
    if (!link) return;

    const notifId = link.dataset.notifId;
    const targetUrl = link.href;

    console.log('🔔 Klik notif ID:', notifId, '→', targetUrl);

    if (!notifId) return;

    e.preventDefault();

    fetch('<?= base_url('notifications') ?>/' + notifId + '/read', {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
        }
      })
      .then(r => r.json())
      .then(data => {
        console.log('✅ Response:', data);
        if (data.success) {
          const badge = document.querySelector('#dropdownNotif .badge');
          if (badge) {
            if (data.unread <= 0) {
              badge.remove();
            } else {
              badge.textContent = data.unread > 9 ? '9+' : data.unread;
            }
          }
        }
      })
      .catch(err => console.error('❌ Error:', err))
      .finally(() => {
        if (targetUrl && targetUrl !== '#' && !targetUrl.endsWith('#')) {
          window.location.href = targetUrl;
        }
      });
  });
</script>
<?= $this->endSection() ?>