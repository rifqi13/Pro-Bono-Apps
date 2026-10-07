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


<section id="hero" class="hero section">
  <div class="container" data-aos="fade-up" data-aos-delay="100">

    <div class="row g-3 mb-4 mt-5">

      <!-- Total -->
      <div class="col-6 col-md-4 col-lg-2">
        <div class="card stat-card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon bg-primary bg-opacity-10 text-primary">
              <i class="bi bi-collection"></i>
            </div>
            <div>
              <div class="stat-label">Total</div>
              <div class="stat-value"><?= $stats['total'] ?></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Submitted -->
      <div class="col-6 col-md-4 col-lg-2">
        <div class="card stat-card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon bg-info bg-opacity-10 text-info">
              <i class="bi bi-send"></i>
            </div>
            <div>
              <div class="stat-label">Submitted</div>
              <div class="stat-value text-info"><?= $stats['submitted'] ?></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Review -->
      <div class="col-6 col-md-4 col-lg-2">
        <div class="card stat-card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon bg-warning bg-opacity-10 text-warning">
              <i class="bi bi-hourglass-split"></i>
            </div>
            <div>
              <div class="stat-label">Review</div>
              <div class="stat-value text-warning"><?= $stats['review'] ?></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Disetujui -->
      <div class="col-6 col-md-4 col-lg-2">
        <div class="card stat-card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon bg-success bg-opacity-10 text-success">
              <i class="bi bi-check-circle"></i>
            </div>
            <div>
              <div class="stat-label">Disetujui</div>
              <div class="stat-value text-success"><?= $stats['approved'] ?></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Ditolak -->
      <div class="col-6 col-md-4 col-lg-2">
        <div class="card stat-card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon bg-danger bg-opacity-10 text-danger">
              <i class="bi bi-x-circle"></i>
            </div>
            <div>
              <div class="stat-label">Ditolak</div>
              <div class="stat-value text-danger"><?= $stats['rejected'] ?></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Warning -->
      <div class="col-6 col-md-4 col-lg-2">
        <div class="card stat-card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="stat-icon bg-warning bg-opacity-10 text-warning">
              <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div>
              <div class="stat-label">Warning</div>
              <div class="stat-value text-warning"><?= $stats['warning'] ?></div>
            </div>
          </div>
        </div>
      </div>

    </div>



    <div class="row g-4 mb-5">
      <div class="col-12">
        <h4 class="fw-bold text-dark mb-1 mt-4"><i class="bi bi-people-fill text-primary me-2"></i>Rekapitulasi Pro Bono Berdasarkan Kategori Perkara</h4>
      </div>


      <?= $this->renderSection('styles') ?>
      <style>
        /* ============================================
     PERBAIKAN TAMPILAN DATATABLE
     ============================================ */
        /* --- Kartu Statistik --- */
        .stat-card {
          border-radius: 12px;
          transition: all 0.3s ease;
          background-color: #ffffff;
          border-left: 4px solid transparent;
          /* Aksen kiri */
        }

        /* Efek Hover: Naik + Shadow Lebih Tebal */
        .stat-card:hover {
          transform: translateY(-5px);
          box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
        }

        /* Warna Aksen Kiri (Sesuaikan dengan Kategori) */
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

        /* Container utama DataTable */
        .data-table-wrapper {
          padding: 0;
          background: transparent;
        }

        /* Bagian atas (Show entries + Search) */
        .dataTables_wrapper .dataTables_length {
          float: left;
          margin-bottom: 16px;
        }

        .dataTables_wrapper .dataTables_length label {
          font-size: 13px;
          color: #495057;
          font-weight: 500;
          display: flex;
          align-items: center;
          gap: 8px;
        }

        .dataTables_wrapper .dataTables_length select {
          border: 1px solid #d1d5db;
          border-radius: 8px;
          padding: 6px 30px 6px 12px;
          font-size: 13px;
          background: white url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E") no-repeat right 12px center;
          background-size: 12px;
          appearance: none;
          -webkit-appearance: none;
          cursor: pointer;
          transition: border-color 0.2s;
        }

        .dataTables_wrapper .dataTables_length select:focus {
          border-color: #0d6efd;
          outline: none;
          box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
        }

        /* Search box */
        .dataTables_wrapper .dataTables_filter {
          float: right;
          margin-bottom: 16px;
        }

        .dataTables_wrapper .dataTables_filter label {
          font-size: 13px;
          color: #495057;
          font-weight: 500;
          display: flex;
          align-items: center;
          gap: 8px;
        }

        .dataTables_wrapper .dataTables_filter input {
          border: 1px solid #d1d5db;
          border-radius: 8px;
          padding: 6px 14px 6px 36px;
          font-size: 13px;
          width: 220px;
          background: white url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='M21 21l-4.35-4.35'/%3E%3C/svg%3E") no-repeat left 12px center;
          background-size: 16px;
          transition: all 0.2s;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
          border-color: #0d6efd;
          outline: none;
          box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
          width: 260px;
        }

        .dataTables_wrapper .dataTables_filter input::placeholder {
          color: #9ca3af;
        }

        /* Clearfix untuk wrapper */
        .dataTables_wrapper .dataTables_length::after,
        .dataTables_wrapper .dataTables_filter::after {
          content: '';
          display: table;
          clear: both;
        }

        /* ============================================
     TABEL
     ============================================ */
        .table-custom {
          width: 100% !important;
          border-collapse: separate;
          border-spacing: 0;
          font-size: 13px;
        }

        .table-custom thead th {
          background: #f8fafc;
          color: #1e293b;
          font-weight: 600;
          font-size: 12px;
          text-transform: uppercase;
          letter-spacing: 0.5px;
          padding: 12px 16px;
          border-bottom: 2px solid #e2e8f0;
          white-space: nowrap;
          position: sticky;
          top: 0;
          z-index: 2;
        }

        .table-custom thead th:first-child {
          border-radius: 12px 0 0 0;
        }

        .table-custom thead th:last-child {
          border-radius: 0 12px 0 0;
        }

        .table-custom tbody td {
          padding: 11px 16px;
          border-bottom: 1px solid #f1f3f5;
          vertical-align: middle;
          color: #1e293b;
        }

        .table-custom tbody tr {
          transition: background 0.15s ease;
        }

        .table-custom tbody tr:hover {
          background: #f8fafc;
        }

        .table-custom tbody tr:last-child td {
          border-bottom: none;
        }

        /* Nomor urut */
        .table-custom .col-no {
          color: #94a3b8;
          font-weight: 500;
          font-size: 12px;
          text-align: center;
          width: 45px;
        }

        /* Nama cabang */
        .table-custom .col-cabang {
          font-weight: 600;
          color: #0f172a;
        }

        /* Angka */
        .table-custom .col-number {
          text-align: center;
          font-weight: 600;
          font-variant-numeric: tabular-nums;
        }

        .table-custom .col-advokat {
          color: #0d6efd;
        }

        .table-custom .col-masuk {
          color: #f59e0b;
        }

        .table-custom .col-sukses {
          color: #10b981;
        }

        /* Progress bar di tabel */
        .progress-custom {
          display: flex;
          align-items: center;
          justify-content: center;
          gap: 10px;
        }

        .progress-custom .progress-bar-track {
          width: 70px;
          height: 6px;
          background: #e9ecef;
          border-radius: 10px;
          overflow: hidden;
        }

        .progress-custom .progress-bar-fill {
          height: 100%;
          border-radius: 10px;
          transition: width 0.6s ease;
        }

        .progress-custom .progress-text {
          font-weight: 700;
          font-size: 13px;
          min-width: 42px;
          text-align: right;
        }

        /* Status Badge */
        .badge-status {
          padding: 4px 14px;
          border-radius: 30px;
          font-size: 11px;
          font-weight: 600;
          letter-spacing: 0.3px;
          display: inline-block;
          text-align: center;
          min-width: 100px;
        }

        .badge-status.sangat-baik {
          background: #d1fae5;
          color: #065f46;
        }

        .badge-status.baik {
          background: #fef3c7;
          color: #92400e;
        }

        .badge-status.cukup {
          background: #dbeafe;
          color: #1e40af;
        }

        .badge-status.perlu-perbaikan {
          background: #fee2e2;
          color: #991b1b;
        }

        /* ============================================
     FOOTER / TOTAL
     ============================================ */
        .table-custom tfoot tr {
          background: #f1f5f9;
          border-top: 2px solid #e2e8f0;
        }

        .table-custom tfoot td {
          padding: 12px 16px;
          font-weight: 700;
          color: #0f172a;
          font-size: 13px;
        }

        .table-custom tfoot .total-label {
          text-align: right;
        }

        /* ============================================
     PAGINATION
     ============================================ */
        .dataTables_wrapper .dataTables_paginate {
          float: right;
          margin-top: 16px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
          padding: 6px 14px;
          margin: 0 3px;
          border-radius: 8px;
          border: 1px solid #e2e8f0;
          background: white;
          color: #475569;
          font-size: 13px;
          cursor: pointer;
          transition: all 0.2s;
          display: inline-block;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
          background: #f1f5f9;
          border-color: #cbd5e1;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
          background: #0d6efd;
          border-color: #0d6efd;
          color: white;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
          background: #0b5ed7;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
          opacity: 0.5;
          cursor: not-allowed;
          background: #f8fafc;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
          background: #f8fafc;
        }

        /* ============================================
     INFO
     ============================================ */
        .dataTables_wrapper .dataTables_info {
          float: left;
          margin-top: 16px;
          font-size: 13px;
          color: #64748b;
        }

        /* ============================================
     RESPONSIVE
     ============================================ */
        @media (max-width: 768px) {

          .dataTables_wrapper .dataTables_length,
          .dataTables_wrapper .dataTables_filter {
            float: none;
            text-align: left;
            width: 100%;
          }

          .dataTables_wrapper .dataTables_filter input {
            width: 100%;
          }

          .dataTables_wrapper .dataTables_filter input:focus {
            width: 100%;
          }

          .dataTables_wrapper .dataTables_info,
          .dataTables_wrapper .dataTables_paginate {
            float: none;
            text-align: center;
            margin-top: 12px;
          }

          .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 4px 10px;
            font-size: 12px;
          }

          .badge-status {
            min-width: 70px;
            font-size: 10px;
            padding: 3px 10px;
          }

          .progress-custom .progress-bar-track {
            width: 50px;
          }
        }

        .card-header {
          background: white !important;
        }

        /* Untuk nama cabang yang panjang di chart */
        .chart-container {
          overflow-x: auto;
          position: relative;
        }

        .chart-container canvas {
          min-width: 100%;
        }

        /* Tooltip untuk nama cabang panjang di tabel */
        .cabang-name {
          max-width: 250px;
          display: inline-block;
          overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;
          vertical-align: middle;
        }

        .cabang-name:hover {
          overflow: visible;
          white-space: normal;
          word-break: break-word;
        }

        /* ============================================================
   CHART SCROLL WRAPPER — untuk bar chart horizontal scroll
   ============================================================ */
        .chart-scroll-wrapper {
          position: relative;
          width: 100%;
          height: 420px;
          overflow-x: auto;
          overflow-y: hidden;
          border: 1px solid #e5e7eb;
          border-radius: 8px;
          background: #fafbfc;
          /* Custom scrollbar */
          scrollbar-width: thin;
          scrollbar-color: #94a3b8 #f1f5f9;
        }

        .chart-scroll-wrapper::-webkit-scrollbar {
          height: 10px;
        }

        .chart-scroll-wrapper::-webkit-scrollbar-track {
          background: #f1f5f9;
          border-radius: 0 0 8px 8px;
        }

        .chart-scroll-wrapper::-webkit-scrollbar-thumb {
          background: #94a3b8;
          border-radius: 5px;
        }

        .chart-scroll-wrapper::-webkit-scrollbar-thumb:hover {
          background: #64748b;
        }

        .chart-scroll-inner {
          height: 400px;
          /* min-width di-set via JS berdasarkan jumlah cabang */
        }
      </style>

      <!-- Tabel Data Pengaduan per Cabang -->
      <div class="row mt-4">
        <div class="col-12">


          <!-- ============================================================ -->
          <!-- SECTION 1: BAR CHART — PERTUMBUHAN PER CABANG               -->
          <!-- ============================================================ -->
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
              <h5 class="fw-bold mb-1">
                <i class="bi bi-bar-chart-fill text-primary me-2"></i>
                Grafik Pertumbuhan Pro Bono
              </h5>
              <p class="text-muted mb-4" style="font-size:13px;">
                Perbandingan jumlah advokat, pengaduan masuk, dan pengaduan sukses per PBH Cabang
              </p>

              <!-- Container dengan overflow-x & scrollbar -->
              <div class="chart-scroll-wrapper">
                <div class="chart-scroll-inner" id="chartCabangWrapper">
                  <canvas id="chartPerCabang"></canvas>
                </div>
              </div>

              <!-- Scroll hint -->
              <div class="text-center mt-2">
                <small class="text-muted">
                  <i class="bi bi-arrows-move"></i> Geser chart ke kanan/kiri untuk lihat semua cabang
                </small>
              </div>

              <div class="mt-3 d-flex justify-content-center gap-4 flex-wrap">
                <div class="d-flex align-items-center gap-2">
                  <span style="background:#0d6efd; width:14px; height:14px; border-radius:3px; display:inline-block;"></span>
                  <small class="text-muted">Jumlah Advokat</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                  <span style="background:#ffc107; width:14px; height:14px; border-radius:3px; display:inline-block;"></span>
                  <small class="text-muted">Pengaduan Masuk</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                  <span style="background:#198754; width:14px; height:14px; border-radius:3px; display:inline-block;"></span>
                  <small class="text-muted">Pengaduan Sukses</small>
                </div>
              </div>
            </div>
          </div>




          <!-- ============================================================ -->
          <!-- SECTION 2: TABEL PER CABANG                                  -->
          <!-- ============================================================ -->
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                  <h5 class="fw-bold mb-0">
                    <i class="bi bi-table me-2"></i>
                    Detail Pertumbuhan Pro Bono
                  </h5>
                  <small class="text-muted"><?= count($dataPerCabang) ?> PBH Cabang terdaftar</small>
                </div>
                <span class="badge bg-primary" style="font-size:14px; padding:8px 16px;">
                  <?= count($dataPerCabang) ?> Cabang
                </span>
              </div>

              <!-- Filter + Search + Pagination -->
              <div class="row g-2 mb-3 align-items-center">
                <div class="col-md-3">
                  <label class="form-label mb-0" style="font-size:13px;">Tampilkan</label>
                  <select id="perPageCabang" class="form-select form-select-sm" style="width:auto; display:inline-block;">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                  </select>
                  <span style="font-size:13px;"> data</span>
                </div>
                <div class="col-md-4 offset-md-5">
                  <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchCabang" class="form-control" placeholder="Cari nama cabang...">
                  </div>
                </div>
              </div>

              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabelCabang">
                  <thead class="table-light">
                    <tr>
                      <th width="50">NO</th>
                      <th>NAMA PBH CABANG</th>
                      <th class="text-end">JUMLAH ADVOKAT</th>
                      <th class="text-end">PENGADUAN MASUK</th>
                      <th class="text-end">PENGADUAN SUKSES</th>
                      <th class="text-center">TINGKAT SUKSES</th>
                      <th class="text-center">STATUS</th>
                    </tr>
                  </thead>
                  <tbody id="tabelCabangBody">
                    <!-- Diisi oleh JS -->
                  </tbody>
                  <tfoot class="table-light fw-bold">
                    <tr>
                      <td colspan="2">TOTAL</td>
                      <td class="text-end"><?= number_format($totalAdvokat) ?></td>
                      <td class="text-end"><?= number_format($totalMasuk) ?></td>
                      <td class="text-end"><?= number_format($totalSukses) ?></td>
                      <td class="text-center"><?= $totalTingkatSukses ?>%</td>
                      <td class="text-center">-</td>
                    </tr>
                  </tfoot>
                </table>
              </div>

              <!-- Pagination -->
              <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted" id="infoCabang"></small>
                <nav>
                  <ul class="pagination pagination-sm mb-0" id="paginationCabang"></ul>
                </nav>
              </div>
            </div>
          </div>


        </div>
      </div>

      <!-- ============================================================ -->
      <!-- SECTION 3: PIE CHART + TABEL KATEGORI PERKARA (BERJENJANG)  -->
      <!-- ============================================================ -->
      <h4 class="fw-bold mb-3">
        <i class="bi bi-pie-chart-fill text-warning me-2"></i>
        Rekapitulasi Pro Bono Berdasarkan Kategori Perkara
      </h4>

      <div class="row g-3 mb-4">

        <!-- ============================================ -->
        <!-- PIE CHART                                    -->
        <!-- ============================================ -->
        <div class="col-md-5">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex flex-column justify-content-center">
              <div style="position: relative; height: 400px;">
                <canvas id="chartKategoriPerkara"></canvas>
              </div>
              <div class="text-center mt-3">
                <small class="text-muted">
                  <i class="bi bi-info-circle me-1"></i>
                  Total Pengaduan: <strong><?= array_sum(array_column($dataKategoriPerkara, 'total')) ?></strong>
                </small>
              </div>
            </div>
          </div>
        </div>

        <!-- ============================================ -->
        <!-- TABEL KATEGORI                                -->
        <!-- ============================================ -->
        <div class="col-md-7">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                  <h6 class="fw-bold mb-0">
                    <i class="bi bi-tags me-2"></i>
                    Sebaran Perkara Pro Bono
                  </h6>
                  <small class="text-muted">
                    <?= count($dataKategoriPerkara) ?> Kategori
                    (Litigasi + Non-Litigasi)
                  </small>
                </div>
                <span class="badge bg-warning text-dark" style="font-size:12px; padding:6px 12px;">
                  <?= count($dataKategoriPerkara) ?> Kategori
                </span>
              </div>

              <!-- Filter Tabel -->
              <div class="row g-2 mb-3 align-items-center">
                <div class="col-md-4">
                  <label class="form-label mb-0" style="font-size:13px;">Tampilkan</label>
                  <select id="perPageKategori" class="form-select form-select-sm" style="width:auto; display:inline-block;">
                    <option value="7" selected>7</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                  </select>
                  <span style="font-size:13px;"> data</span>
                </div>
                <div class="col-md-4 offset-md-4">
                  <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchKategori" class="form-control" placeholder="Cari kategori...">
                  </div>
                </div>
              </div>

              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabelKategori">
                  <thead class="table-light">
                    <tr>
                      <th width="50">No</th>
                      <th>Kategori Kasus</th>
                      <th class="text-end">Jumlah Kasus</th>
                      <th>Persentase</th>
                      <th class="text-center">Indikator</th>
                    </tr>
                  </thead>
                  <tbody id="tabelKategoriBody">
                    <!-- Diisi JS -->
                  </tbody>
                </table>
              </div>

              <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted" id="infoKategori"></small>
                <nav>
                  <ul class="pagination pagination-sm mb-0" id="paginationKategori"></ul>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>





      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <h6 class="fw-bold mb-3">Top 10 Advokat</h6>
              <div class="table-responsive" style="max-height:300px;">
                <table class="table table-sm mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Advokat</th>
                      <th>Cabang</th>
                      <th>Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($topAdvokat as $a): ?>
                      <tr>
                        <td style="font-size:12px;"><?= esc($a['nama_lengkap']) ?><br><small class="text-muted"><?= esc($a['nia']) ?></small></td>
                        <td style="font-size:11px;"><?= esc($a['cabang']) ?></td>
                        <td><span class="badge bg-success"><?= $a['total'] ?></span></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
        <div class="col-md-6">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Menunggu Verifikasi</h6>
                <a href="<?= base_url('admin/verifikasi') ?>" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
              </div>
              <div class="table-responsive">
                <table class="table table-sm table-hover">
                  <thead class="table-light">
                    <tr>
                      <th>No. Registrasi</th>
                      <th>Advokat</th>
                      <th>Cabang</th>
                      <th>Jenis</th>
                      <th>Submitted</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($pendingList)): ?>
                      <tr>
                        <td colspan="6" class="text-center text-muted py-4">Tidak ada yang menunggu.</td>
                      </tr>
                      <?php else: foreach ($pendingList as $p): ?>
                        <tr>
                          <td><code><?= esc($p['no_registrasi']) ?></code></td>
                          <td><?= esc($p['advokat_nama']) ?></td>
                          <td><?= esc($p['cabang_nama']) ?></td>
                          <td><?= esc($p['jenis_layanan']) ?></td>
                          <td><?= date('d M Y H:i', strtotime($p['submitted_at'])) ?></td>
                          <td><a href="<?= base_url('admin/verifikasi/' . $p['id']) ?>" class="btn btn-sm btn-primary">Verifikasi</a></td>
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



      <?= $this->endSection() ?>

      <?= $this->section('scripts') ?>
      <script>
        (function() {
          window.addEventListener('load', function() {
            if (typeof Chart === 'undefined') {
              console.error('❌ Chart.js tidak ter-load');
              return;
            }

            // ============================================================
            // DATA DARI PHP
            // ============================================================
            const dataCabang = <?= json_encode($dataPerCabang ?? []) ?>;
            const dataKategori = <?= json_encode($dataKategoriPerkara ?? []) ?>;

            // ============================================================
            // 1. BAR CHART — PER CABANG (HORIZONTAL SCROLL)
            // ============================================================
            const elCabang = document.getElementById('chartPerCabang');
            const wrapperCabang = document.getElementById('chartCabangWrapper');

            if (elCabang && wrapperCabang && dataCabang.length > 0) {
              // Hitung lebar minimum berdasarkan jumlah cabang
              // 40px per cabang, minimal 1000px
              const minWidth = Math.max(dataCabang.length * 40, 1000);
              wrapperCabang.style.minWidth = minWidth + 'px';

              new Chart(elCabang, {
                type: 'bar',
                data: {
                  labels: dataCabang.map(c => c.nama),
                  datasets: [{
                      label: 'Jumlah Advokat',
                      data: dataCabang.map(c => c.total_advokat),
                      backgroundColor: '#0d6efd',
                      borderRadius: 3
                    },
                    {
                      label: 'Pengaduan Masuk',
                      data: dataCabang.map(c => c.total_masuk),
                      backgroundColor: '#ffc107',
                      borderRadius: 3
                    },
                    {
                      label: 'Pengaduan Sukses',
                      data: dataCabang.map(c => c.total_sukses),
                      backgroundColor: '#198754',
                      borderRadius: 3
                    }
                  ]
                },
                options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  resizeDelay: 0,
                  plugins: {
                    legend: {
                      display: false
                    },
                    tooltip: {
                      mode: 'index',
                      intersect: false,
                      callbacks: {
                        title: function(ctx) {
                          return ctx[0].label;
                        }
                      }
                    }
                  },
                  scales: {
                    x: {
                      ticks: {
                        font: {
                          size: 10
                        },
                        maxRotation: 60,
                        minRotation: 60,
                        autoSkip: false
                      },
                      grid: {
                        display: false
                      }
                    },
                    y: {
                      beginAtZero: true,
                      title: {
                        display: true,
                        text: 'Jumlah',
                        font: {
                          size: 12
                        }
                      },
                      ticks: {
                        precision: 0
                      }
                    }
                  },
                  interaction: {
                    mode: 'index',
                    intersect: false
                  },
                  animation: {
                    duration: 600
                  }
                }
              });
            }

            // ============================================================
            // 2. TABEL PER CABANG + PAGINATION + SEARCH
            // ============================================================
            let currentPageCabang = 1;
            let perPageCabang = 10;
            let filteredCabang = dataCabang.slice();

            function renderTabelCabang() {
              const start = (currentPageCabang - 1) * perPageCabang;
              const end = start + perPageCabang;
              const pageData = filteredCabang.slice(start, end);

              let html = '';
              if (pageData.length === 0) {
                html = '<tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data.</td></tr>';
              } else {
                pageData.forEach((c, i) => {
                  const no = start + i + 1;
                  html += '<tr>';
                  html += '<td>' + no + '</td>';
                  html += '<td>' + c.nama + '</td>';
                  html += '<td class="text-end">' + c.total_advokat + '</td>';
                  html += '<td class="text-end">' + c.total_masuk + '</td>';
                  html += '<td class="text-end">' + c.total_sukses + '</td>';
                  html += '<td class="text-center">';
                  html += '<div class="progress" style="height:6px; width:100px; display:inline-block; vertical-align:middle; margin-right:8px;">';
                  html += '<div class="progress-bar bg-success" style="width:' + c.tingkat_sukses + '%;"></div>';
                  html += '</div>';
                  html += '<strong>' + c.tingkat_sukses + '%</strong>';
                  html += '</td>';
                  html += '<td class="text-center"><span class="badge bg-' + c.status_badge + '">' + c.status + '</span></td>';
                  html += '</tr>';
                });
              }
              document.getElementById('tabelCabangBody').innerHTML = html;

              document.getElementById('infoCabang').textContent =
                'Menampilkan ' + (start + 1) + ' sampai ' + Math.min(end, filteredCabang.length) +
                ' dari ' + filteredCabang.length + ' data';

              renderPagination('paginationCabang', filteredCabang.length, perPageCabang, currentPageCabang, function(page) {
                currentPageCabang = page;
                renderTabelCabang();
              });
            }

            // ============================================================
            // 3. TABEL KATEGORI PERKARA — BERJENJANG
            // ============================================================
            let currentPageKategori = 1;
            let perPageKategori = 7;
            let filteredKategori = dataKategori.slice();

            function renderTabelKategori() {
              const start = (currentPageKategori - 1) * perPageKategori;
              const end = start + perPageKategori;
              const pageData = filteredKategori.slice(start, end);

              let html = '';
              if (pageData.length === 0) {
                html = '<tr><td colspan="5" class="text-center text-muted py-4">Tidak ada data.</td></tr>';
              } else {
                let lastKategori = null;
                let nomor = start + 1;

                pageData.forEach(function(k) {
                  if (k.kategori !== lastKategori) {
                    const isLitigasi = k.kategori === 'litigasi';
                    const bgColor = isLitigasi ? '#eff6ff' : '#ecfdf5';
                    const textColor = isLitigasi ? '#1e40af' : '#065f46';
                    const icon = isLitigasi ? '⚖️' : '🤝';
                    const label = isLitigasi ? 'LITIGASI' : 'NON-LITIGASI';

                    html += '<tr style="background:' + bgColor + ';">';
                    html += '<td colspan="5" style="font-weight:700; color:' + textColor + '; font-size:12px; letter-spacing:0.5px; padding:8px 12px;">';
                    html += icon + ' ' + label;
                    html += '</td>';
                    html += '</tr>';

                    lastKategori = k.kategori;
                  }

                  html += '<tr>';
                  html += '<td>' + nomor + '</td>';
                  html += '<td>';
                  html += '<span style="background:' + k.warna + '; width:14px; height:14px; border-radius:3px; display:inline-block; margin-right:8px; vertical-align:middle;"></span>';
                  html += k.label;
                  html += '</td>';
                  html += '<td class="text-end">' + k.total + '</td>';
                  html += '<td>';
                  html += '<div class="progress" style="height:6px; width:80px; display:inline-block; vertical-align:middle; margin-right:8px;">';
                  html += '<div class="progress-bar" style="width:' + k.persentase + '%; background:' + k.warna + ';"></div>';
                  html += '</div>';
                  html += '<strong>' + k.persentase + '%</strong>';
                  html += '</td>';
                  html += '<td class="text-center"><span class="badge bg-' + k.indikator_badge + '">' + k.indikator + '</span></td>';
                  html += '</tr>';

                  nomor++;
                });
              }

              document.getElementById('tabelKategoriBody').innerHTML = html;

              document.getElementById('infoKategori').textContent =
                'Menampilkan ' + (start + 1) + ' sampai ' + Math.min(end, filteredKategori.length) +
                ' dari ' + filteredKategori.length + ' data';

              renderPagination('paginationKategori', filteredKategori.length, perPageKategori, currentPageKategori, function(page) {
                currentPageKategori = page;
                renderTabelKategori();
              });
            }

            // ============================================================
            // 4. PIE CHART — KATEGORI PERKARA BERJENJANG
            // ============================================================
            const elKategori = document.getElementById('chartKategoriPerkara');
            if (elKategori && dataKategori.length > 0) {
              new Chart(elKategori, {
                type: 'pie',
                data: {
                  labels: dataKategori.map(k => k.label),
                  datasets: [{
                    data: dataKategori.map(k => k.total),
                    backgroundColor: dataKategori.map(k => k.warna),
                    borderColor: '#fff',
                    borderWidth: 2,
                    hoverOffset: 10
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
                        padding: 10,
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
                          const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                          const pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                          return ' ' + ctx.label + ': ' + ctx.parsed + ' (' + pct + '%)';
                        }
                      }
                    }
                  }
                }
              });
            }

            // ============================================================
            // 5. EVENT LISTENER: SEARCH & FILTER
            // ============================================================
            const searchCabang = document.getElementById('searchCabang');
            if (searchCabang) {
              searchCabang.addEventListener('input', function() {
                const q = this.value.toLowerCase();
                filteredCabang = dataCabang.filter(c => c.nama.toLowerCase().includes(q));
                currentPageCabang = 1;
                renderTabelCabang();
              });
            }

            const perPageCabangEl = document.getElementById('perPageCabang');
            if (perPageCabangEl) {
              perPageCabangEl.addEventListener('change', function() {
                perPageCabang = parseInt(this.value);
                currentPageCabang = 1;
                renderTabelCabang();
              });
            }

            const searchKategori = document.getElementById('searchKategori');
            if (searchKategori) {
              searchKategori.addEventListener('input', function() {
                const q = this.value.toLowerCase();
                filteredKategori = dataKategori.filter(k => k.label.toLowerCase().includes(q));
                currentPageKategori = 1;
                renderTabelKategori();
              });
            }

            const perPageKategoriEl = document.getElementById('perPageKategori');
            if (perPageKategoriEl) {
              perPageKategoriEl.addEventListener('change', function() {
                perPageKategori = parseInt(this.value);
                currentPageKategori = 1;
                renderTabelKategori();
              });
            }

            // ============================================================
            // 6. HELPER: PAGINATION
            // ============================================================
            function renderPagination(elementId, totalItems, perPage, currentPage, onPageClick) {
              const totalPages = Math.ceil(totalItems / perPage);
              const el = document.getElementById(elementId);
              if (!el) return;

              let html = '';

              html += '<li class="page-item ' + (currentPage === 1 ? 'disabled' : '') + '">';
              html += '<a class="page-link" href="#" data-page="' + (currentPage - 1) + '">Sebelumnya</a>';
              html += '</li>';

              let start = Math.max(1, currentPage - 2);
              let end = Math.min(totalPages, currentPage + 2);

              for (let i = start; i <= end; i++) {
                html += '<li class="page-item ' + (i === currentPage ? 'active' : '') + '">';
                html += '<a class="page-link" href="#" data-page="' + i + '">' + i + '</a>';
                html += '</li>';
              }

              html += '<li class="page-item ' + (currentPage === totalPages ? 'disabled' : '') + '">';
              html += '<a class="page-link" href="#" data-page="' + (currentPage + 1) + '">Berikutnya</a>';
              html += '</li>';

              el.innerHTML = html;

              el.querySelectorAll('a.page-link').forEach(a => {
                a.addEventListener('click', function(e) {
                  e.preventDefault();
                  const page = parseInt(this.dataset.page);
                  if (page >= 1 && page <= totalPages && page !== currentPage) {
                    onPageClick(page);
                  }
                });
              });
            }

            // ============================================================
            // 7. RENDER AWAL
            // ============================================================
            renderTabelCabang();
            renderTabelKategori();

            console.log('✅ Dashboard admin berhasil dirender');
          });
        })();
      </script>
      <?= $this->endSection() ?>