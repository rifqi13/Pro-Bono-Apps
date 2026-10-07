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
    <h1>Detail Pengajuan Pro Bono</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="<?= base_url('advokat/dashboard') ?>">Dashboard</a></li>
        <li class="current">Detail Pengajuan Pro Bono</li>
      </ol>
    </nav>
  </div>
</div>

<section class="contact section light-background">
  <div class="container" data-aos="fade-up">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h3 class="fw-bold mb-0">Detail Pengajuan — <code><?= esc($pengajuan['no_registrasi']) ?></code></h3>
        <small class="text-muted"><i class="bi bi-lock-fill"></i> Mode read-only</small>
      </div>
      <a href="<?= base_url('cabang/pengajuan') ?>" class="btn btn-sm btn-outline-secondary">← Kembali</a>
    </div>

    <?php if (!empty($pengajuan['has_warning'])): ?>
      <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle"></i> <strong>Pengajuan ini memiliki catatan dari verifikator.</strong>
        <?php if (!empty($pengajuan['catatan_verifikator'])): ?>
          <div class="mt-1" style="font-size:13px;"><?= esc($pengajuan['catatan_verifikator']) ?></div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <!-- Info Header -->
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3"><small class="text-muted">Advokat</small>
            <div class="fw-bold"><?= esc($pengajuan['advokat_nama']) ?>, <?= esc($pengajuan['advokat_gelar']) ?></div>
          </div>
          <div class="col-md-2"><small class="text-muted">NIA</small>
            <div class="fw-bold"><code><?= esc($pengajuan['advokat_nia']) ?></code></div>
          </div>
          <div class="col-md-2"><small class="text-muted">Cabang</small>
            <div class="fw-bold"><?= esc($pengajuan['cabang_nama']) ?></div>
          </div>
          <div class="col-md-2"><small class="text-muted">Jenis</small>
            <div class="fw-bold"><?= esc($pengajuan['jenis_layanan']) ?><?= $pengajuan['jenis_non_litigasi'] ? ' - ' . esc($pengajuan['jenis_non_litigasi']) : '' ?></div>
          </div>
          <div class="col-md-3"><small class="text-muted">Status</small>
            <div>
              <?php $badge = match ($pengajuan['status']) {
                'approved' => 'success',
                'rejected' => 'danger',
                'review', 'submitted' => 'warning',
                default => 'secondary'
              }; ?>
              <span class="badge bg-<?= $badge ?>"><?= ucfirst($pengajuan['status']) ?></span>
            </div>
          </div>
        </div>
        <?php if ($pengajuan['verified_at']): ?>
          <div class="mt-3 pt-3 border-top" style="font-size:13px;">
            <i class="bi bi-check-circle text-success"></i>
            Diverifikasi pada <strong><?= date('d M Y H:i', strtotime($pengajuan['verified_at'])) ?></strong>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Detail Spesifik -->
    <?php if (!empty($pengajuan['detail'])): ?>
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
          <h6 class="fw-bold">Detail <?= esc(ucfirst($pengajuan['jenis_layanan'])) ?></h6>
          <table class="table table-sm">
            <?php foreach ($pengajuan['detail'] as $k => $v): if (in_array($k, ['pengajuan_id', 'created_at', 'updated_at'])) continue; ?>
              <tr>
                <th width="30%"><?= esc(ucwords(str_replace('_', ' ', $k))) ?></th>
                <td><?= esc($v) ?></td>
              </tr>
            <?php endforeach; ?>
          </table>
        </div>
      </div>
    <?php endif; ?>

    <!-- Penerima Manfaat -->
    <?php if (!empty($pengajuan['penerima_manfaat'])): ?>
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
          <h6 class="fw-bold">Penerima Manfaat (<?= count($pengajuan['penerima_manfaat']) ?>)</h6>
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
                  <td><span class="badge bg-<?= $pm['kategori'] === 'anak' ? 'info' : 'secondary' ?>"><?= esc($pm['kategori']) ?></span></td>
                  <td><?= esc($pm['kategori'] === 'anak' ? $pm['nama'] : ($pm['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan')) ?></td>
                  <td><?= esc($pm['pekerjaan'] ?? '-') ?></td>
                  <td><?php if ($pm['file_ktp']): ?><a href="<?= base_url('file/ktp/' . $pm['id']) ?>" target="_blank">Lihat</a><?php endif; ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endif; ?>

    <!-- Dokumen -->
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <h6 class="fw-bold">Dokumen (<?= count($pengajuan['dokumen']) ?>)</h6>
        <ul class="list-group list-group-flush">
          <?php foreach ($pengajuan['dokumen'] as $d): ?>
            <li class="list-group-item d-flex justify-content-between">
              <span><?= esc(ucwords(str_replace('_', ' ', $d['jenis_dokumen']))) ?> — <small class="text-muted"><?= esc($d['file_name']) ?></small></span>
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
              <div class="col-md-3">
                <a href="<?= base_url('file/foto/' . $f['id']) ?>" target="_blank">
                  <img src="<?= base_url('file/foto/' . $f['id']) ?>" class="img-fluid rounded" style="height:120px; object-fit:cover;">
                </a>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- Tidak ada tombol aksi apapun -->
    <div class="alert alert-secondary text-center">
      <i class="bi bi-lock-fill"></i> Mode read-only — Anda tidak dapat mengubah data ini.
    </div>
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