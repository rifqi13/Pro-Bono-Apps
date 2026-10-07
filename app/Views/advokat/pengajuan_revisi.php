<?= $this->extend('layouts/advokat') ?>

<?= $this->section('styles') ?>
<style>
  .hidden {
    display: none !important;
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
    <h1>Revisi Pengajuan</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="<?= base_url('advokat/dashboard') ?>">Dashboard</a></li>
        <li><a href="<?= base_url('advokat/pengajuan/' . $pengajuan['id']) ?>">Detail</a></li>
        <li class="current">Revisi</li>
      </ol>
    </nav>
  </div>
</div>

<section class="section">
  <div class="container">

    <!-- Info Pengajuan -->
    <div class="alert alert-info">
      <strong>No. Registrasi:</strong> <?= esc($pengajuan['no_registrasi']) ?><br>
      <strong>Status:</strong>
      <span class="badge bg-warning"><?= esc($pengajuan['status']) ?></span>
      <br>
      <small class="text-muted">
        Silakan perbaiki data di bawah ini, lalu klik "Kirim Revisi".
        Semua catatan verifikator akan otomatis ditandai selesai.
      </small>
    </div>

    <!-- Catatan Verifikator -->
    <?php if (!empty($feedbacks)): ?>
      <div class="card border-warning shadow-sm mb-4">
        <div class="card-body">
          <h6 class="fw-bold text-warning mb-3">
            <i class="bi bi-exclamation-triangle-fill"></i>
            Catatan yang Harus Diperbaiki
          </h6>
          <?php foreach ($feedbacks as $fb): ?>
            <div class="border-start border-3 border-warning ps-3 mb-2">
              <small class="text-muted"><?= date('d M Y H:i', strtotime($fb['created_at'])) ?></small>
              <div><?= esc($fb['catatan']) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Form Revisi (sama seperti create, tapi action ke revisiStore) -->
    <form method="post"
      action="<?= base_url('advokat/pengajuan/' . $pengajuan['id'] . '/revisi') ?>"
      enctype="multipart/form-data"
      novalidate>
      <?= csrf_field() ?>

      <!-- Copy isi form dari pengajuan_form.php, isi dengan data lama -->
      <?= view('advokat/_form_revisi_content', ['pengajuan' => $pengajuan]) ?>

      <div class="text-center mt-4 mb-5">
        <button type="submit" class="btn btn-primary btn-lg">
          <i class="bi bi-send"></i> Kirim Revisi
        </button>
        <a href="<?= base_url('advokat/pengajuan/' . $pengajuan['id']) ?>"
          class="btn btn-secondary btn-lg ms-2">
          Batal
        </a>
      </div>

    </form>

  </div>
</section>

<?= $this->endSection() ?>