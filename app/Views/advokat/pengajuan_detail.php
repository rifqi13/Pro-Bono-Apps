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

<div class="page-title light-background">
  <div class="container">
    <h1>Detail Pengajuan</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="<?= base_url('advokat/dashboard') ?>">Dashboard</a></li>
        <li class="current">Detail Pengajuan</li>
      </ol>
    </nav>
  </div>
</div>

<section class="contact section light-background">
  <div class="container" data-aos="fade-up">

    <div class="card border-0 shadow-sm mb-3"><div class="card-body">
  <div class="row g-3">
    <div class="col-md-4"><small class="text-muted">No. Registrasi</small><div class="fw-bold"><code><?= esc($pengajuan['no_registrasi']) ?></code></div></div>
    <div class="col-md-4"><small class="text-muted">Tahun</small><div class="fw-bold"><?= esc($pengajuan['tahun']) ?></div></div>
    <div class="col-md-4"><small class="text-muted">Status</small><div>
      <?php $badge = match($pengajuan['status']) {
        'approved' => 'success', 'rejected' => 'danger',
        'review','submitted' => 'warning', default => 'secondary' }; ?>
      <span class="badge bg-<?= $badge ?>"><?= ucfirst($pengajuan['status']) ?></span>
    </div></div>
    <div class="col-md-6"><small class="text-muted">Jenis Layanan</small><div class="fw-bold"><?= esc($pengajuan['jenis_layanan']) ?><?= $pengajuan['jenis_non_litigasi'] ? ' - ' . esc($pengajuan['jenis_non_litigasi']) : '' ?></div></div>
    <div class="col-md-6"><small class="text-muted">Cabang</small><div class="fw-bold"><?= esc($pengajuan['cabang_nama']) ?></div></div>
  </div>
  <?php if (!empty($pengajuan['catatan_verifikator'])): ?>
    <div class="alert alert-<?= $pengajuan['status'] === 'rejected' ? 'danger' : 'info' ?> mt-3 mb-0">
      <strong>Catatan Verifikator:</strong> <?= esc($pengajuan['catatan_verifikator']) ?>
    </div>
  <?php endif; ?>
</div></div>

<div class="card border-0 shadow-sm mb-3"><div class="card-body">
  <h6 class="fw-bold mb-3">Penerima Manfaat</h6>
  <div class="table-responsive"><table class="table table-sm">
    <thead class="table-light"><tr><th>Usia</th><th>Kategori</th><th>Nama/JK</th><th>Pekerjaan</th><th>KTP</th></tr></thead>
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
  </table></div>
</div></div>

<div class="card border-0 shadow-sm mb-3"><div class="card-body">
  <h6 class="fw-bold mb-3">Dokumen</h6>
  <ul class="list-group list-group-flush">
    <?php foreach ($pengajuan['dokumen'] as $d): ?>
      <li class="list-group-item d-flex justify-content-between">
        <span><?= esc($d['jenis_dokumen']) ?> — <?= esc($d['file_name']) ?></span>
        <a href="<?= base_url('file/dokumen/' . $d['id']) ?>" target="_blank">Lihat</a>
      </li>
    <?php endforeach; ?>
  </ul>
</div></div>

<?php if (!empty($pengajuan['foto'])): ?>
<div class="card border-0 shadow-sm mb-3"><div class="card-body">
  <h6 class="fw-bold mb-3">Bukti Foto</h6>
  <div class="row g-2">
    <?php foreach ($pengajuan['foto'] as $f): ?>
      <div class="col-md-3"><a href="<?= base_url('file/foto/' . $f['id']) ?>" target="_blank"><img src="<?= base_url('file/foto/' . $f['id']) ?>" class="img-fluid rounded" style="height:120px; object-fit:cover;"></a></div>
    <?php endforeach; ?>
  </div>
</div></div>
<?php endif; ?>

<!-- ============================================================ -->
<!-- CATATAN DARI VERIFIKATOR                                     -->
<!-- ============================================================ -->
<?php if (!empty($pengajuan['has_warning']) && !empty($pengajuan['catatan_verifikator'])): ?>
  <div class="card border-warning shadow-sm mb-3" style="border-left: 4px solid #f59e0b !important;">
    <div class="card-body">
      <h6 class="fw-bold text-warning mb-2">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        Catatan dari Verifikator
      </h6>
      <p class="mb-0" style="white-space: pre-line;">
        <?= esc($pengajuan['catatan_verifikator']) ?>
      </p>
      <?php if (!empty($pengajuan['verified_at'])): ?>
        <small class="text-muted d-block mt-2">
          <i class="bi bi-clock"></i>
          <?= tanggal_indo_full($pengajuan['verified_at']) ?>
        </small>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

<!-- Kalau ada feedback list (lebih detail) -->
<!-- ============================================================ -->
<!-- CATATAN DARI VERIFIKATOR                                     -->
<!-- ============================================================ -->
<?php if (!empty($feedbacks)): ?>
  <?php
    $adaYangBelumDitangani = false;
    foreach ($feedbacks as $fb) {
      if (empty($fb['resolved_at'])) { $adaYangBelumDitangani = true; break; }
    }
  ?>
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h6 class="fw-bold mb-0">
          <i class="bi bi-chat-left-text text-warning me-2"></i>
          Catatan dari Verifikator
        </h6>
        <?php if ($adaYangBelumDitangani): ?>
          <a href="<?= base_url('advokat/pengajuan/' . $pengajuan['id'] . '/revisi') ?>" 
             class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil-square"></i> Revisi Pengajuan Lengkap
          </a>
        <?php endif; ?>
      </div>

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

          <!-- Isi Catatan -->
          <div class="mt-2 p-2" style="background: #fef3c7; border-radius: 6px; font-size: 14px;">
            <?= esc($fb['catatan']) ?>
          </div>

          <?php if ($fb['resolved_at']): ?>
            <!-- ✅ SUDAH DIPERBAIKI -->
            <div class="alert alert-success mt-2 mb-0 py-2 px-3" style="font-size: 13px;">
              <div class="d-flex justify-content-between flex-wrap gap-2">
                <strong>
                  <i class="bi bi-check-circle-fill"></i>
                  Sudah Diperbaiki
                </strong>
                <small><?= date('d M Y H:i', strtotime($fb['resolved_at'])) ?></small>
              </div>
              <?php if (!empty($fb['catatan_advokat'])): ?>
                <div class="mt-1"><?= esc($fb['catatan_advokat']) ?></div>
              <?php endif; ?>
              <?php if (!empty($fb['file_perbaikan'])): ?>
                <a href="<?= base_url('file/perbaikan/' . $fb['id']) ?>" 
                   target="_blank"
                   class="btn btn-sm btn-outline-success mt-2">
                  <i class="bi bi-paperclip"></i> Lihat File Perbaikan
                </a>
              <?php endif; ?>
            </div>
          <?php else: ?>
            <!-- ⏳ BELUM DIPERBAIKI — Tombol Upload -->
            <div class="mt-2">
              <button type="button" 
                      class="btn btn-sm btn-warning"
                      data-bs-toggle="modal"
                      data-bs-target="#modalPerbaikan"
                      data-feedback-id="<?= esc($fb['id']) ?>"
                      data-catatan="<?= esc($fb['catatan']) ?>">
                <i class="bi bi-upload"></i> Upload Perbaikan
              </button>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- MODAL UPLOAD PERBAIKAN                                        -->
  <!-- ============================================================ -->
  <div class="modal fade" id="modalPerbaikan" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" action="" enctype="multipart/form-data" id="formPerbaikan">
          <?= csrf_field() ?>
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="bi bi-upload text-warning"></i>
              Upload Perbaikan
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="alert alert-warning" style="font-size: 13px;">
              <strong>Catatan Verifikator:</strong>
              <div id="modalCatatanAdmin" class="mt-1"></div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">
                File Perbaikan <span class="text-muted">(opsional)</span>
              </label>
              <input type="file" name="file_perbaikan" 
                     class="form-control" 
                     accept=".pdf,.jpg,.jpeg,.png">
              <small class="text-muted">Format PDF/JPG/PNG, maks 5 MB</small>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">
                Keterangan Perbaikan <span class="text-muted">(opsional)</span>
              </label>
              <textarea name="catatan_advokat" 
                        class="form-control" 
                        rows="3"
                        placeholder="Contoh: Resume sudah saya lengkapi dengan uraian perkara."></textarea>
            </div>

            <div class="alert alert-info mb-0" style="font-size: 12px;">
              <i class="bi bi-info-circle"></i>
              Kalau banyak perubahan, gunakan tombol 
              <strong>Revisi Pengajuan Lengkap</strong> untuk edit semua sekaligus.
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-send"></i> Kirim Perbaikan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    document.getElementById('modalPerbaikan').addEventListener('show.bs.modal', function (event) {
      const btn = event.relatedTarget;
      const feedbackId = btn.dataset.feedbackId;
      const catatan = btn.dataset.catatan;

      document.getElementById('modalCatatanAdmin').textContent = catatan;
      document.getElementById('formPerbaikan').action =
        '<?= base_url('advokat/pengajuan/feedback') ?>/' + feedbackId + '/tangani';
    });
  </script>
<?php endif; ?>
    

  </div>
</section>




<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Event Delegation — pasang di document, bukan di elemen
document.addEventListener('click', function (e) {
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