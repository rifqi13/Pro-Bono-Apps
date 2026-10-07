<?= $this->extend('layouts/advokat') ?>

<?= $this->section('styles') ?>
<style>
  /* --- Perbaikan Total Mobile Navigasi (Tanpa Ubah HTML) --- */
  @media (max-width: 576px) {

    /* 1. Atur ulang padding kontainer utama agar pas di HP */
    header#header .header-container {
      padding-top: 10px !important;
      padding-bottom: 10px !important;
    }

    /* 2. Kunci ukuran Logo Kiri agar proporsional dan tidak terdorong */
    header#header a.logo {
      flex-shrink: 0 !important;
    }

    header#header a.logo .logo-image {
      height: 28px !important;
      width: auto !important;
    }

    /* 3. Kunci grup kanan agar posisinya stabil dan tidak berbalik */
    header#header .header-container>.d-flex.align-items-center.gap-3 {
      display: flex !important;
      flex-direction: row !important;
      flex-wrap: nowrap !important;
      gap: 8px !important;
    }

    /* 4. Kunci ukuran Logo Kanan DPN PERADI */
    header#header .header-container>.d-flex.align-items-center.gap-3 img {
      height: 26px !important;
      width: auto !important;
    }

    /* 5. MATIKAN POPPER.JS & KUNCI POSISI DROPDOWN DI KANAN */
    header#header .dropdown {
      position: relative !important;
      /* Menjadi patokan posisi untuk menu di dalamnya */
    }

    header#header .dropdown .dropdown-menu {
      position: absolute !important;
      /* Menghapus gaya instan yang digenerate oleh javascript Popper.js */
      transform: none !important;
      inset: auto !important;

      /* Set posisi manual yang aman dari tabrakan layar */
      top: 100% !important;
      right: 0 !important;
      left: auto !important;

      margin-top: 8px !important;
      display: none;
    }

    /* Memastikan menu tampil dengan posisi benar saat dropdown aktif */
    header#header .dropdown .dropdown-menu.show {
      display: block !important;
    }
  }


  /* Header - TANPA background, shadow, border */
  .header {
    background: transparent !important;
    box-shadow: none !important;
    border: none !important;
    border-bottom: none !important;
    z-index: 1030;
  }

  .header-container {
    max-width: 1400px;
    margin: 0 auto;
  }

  .logo {
    text-decoration: none;
    display: flex;
    align-items: center;
  }

  .logo-image {
    height: auto;
    width: auto;
  }

  /* Tombol User - Tetap di paling kanan */
  .btn-getstarted {
    border-radius: 30px;
    background: #0a1f3c;
    color: white;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    padding: 8px 16px;
  }

  .btn-getstarted:hover {
    background: #1862b5;
    color: white;
  }

  .btn-getstarted::after {
    display: none;
  }

  .dropdown-menu {
    border-radius: 12px;
    padding: 8px 0;
    min-width: 200px;
  }

  .dropdown-item {
    padding: 10px 18px;
    font-size: 14px;
    transition: all 0.2s;
  }

  .dropdown-item:hover {
    background: #f1f5f9;
  }

  /* =============================================
   RESPONSIVE - POSISI TETAP SAMA
   ============================================= */

  /* Tablet (max-width: 991px) */
  @media (max-width: 991px) {
    .header-container {
      padding: 12px 16px !important;
    }

    .logo-image {
      max-height: 32px;
    }

    .logo:last-of-type img {
      height: 32px !important;
    }

    .btn-getstarted {
      padding: 6px 12px;
      font-size: 13px;
    }

    .btn-getstarted i {
      font-size: 18px;
    }
  }

  /* Mobile (max-width: 768px) */
  @media (max-width: 768px) {
    .header-container {
      padding: 10px 12px !important;
    }

    /* PASTIKAN POSISI TIDAK BERUBAH */
    .header .d-flex.justify-content-between {
      flex-direction: row !important;
    }

    /* Logo Kiri tetap di kiri */
    .header .logo:first-of-type {
      order: 1 !important;
    }

    /* Bagian kanan (logo kanan + dropdown) tetap di kanan */
    .header .d-flex.align-items-center.gap-2.gap-sm-3 {
      order: 2 !important;
      flex-direction: row !important;
    }

    /* Dropdown user tetap paling kanan */
    .header .dropdown {
      order: 3 !important;
    }

    .logo-image {
      max-height: 28px !important;
    }

    .logo:last-of-type img {
      height: 28px !important;
    }

    .gap-2 {
      gap: 8px !important;
    }

    .btn-getstarted {
      padding: 5px 10px !important;
      font-size: 12px !important;
    }

    .btn-getstarted i {
      font-size: 16px !important;
    }
  }

  /* Mobile Small (max-width: 576px) */
  @media (max-width: 576px) {
    .header-container {
      padding: 8px 10px !important;
    }

    /* Logo Kiri tetap di kiri */
    .header .logo:first-of-type {
      order: 1 !important;
    }

    /* Bagian kanan tetap di kanan */
    .header .d-flex.align-items-center.gap-2.gap-sm-3 {
      order: 2 !important;
      flex-direction: row !important;
    }

    /* Dropdown user tetap paling kanan */
    .header .dropdown {
      order: 3 !important;
    }

    .logo-image {
      max-height: 24px !important;
    }

    .logo:last-of-type img {
      height: 24px !important;
    }

    .gap-2 {
      gap: 6px !important;
    }

    .btn-getstarted {
      padding: 4px 8px !important;
      font-size: 11px !important;
      border-radius: 20px !important;
    }

    .btn-getstarted i {
      font-size: 14px !important;
    }

    .dropdown-menu {
      min-width: 150px !important;
    }

    .dropdown-item {
      padding: 8px 12px !important;
      font-size: 12px !important;
    }
  }

  /* Mobile Extra Small (max-width: 400px) */
  @media (max-width: 400px) {
    .header-container {
      padding: 6px 8px !important;
    }

    .logo-image {
      max-height: 20px !important;
    }

    .logo:last-of-type img {
      height: 20px !important;
    }

    .gap-2 {
      gap: 4px !important;
    }

    .btn-getstarted {
      padding: 3px 6px !important;
      font-size: 10px !important;
    }

    .btn-getstarted i {
      font-size: 12px !important;
    }
  }

  /* Sembunyikan section yang belum aktif */
  .hidden {
    display: none !important;
  }

  /* Item penerima manfaat */
  .penerima-item {
    background: #f8fafc;
    border: 1px solid #dde5f0;
    border-radius: 8px;
    padding: 15px 18px;
    margin-bottom: 12px;
    position: relative;
  }

  .penerima-item .remove-btn {
    position: absolute;
    top: 8px;
    right: 10px;
    background: #d93025;
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 3px 10px;
    font-size: 11px;
    cursor: pointer;
  }

  .penerima-item .remove-btn:hover {
    background: #b71c1c;
  }

  /* Item foto */
  .foto-item {
    background: #fffdf5;
    border: 1px solid #f0e0b0;
    border-radius: 8px;
    padding: 12px 15px;
    margin-bottom: 10px;
    position: relative;
  }

  .foto-item .remove-btn {
    position: absolute;
    top: 8px;
    right: 10px;
    background: #d97706;
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 3px 10px;
    font-size: 11px;
    cursor: pointer;
  }

  .foto-item .remove-btn:hover {
    background: #b45309;
  }

  /* Input invalid */
  .is-invalid {
    border-color: #d93025 !important;
    background: #fff5f5 !important;
  }

  /* Tombol kecil (sudah ada di template eksisting) */
  .btn-xs {
    padding: 0.30rem 0.50rem !important;
    font-size: 0.7rem;
    border-radius: 0.2rem;
    line-height: 1.2;
  }

  .hidden {
    display: none !important;
  }

  .penerima-item {
    background: #f8fafc;
    border: 1px solid #dde5f0;
    border-radius: 8px;
    padding: 15px 18px;
    margin-bottom: 12px;
    position: relative;
  }

  .penerima-item .remove-btn {
    position: absolute;
    top: 8px;
    right: 10px;
    background: #d93025;
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 3px 10px;
    font-size: 11px;
    cursor: pointer;
  }

  .penerima-item .remove-btn:hover {
    background: #b71c1c;
  }

  .foto-item {
    background: #fffdf5;
    border: 1px solid #f0e0b0;
    border-radius: 8px;
    padding: 12px 15px;
    margin-bottom: 10px;
    position: relative;
  }

  .foto-item .remove-btn {
    position: absolute;
    top: 8px;
    right: 10px;
    background: #d97706;
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 3px 10px;
    font-size: 11px;
    cursor: pointer;
  }

  .foto-item .remove-btn:hover {
    background: #b45309;
  }

  .is-invalid {
    border-color: #d93025 !important;
    background: #fff5f5 !important;
  }

  .autosave-status {
    font-size: 13px;
    color: #6b7280;
  }

  .autosave-status.saving {
    color: #f59e0b;
  }

  .autosave-status.saved {
    color: #10b981;
  }

  .autosave-status.error {
    color: #ef4444;
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
    <h1>Input Data Perkara Pro Bono</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="<?= base_url('advokat/dashboard') ?>">Dashboard</a></li>
        <li class="current">Input Data Perkara Pro Bono</li>
      </ol>
    </nav>
  </div>
</div>

<section class="contact section light-background">
  <div class="container" data-aos="fade-up">

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="fw-bold mb-0">Form Pengajuan</h5>
      <div class="autosave-status" id="autosaveStatus">
        <i class="bi bi-cloud-check"></i> <span id="autosaveText">Auto-save aktif</span>
      </div>
    </div>

    <form id="probonoForm"
      method="post"
      action="<?= base_url('advokat/pengajuan/store') ?>"
      enctype="multipart/form-data"
      novalidate>
      <?= csrf_field() ?>
      <input type="hidden" name="pengajuan_id" id="pengajuanId" value="<?= esc($pengajuan['pengajuan_id'] ?? '') ?>">

      <!-- =============================== -->
      <!-- JENIS LAYANAN                   -->
      <!-- =============================== -->
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
          <label class="form-label fw-semibold">Jenis Layanan <span class="text-danger">*</span></label>
          <div class="d-flex gap-4">
            <div class="form-check">
              <input class="form-check-input" type="radio" name="jenis_layanan" id="layanan_litigasi" value="litigasi">
              <label class="form-check-label" for="layanan_litigasi">Litigasi</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="jenis_layanan" id="layanan_non_litigasi" value="non-litigasi">
              <label class="form-check-label" for="layanan_non_litigasi">Non-Litigasi</label>
            </div>
          </div>
        </div>
      </div>

      <!-- =============================== -->
      <!-- LITIGASI                        -->
      <!-- =============================== -->
      <div id="section-litigasi" class="hidden">
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body">
            <label class="form-label fw-semibold">Penerima Manfaat <span class="text-danger">*</span></label>
            <div id="penerima-container"></div>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="tambahPenerima('litigasi')">
              + Tambah Penerima Manfaat
            </button>
          </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label fw-semibold">Jenis Perkara <span class="text-danger">*</span></label>
              <select name="jenis_perkara" class="form-select">
                <option value="">-- Pilih --</option>
                <option value="perdata_perburuhan">Perdata Perburuhan</option>
                <option value="perdata_pertanahan">Perdata Pertanahan</option>
                <option value="perdata_keluarga">Perdata Keluarga</option>
                <option value="perdata_umum">Perdata Umum</option>
                <option value="pidana_khusus">Pidana Khusus</option>
                <option value="pidana_umum">Pidana Umum</option>
                <option value="tata_usaha_negara">Tata Usaha Negara</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">No. Perkara (opsional)</label>
              <input type="text" name="no_perkara" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Pengadilan (opsional)</label>
              <input type="text" name="pengadilan" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Scan Resume Penanganan Perkara <span class="text-danger">*</span></label>
              <input type="file" name="resume_perkara" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Scan Surat Pernyataan Tidak Sedang Mempunyai Kuasa Hukum <span class="text-danger">*</span></label>
              <input type="file" name="surat_tidak_kuasa_litigasi" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Scan Surat Pernyataan &amp; Penilaian dari Advokat <span class="text-danger">*</span></label>
              <input type="file" name="surat_penilaian_litigasi" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Scan Surat Kuasa <span class="text-danger">*</span></label>
              <input type="file" name="surat_kuasa_litigasi" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
            </div>
            <div>
              <label class="form-label fw-semibold">Dokumen Pendukung (SKTM/KIS/KIP/PKH/BST) <span class="text-danger">*</span></label>
              <select name="jenis_dokumen_litigasi" class="form-select mb-2">
                <option value="">-- Pilih --</option>
                <option value="SKTM">SKTM</option>
                <option value="KIS">KIS</option>
                <option value="KIP">KIP</option>
                <option value="PKH">PKH</option>
                <option value="BST">BST</option>
              </select>
              <input type="file" name="dokumen_pendukung_litigasi" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
            </div>
          </div>
        </div>
      </div>

      <!-- =============================== -->
      <!-- NON-LITIGASI                    -->
      <!-- =============================== -->
      <div id="section-non-litigasi" class="hidden">
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body">
            <label class="form-label fw-semibold">Jenis Kegiatan <span class="text-danger">*</span></label>
            <div class="d-flex gap-4">
              <div class="form-check"><input class="form-check-input" type="radio" name="jenis_non_litigasi" id="nl_seminar" value="seminar"><label class="form-check-label" for="nl_seminar">Seminar</label></div>
              <div class="form-check"><input class="form-check-input" type="radio" name="jenis_non_litigasi" id="nl_penyuluhan" value="penyuluhan"><label class="form-check-label" for="nl_penyuluhan">Penyuluhan</label></div>
              <div class="form-check"><input class="form-check-input" type="radio" name="jenis_non_litigasi" id="nl_pendampingan" value="pendampingan"><label class="form-check-label" for="nl_pendampingan">Pendampingan</label></div>
            </div>
          </div>
        </div>

        <!-- Seminar / Penyuluhan -->
        <div id="section-seminar-penyuluhan" class="hidden">
          <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
              <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Nama Acara <span class="text-danger">*</span></label><input type="text" name="nama_acara" class="form-control"></div>
                <div class="col-md-3"><label class="form-label">Tanggal <span class="text-danger">*</span></label><input type="date" name="tanggal_acara" class="form-control"></div>
                <div class="col-md-3"><label class="form-label">Waktu <span class="text-danger">*</span></label><input type="time" name="waktu_acara" class="form-control"></div>
                <div class="col-md-6"><label class="form-label">Tempat <span class="text-danger">*</span></label><input type="text" name="tempat_acara" class="form-control"></div>
                <div class="col-md-3"><label class="form-label">Peserta <span class="text-danger">*</span></label><input type="text" name="peserta" class="form-control"></div>
                <div class="col-md-3"><label class="form-label">Jumlah Peserta <span class="text-danger">*</span></label><input type="number" name="jumlah_peserta" class="form-control" min="1"></div>
              </div>
              <div class="mt-3">
                <label class="form-label">Upload Surat Undangan / Flyer <span class="text-danger">*</span></label>
                <input type="file" name="undangan_flyer" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
              </div>
              <div class="mt-3">
                <label class="form-label">Bukti Foto (Minimal 3) <span class="text-danger">*</span></label>
                <div id="foto-container-sp"></div>
                <button type="button" class="btn btn-sm btn-outline-warning" onclick="tambahFoto('sp')">+ Tambah Foto</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Pendampingan -->
        <div id="section-pendampingan" class="hidden">
          <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
              <label class="form-label fw-semibold">Penerima Manfaat <span class="text-danger">*</span></label>
              <div id="penerima-container-pendampingan"></div>
              <button type="button" class="btn btn-sm btn-outline-primary" onclick="tambahPenerima('pendampingan')">+ Tambah Penerima Manfaat</button>
            </div>
          </div>

          <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
              <div class="mb-3"><label class="form-label">Scan Surat Pernyataan Tidak Sedang Mempunyai Kuasa Hukum <span class="text-danger">*</span></label><input type="file" name="surat_tidak_kuasa_pendampingan" class="form-control" accept=".pdf,.jpg,.jpeg,.png"></div>
              <div class="mb-3"><label class="form-label">Scan Surat Pernyataan &amp; Penilaian Advokat <span class="text-danger">*</span></label><input type="file" name="surat_penilaian_pendampingan" class="form-control" accept=".pdf,.jpg,.jpeg,.png"></div>
              <div class="mb-3"><label class="form-label">Scan Surat Kuasa <span class="text-danger">*</span></label><input type="file" name="surat_kuasa_pendampingan" class="form-control" accept=".pdf,.jpg,.jpeg,.png"></div>
              <div class="mb-3">
                <label class="form-label">Dokumen Pendukung <span class="text-danger">*</span></label>
                <select name="jenis_dokumen_pendampingan" class="form-select mb-2">
                  <option value="">-- Pilih --</option>
                  <option value="SKTM">SKTM</option>
                  <option value="KIS">KIS</option>
                  <option value="KIP">KIP</option>
                  <option value="PKH">PKH</option>
                  <option value="BST">BST</option>
                </select>
                <input type="file" name="dokumen_pendukung_pendampingan" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
              </div>
              <div class="mb-3"><label class="form-label">Lokasi Pendampingan <span class="text-danger">*</span></label><input type="text" name="lokasi_pendampingan" class="form-control"></div>
              <div>
                <label class="form-label">Bukti Foto <span class="text-danger">*</span></label>
                <div id="foto-container-pendampingan"></div>
                <button type="button" class="btn btn-sm btn-outline-warning" onclick="tambahFoto('pendampingan')">+ Tambah Foto</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="text-center mt-4 mb-5">
        <button type="button" id="btnPreview" class="btn btn-outline-primary btn-lg" disabled><i class="bi bi-eye"></i> Preview</button>
        <button type="submit" id="btnSubmit" class="btn btn-primary btn-lg ms-2" disabled><i class="bi bi-send"></i> Kirim Pengajuan</button>
      </div>
    </form>

  </div>
</section>

<!-- MODAL PREVIEW -->
<div class="modal fade" id="previewModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Preview Pengajuan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="previewContent">Loading...</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali Edit</button>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('probonoForm').requestSubmit()">
          <i class="bi bi-check-circle"></i> Konfirmasi &amp; Kirim
        </button>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- HANYA 2 file JS eksternal — TIDAK ada inline script -->
<script src="<?= base_url('assets/js/form-dynamic.js') ?>"></script>
<script src="<?= base_url('assets/js/autosave.js') ?>"></script>
<script>
  // Init auto-save (tanpa definisi counterPenerima — sudah di form-dynamic.js)
  document.addEventListener('DOMContentLoaded', function() {
    if (typeof initAutoSave === 'function') {
      initAutoSave({
        formId: 'probonoForm',
        endpoint: '<?= base_url('advokat/pengajuan/draft') ?>',
        csrfName: '<?= csrf_token() ?>',
        csrfHash: '<?= csrf_hash() ?>',
        intervalMs: 30000
      });
    }
  });
</script>
<?= $this->endSection() ?>