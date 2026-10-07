<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?= esc($title ?? 'Dashboard') ?> - PERADI Pro Bono</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="<?php echo base_url('assets/img/favicon.png'); ?>" rel="icon">
  <link href="<?php echo base_url('assets/img/apple-touch-icon.png'); ?>" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="<?php echo base_url('assets/vendor/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets/vendor/bootstrap-icons/bootstrap-icons.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets/vendor/aos/aos.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets/vendor/glightbox/css/glightbox.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets/vendor/swiper/swiper-bundle.min.css'); ?>" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="<?php echo base_url('assets/css/main.css'); ?>" rel="stylesheet">

  <!-- =======================================================
  * Template Name: iLanding
  * Template URL: https://bootstrapmade.com/ilanding-bootstrap-landing-page-template/
  * Updated: Nov 12 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
  <?= $this->renderSection('styles') ?>
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

    .list-angka {
      list-style: none;
      /* Menghilangkan bullet point default */
      padding-left: 0;
      display: grid;
      grid-template-columns: 1fr 1fr;
      /* Membuat 2 kolom seperti di gambar */
      gap: 15px;
      /* Jarak antar item */
    }

    .list-angka li {
      display: flex;
      align-items: center;
      gap: 10px;
      /* Jarak antara angka dan teks */
      font-family: sans-serif;
      color: #333;
    }

    /* Membuat lingkaran angka */
    .list-angka li::before {
      counter-increment: list-counter;
      /* Menambah angka otomatis */
      content: counter(list-counter);
      /* Menampilkan angka */
      display: flex;
      justify-content: center;
      align-items: center;
      width: 24px;
      height: 24px;
      background-color: #007bff;
      /* Warna biru seperti gambar */
      color: white;
      border-radius: 50%;
      /* Membuat bentuk lingkaran */
      font-size: 12px;
      font-weight: bold;
    }

    /* Inisialisasi counter di parent */
    .list-angka {
      counter-reset: list-counter;
    }

    .about .image-wrapper .small-image {
      position: absolute;
      top: 40%;
      left: -5%;
      width: 50%;
      border: 8px solid #007bff;
    }

    /* --- Container Utama Kiri --- */
    .konten-kiri {
      font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      color: #333;
      /* Batasi lebar agar tidak terlalu panjang */
    }

    /* =========================================
   BAGIAN 1: DAFTAR ANGKA BIRU (2 KOLOM)
   ========================================= */
    .list-angka {
      list-style: none;
      /* Hilangkan bullet default */
      padding: 0;
      margin: 0 0 40px 0;
      /* Jarak ke bagian bawah */
      display: grid;
      grid-template-columns: 1fr 1fr;
      /* Bagi jadi 2 kolom sama rata */
      gap: 15px 30px;
      /* Jarak antar item (baris 15px, kolom 30px) */
      counter-reset: list-counter;
    }

    .list-angka li {
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 15px;
      color: #444;
      line-height: 1.4;
    }

    /* Membuat Lingkaran Angka Biru */
    .list-angka li::before {
      counter-increment: list-counter;
      content: counter(list-counter);
      display: flex;
      justify-content: center;
      align-items: center;
      min-width: 24px;
      /* Lebar lingkaran */
      height: 24px;
      background-color: #007bff;
      /* Warna biru */
      color: white;
      border-radius: 50%;
      font-size: 12px;
      font-weight: bold;
    }

    /* =========================================
   BAGIAN 2: ALUR 3 LANGKAH
   ========================================= */
    .alur-langkah {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      /* Bagi jadi 3 kolom sama rata */
      gap: 25px;
      /* Jarak antar langkah */
      margin-bottom: 40px;
      /* Jarak ke tombol */
    }

    .langkah-item {
      display: flex;
      flex-direction: column;
    }

    /* Angka Besar (1, 2, 3) */
    .angka-langkah {
      font-size: 24px;
      font-weight: 600;
      color: #333;
      margin-bottom: 8px;
      display: block;
    }

    /* Judul Langkah */
    .langkah-item h3 {
      font-size: 18px;
      font-weight: 600;
      color: #2c3e50;
      margin: 0 0 10px 0;
      line-height: 1.3;
    }

    /* Deskripsi Langkah */
    .langkah-item p {
      font-size: 14px;
      color: #555;
      line-height: 1.6;
      margin: 0;
    }

    /* =========================================
   BAGIAN 3: TOMBOL BIRU
   ========================================= */
    .btn-biru {
      display: inline-block;
      background-color: #007bff;
      color: white;
      text-decoration: none;
      padding: 12px 24px;
      border-radius: 6px;
      font-size: 15px;
      font-weight: 500;
      transition: background 0.3s ease;
    }

    .btn-biru:hover {
      background-color: #0056b3;
      /* Warna biru lebih gelap saat di-hover */
    }

    @media (max-width: 768px) {
      .list-angka {
        grid-template-columns: 1fr;
        /* Jadi 1 kolom */
      }

      .alur-langkah {
        grid-template-columns: 1fr;
        /* Jadi 1 kolom vertikal */
      }
    }

    /* --- Container Utama --- */
    .section-kartu {
      margin-left: 20px;
      /* Memberi jarak dari sisi kiri */
      margin-right: 20px;
      /* Memberi jarak dari sisi kanan */
    }

    /* --- Grid Layout untuk 4 Kartu --- */
    .grid-kartu {
      display: grid;
      /* Membuat 4 kolom yang sama rata */
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
      /* Jarak antar kartu */
    }

    /* --- Style Dasar Kartu --- */
    .kartu {
      padding: 30px 24px;
      border-radius: 12px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      display: flex;
      flex-direction: column;
    }

    /* Efek Hover (Opsional: Kartu sedikit naik saat diarahkan mouse) */
    .kartu:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
    }

    /* --- Ikon --- */
    .kartu .ikon {
      margin-bottom: 20px;
      display: flex;
      align-items: center;
    }

    .kartu .ikon svg {
      width: 36px;
      height: 36px;
    }

    /* --- Judul --- */
    .kartu h3 {
      font-size: 18px;
      font-weight: 700;
      color: #2c3e50;
      margin: 0 0 12px 0;
      line-height: 1.4;
    }

    /* --- Deskripsi --- */
    .kartu p {
      font-size: 14px;
      color: #555;
      line-height: 1.6;
      margin: 0;
    }

    /* =========================================
   VARIAN WARNA (Pastel & Ikon)
   ========================================= */

    /* 1. Oranye */
    .kartu-oranye {
      background-color: #FEF9E7;
      /* Background pastel oranye */
    }

    .kartu-oranye .ikon svg {
      stroke: #F39C12;
      /* Warna ikon oranye */
    }

    /* 2. Biru */
    .kartu-biru {
      background-color: #EBF5FB;
      /* Background pastel biru */
    }

    .kartu-biru .ikon svg {
      stroke: #3498DB;
      /* Warna ikon biru */
    }

    /* 3. Hijau */
    .kartu-hijau {
      background-color: #E8F8F5;
      /* Background pastel hijau */
    }

    .kartu-hijau .ikon svg {
      stroke: #1ABC9C;
      /* Warna ikon hijau */
    }

    /* 4. Merah Muda */
    .kartu-merah {
      background-color: #FDEDEC;
      /* Background pastel merah */
    }

    .kartu-merah .ikon svg {
      stroke: #E74C3C;
      /* Warna ikon merah */
    }

    /* =========================================
   RESPONSIF (Agar rapi di HP)
   ========================================= */
    @media (max-width: 1024px) {
      .grid-kartu {
        grid-template-columns: repeat(2, 1fr);
        /* Jadi 2 kolom di tablet */
      }
    }

    @media (max-width: 600px) {
      .section-kartu {
        margin-left: 0;
        /* Hilangkan margin kiri di HP */
      }

      .grid-kartu {
        grid-template-columns: 1fr;
        /* Jadi 1 kolom di HP */
      }
    }
  </style>
</head>


<body class="index-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="header-container container-fluid container-xl py-4 position-relative d-flex align-items-center justify-content-between flex-nowrap">

      <a href="<?= base_url(); ?>" class="logo d-flex align-items-center me-auto me-xl-0 flex-shrink-0">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <!-- <img src="<?php echo base_url('assets/img/logo.png'); ?>" alt=""> -->
        <img src="<?php echo base_url('assets/img/logo.png'); ?>" alt="Logo" class="logo-image">
      </a>


      <!-- Bagian Kanan - Logo Kanan + Dropdown User -->
      <div class="d-flex align-items-center gap-2 gap-sm-3 flex-shrink-0">
        <!-- Logo Kanan -->
        <a href="<?= base_url(); ?>" class="logo d-flex align-items-center">
          <img src="<?php echo base_url('assets/img/Peradi_logo_v3.png'); ?>" alt="Logo Kanan" style="height: 40px;">
        </a>

        

        <!-- ================= DROPDOWN NOTIFIKASI ================= -->
        <div class="dropdown">
          <a class="btn btn-primary rounded-pill d-flex align-items-center justify-content-center position-relative dropdown-toggle-no-caret"
            href="#" role="button" id="dropdownNotif" data-bs-toggle="dropdown" aria-expanded="false"
            style="width: 45px; height: 45px;">
            <i class="bi bi-bell-fill fs-5"></i>

            <!-- Badge Notifikasi (Dinamis dari Sistem Anda) -->
            <?php if (unread_notif_count() > 0): ?>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light"
                style="font-size: 0.6rem; padding: 0.35em 0.5em;">
                <?= unread_notif_count() > 9 ? '9+' : unread_notif_count() ?>
              </span>
            <?php endif; ?>
          </a>

          <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-2"
            aria-labelledby="dropdownNotif"
            style="border-radius: 12px; min-width: 340px; max-height: 420px; overflow-y: auto;">

            <!-- Header Notifikasi + Tombol Tandai Semua Dibaca -->
            <li class="px-3 py-2 border-bottom mb-2 d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-2">
                <span class="fw-bold text-dark">Notifikasi</span>
                <?php if (unread_notif_count() > 0): ?>
                  <span class="badge bg-primary rounded-pill" style="font-size: 0.65rem;">
                    <?= unread_notif_count() ?> Baru
                  </span>
                <?php endif; ?>
              </div>
              <form action="<?= base_url('notifications/mark-all-read') ?>" method="post" class="m-0">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-link btn-sm p-0 text-decoration-none" style="font-size: 11px;">
                  Tandai semua dibaca
                </button>
              </form>
            </li>

            <!-- Loop Notifikasi dari Sistem Anda -->
            <?php $notifs = recent_notifs(5); ?>

            <?php if (empty($notifs)): ?>
              <!-- Empty State -->
              <li class="px-3 py-4 text-center text-muted">
                <i class="bi bi-bell-slash fs-3 d-block mb-2 opacity-50"></i>
                <div style="font-size: 13px;">Tidak ada notifikasi</div>
              </li>
            <?php else: ?>
              <?php foreach ($notifs as $n): ?>
                <li>
                  <a class="dropdown-item py-2 d-flex align-items-start gap-2 rounded-2 notif-link"
                    href="<?= esc($n['link'] ?? '#') ?>"
                    data-notif-id="<?= esc($n['id']) ?>"
                    style="white-space: normal;">

                    <!-- Ikon dengan Background Pastel (Warna mengikuti $n['type']) -->
                    <div class="bg-<?= esc($n['type']) ?> bg-opacity-10 p-2 rounded-circle d-flex align-items-center justify-content-center"
                      style="width: 36px; height: 36px; flex-shrink: 0;">
                      <i class="bi <?= esc($n['icon']) ?> text-<?= esc($n['type']) ?>"></i>
                    </div>

                    <!-- Konten Notifikasi -->
                    <div class="flex-grow-1">
                      <div class="fw-semibold text-dark" style="font-size: 13px;">
                        <?= esc($n['title']) ?>
                      </div>
                      <div class="text-muted" style="font-size: 12px; line-height: 1.4;">
                        <?= esc($n['message']) ?>
                      </div>
                      <div class="text-muted mt-1" style="font-size: 11px;">
                        <i class="bi bi-clock me-1"></i>
                        <?= date('d M H:i', strtotime($n['created_at'])) ?>
                      </div>
                    </div>
                  </a>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>

            <!-- Footer: Lihat Semua Notifikasi -->
            <li class="border-top mt-1">
              <a class="dropdown-item text-center py-2 text-primary fw-semibold rounded-2"
                style="font-size: 12px;"
                href="<?= base_url('notifications') ?>">
                Lihat Semua Notifikasi
              </a>
            </li>
          </ul>
        </div>

        <!-- ================= DROPDOWN USER ================= -->
        <div class="dropdown ms-2">
          <a class="btn btn-primary rounded-pill d-flex align-items-center justify-content-center dropdown-toggle-no-caret"
            href="#" role="button" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false"
            style="width: 45px; height: 45px;">
            <i class="bi bi-person-fill fs-5"></i>
          </a>

          <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-2" aria-labelledby="dropdownUser" style="border-radius: 12px; min-width: 220px;">
            <!-- Header: Nama Pengguna -->
            <li class="px-3 py-2 border-bottom mb-2">
              <div class="fw-bold text-dark"><?= esc(session()->get('nama')) ?></div>
              <div class="text-muted small"><?= esc(session()->get('email')) ?></div>
            </li>

            <!-- Menu Profil -->
            <li>
              <a class="dropdown-item py-2 d-flex align-items-center gap-2 rounded-2" href="#">
                <i class="bi bi-person text-primary"></i>
                <span>Profil Saya</span>
              </a>
            </li>

            

            <!-- Divider -->
            <li>
              <hr class="dropdown-divider my-1">
            </li>

            <!-- Menu Keluar -->
            <li>
              <a class="dropdown-item py-2 d-flex align-items-center gap-2 rounded-2 text-danger" href="<?= base_url('auth/logout') ?>">
                <i class="bi bi-box-arrow-right"></i>
                <span>Keluar</span>
              </a>
            </li>
          </ul>
        </div>



        <style>
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

          /* Menghilangkan panah default pada tombol dropdown */
          .dropdown-toggle-no-caret::after {
            display: none !important;
          }

          /* Style Tombol Pemicu (Trigger) */
          .btn-getstarted-trigger {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50% !important;
            /* Bulat sempurna */
            background-color: #0d6efd;
            /* Warna biru Bootstrap */
            color: white;
            border: none;
            transition: all 0.3s ease;
          }

          .btn-getstarted-trigger:hover {
            background-color: #0b5ed7;
            transform: scale(1.05);
            /* Efek zoom saat hover */
          }

          /* Style Dropdown Menu */
          .dropdown-menu {
            animation: fadeIn 0.2s ease-in-out;
          }

          /* Animasi Fade In */
          @keyframes fadeIn {
            from {
              opacity: 0;
              transform: translateY(-10px);
            }

            to {
              opacity: 1;
              transform: translateY(0);
            }
          }

          /* Style Item Dropdown */
          .dropdown-item {
            transition: background-color 0.2s ease;
          }

          .dropdown-item:hover {
            background-color: #f8f9fa;
          }

          /* Style untuk item yang aktif/terpilih */
          .dropdown-item.active,
          .dropdown-item:active {
            background-color: #0d6efd;
            color: white;
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
        </style>
      </div>
  </header>

  <main class="main">
    

    <?= $this->renderSection('content') ?>

  </main>

  <footer id="footer" class="footer">
    <div class="container text-center mt-4">

      <div class="address-content mb-3">
        <p class="mb-0">
          PERADI TOWER<br>
          Jl. Jend. Achmad Yani No.116, Jakarta Timur 13120
        </p>
      </div>

      <div class="social-links d-flex justify-content-center mt-3 mb-4">
        <a href="https://www.facebook.com/DPNPERADI"><i class="bi bi-facebook"></i></a>
        <a href="https://x.com/dpn_peradi"><i class="bi bi-twitter-x"></i></a>
        <a href="https://www.instagram.com/dpnperadi/"><i class="bi bi-instagram"></i></a>
        <a href="https://www.youtube.com/channel/UCadqocjnX4N7rg7PUh6PN7g"><i class="bi bi-youtube"></i></a>
        <a href="https://www.tiktok.com/@dpnperadi"><i class="bi bi-tiktok"></i></a>
      </div>

      <div class="copyright border-top pt-4">
        <p><span>Copyright</span> © <strong class="px-1 sitename">DPN PERADI</strong> <span>All Rights Reserved</span></p>
      </div>

    </div>
  </footer>
  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="<?php echo base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets/vendor/php-email-form/validate.js'); ?>"></script>
  <script src="<?php echo base_url('assets/vendor/aos/aos.js'); ?>"></script>
  <script src="<?php echo base_url('assets/vendor/glightbox/js/glightbox.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets/vendor/swiper/swiper-bundle.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets/vendor/purecounter/purecounter_vanilla.js'); ?>"></script>

  <!-- Main JS File -->
  <script src="<?php echo base_url('assets/js/main.js'); ?>"></script>


  <script>
    AOS.init();
  </script>


  <?= $this->renderSection('scripts') ?>
</body>

</html>