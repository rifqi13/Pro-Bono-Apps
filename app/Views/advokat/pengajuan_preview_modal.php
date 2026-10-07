<?php $d = $draft['draft_data']; ?>
<h6 class="fw-bold">Ringkasan Pengajuan</h6>
<table class="table table-sm">
  <tr><th width="35%">Jenis Layanan</th><td><?= esc($d['jenis_layanan'] ?? '-') ?></td></tr>
  <?php if (($d['jenis_layanan'] ?? '') === 'litigasi'): ?>
    <tr><th>Jenis Perkara</th><td><?= esc($d['jenis_perkara'] ?? '-') ?></td></tr>
  <?php elseif (($d['jenis_layanan'] ?? '') === 'non-litigasi'): ?>
    <tr><th>Jenis Kegiatan</th><td><?= esc($d['jenis_non_litigasi'] ?? '-') ?></td></tr>
    <?php if (in_array($d['jenis_non_litigasi'] ?? '', ['seminar','penyuluhan'])): ?>
      <tr><th>Nama Acara</th><td><?= esc($d['nama_acara'] ?? '-') ?></td></tr>
      <tr><th>Tanggal</th><td><?= esc($d['tanggal_acara'] ?? '-') ?> <?= esc($d['waktu_acara'] ?? '') ?></td></tr>
      <tr><th>Tempat</th><td><?= esc($d['tempat_acara'] ?? '-') ?></td></tr>
      <tr><th>Peserta</th><td><?= esc($d['peserta'] ?? '-') ?> (<?= esc($d['jumlah_peserta'] ?? 0) ?> orang)</td></tr>
    <?php elseif (($d['jenis_non_litigasi'] ?? '') === 'pendampingan'): ?>
      <tr><th>Lokasi</th><td><?= esc($d['lokasi_pendampingan'] ?? '-') ?></td></tr>
    <?php endif; ?>
  <?php endif; ?>
</table>

<div class="alert alert-info mb-0">
  <i class="bi bi-info-circle"></i> Periksa kembali data Anda. Setelah dikirim, pengajuan tidak dapat diubah kecuali oleh admin.
</div>