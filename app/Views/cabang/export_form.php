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
    <h1>Export Laporan</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="<?= base_url('advokat/dashboard') ?>">Dashboard</a></li>
        <li class="current">Export Laporan</li>
      </ol>
    </nav>
  </div>
</div>

<section class="contact section light-background">
  <div class="container" data-aos="fade-up">
    <div class="alert alert-info">
      <i class="bi bi-info-circle"></i> Export hanya mencakup data pengajuan dari cabang Anda.
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <h6 class="fw-bold mb-3">Filter Export</h6>

        <form method="post" id="exportForm">
          <?= csrf_field() ?>
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">Dari Tanggal</label>
              <input type="date" name="dari" class="form-control">
            </div>
            <div class="col-md-3">
              <label class="form-label">Sampai Tanggal</label>
              <input type="date" name="sampai" class="form-control">
            </div>
            <div class="col-md-3">
              <label class="form-label">Tahun</label>
              <select name="tahun" class="form-select">
                <option value="">Semua</option>
                <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                  <option value="<?= $y ?>"><?= $y ?></option>
                <?php endfor; ?>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <option value="">Semua</option>
                <option value="approved">Disetujui</option>
                <option value="rejected">Ditolak</option>
                <option value="submitted">Submitted</option>
                <option value="review">Review</option>
              </select>
            </div>
          </div>

          <div class="mt-4 d-flex gap-2">
            <button type="button" class="btn btn-success" onclick="doExport('excel')"><i class="bi bi-file-earmark-excel"></i> Export Excel</button>
            <button type="button" class="btn btn-danger" onclick="doExport('pdf')"><i class="bi bi-file-earmark-pdf"></i> Export PDF</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>




<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  function doExport(type) {
    const form = document.getElementById('exportForm');
    form.action = '<?= base_url('cabang/export') ?>/' + type;
    form.submit();
  }
</script>
<?= $this->endSection() ?>