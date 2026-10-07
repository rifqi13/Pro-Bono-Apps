<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>



<main class="main">

  <!-- Page Title -->
  <div class="page-title light-background">
    <div class="container">
      <h1>Lupa Password</h1>
      <nav class="breadcrumbs">
        <ol>
          <li><a href="<?= base_url(); ?>">Home</a></li>
          <li class="current">Lupa Password</li>
        </ol>
      </nav>
    </div>
  </div><!-- End Page Title -->

  <!-- Section: Design Block -->
  <section class="contact background-radial-gradient overflow-hidden d-flex align-items-center justify-content-center">
    
    <!-- Style Background Gradasi Biru (Tetap Dipertahankan) -->
    <style>
      .background-radial-gradient {
        background-color: #f3f9ff;
        background-image: linear-gradient(
          180deg,
          hsl(210deg 100% 98%) 0%,
          hsl(211deg 100% 80%) 10%,
          hsl(212deg 100% 62%) 30%,
          hsl(213deg 100% 55%) 55%,
          hsl(212deg 100% 72%) 74%,
          hsl(210deg 100% 88%) 86%,
          hsl(210deg 100% 95%) 94%,
          hsl(210deg 100% 96%) 98%,
          hsl(210deg 100% 98%) 100%
        );
      }

      #radius-shape-1 {
        height: 220px;
        width: 220px;
        top: -60px;
        left: -130px;
        background: radial-gradient(#0c04f3, #08fff3);
        overflow: hidden;
        opacity: 0.6;
      }

      #radius-shape-2 {
        border-radius: 38% 62% 63% 37% / 70% 33% 67% 30%;
        bottom: -60px;
        right: -110px;
        width: 300px;
        height: 300px;
        background: radial-gradient(#001eff, #13EBFE);
        overflow: hidden;
        opacity: 0.5;
      }

      .bg-glass {
        background-color: hsla(0, 0%, 100%, 0.95) !important;
        backdrop-filter: saturate(200%) blur(25px);
        border-radius: 12px;
      }
    </style>

    <div class="container px-4 py-md-5 my-5 position-relative" style="z-index: 10;margin-top:0 !important;padding-top:0 !important;">
      <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7 col-sm-10 position-relative">

          <!-- Ornamen Lingkaran -->
          <div id="radius-shape-1" class="position-absolute rounded-circle shadow-5-strong"></div>
          <div id="radius-shape-2" class="position-absolute shadow-5-strong"></div>

          <!-- Kartu Form -->
          <div class="card bg-glass border-0 shadow-lg">
            <div class="card-body p-4 p-md-5">

              

              <form action="<?= base_url('auth/forgot') ?>" method="post" novalidate>
              <?= csrf_field() ?>

                <!-- Email -->
                <div class="mb-3">
                  <label class="form-label">Alamat Email <span class="text-danger">*</span></label>
                  <input type="email" name="email" class="form-control" required autofocus value="<?= esc(old('email')) ?>">
                </div>


                <!-- Tombol Login -->
                <button type="submit" class="btn btn-primary  btn-lg w-100 mb-4">Kirim Link Reset</button>

                <!-- Link Daftar -->
                <div class="text-center small">
                   <a href="<?= base_url('auth/login') ?>">← Kembali ke Login</a>
                </div>

              </form>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>

</main>



<?= $this->endSection() ?>