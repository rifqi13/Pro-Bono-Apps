<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>

<main class="main">

  <!-- Page Title -->
  <div class="page-title light-background">
    <div class="container">
      <h1>Registrasi Akun</h1>
      <nav class="breadcrumbs">
        <ol>
          <li><a href="<?= base_url() ?>">Home</a></li>
          <li class="current">Registrasi Akun</li>
        </ol>
      </nav>
    </div>
  </div><!-- End Page Title -->

  <!-- Contact Section -->
  <section id="contact" class="contact section light-background">


    <div class="container" data-aos="fade-up" data-aos-delay="100">

      <div class="row g-4 g-lg-5">

        <div class="col-lg-12">
          <div class="contact-form" data-aos="fade-up" data-aos-delay="300">


            <form action="<?= base_url('auth/register') ?>" class="php-email-form" method="post" novalidate>
              <?= csrf_field() ?>

              <div class="row justify-content-between text-left">
                <div class="form-group col-sm-6 flex-column d-flex">
                  <label class="form-control-label px-3">Nomor Induk Advokat (NIA)<span class="text-danger"> *</span></label>
                  <input type="text" id="nia" name="nia" class="form-control" required
                    value="<?= esc(old('nia')) ?>" placeholder="Contoh: xx.xxxxx" maxlength="8" autocomplete="off">
                  <small class="text-muted">Format: 2 digit.5 digit (contoh: xx.xxxxx)</small>
                </div>

                <div class="form-group col-sm-6 flex-column d-flex">
                  <label class="form-control-label px-3">Pusat Bantuan Hukum Cabang<span class="text-danger"> *</span></label>

                  <select name="pbh_cabang_id" class="form-select" required>
                    <option value="" disabled selected>-- Pilih PBH Cabang --</option>
                    <?php foreach ($cabang_list as $c): ?>
                      <option value="<?= $c['id'] ?>" <?= old('pbh_cabang_id') == $c['id'] ? 'selected' : '' ?>>
                        <?= esc($c['nama']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="row justify-content-between text-left">
                <div class="form-group col-sm-12 flex-column d-flex">
                  <label class="form-control-label px-3">Nama Lengkap & Gelar<span class="text-danger"> *</span></label>
                  <input type="text" id="email" name="email" placeholder="" onblur="validate(3)">
                </div>
              </div>
              <div class="row justify-content-between text-left">
                <div class="form-group col-sm-12 flex-column d-flex">
                  <label class="form-control-label px-3">Alamat Email<span class="text-danger"> *</span></label>
                  <input type="email" id="mob" name="mob" placeholder="" onblur="validate(4)">
                </div>
              </div>
              <div class="row justify-content-between text-left">
                <div class="form-group col-sm-12 flex-column d-flex">
                  <label class="form-control-label px-3">Nomor WhatsApp Aktif<span class="text-danger"> *</span></label>
                  <input type="text" id="job" name="job" placeholder="" onblur="validate(5)">
                </div>
              </div>

              <div class="row justify-content-between text-left">
                <div class="form-group col-sm-12 flex-column d-flex">
                  <label class="form-control-label px-3">Password<span class="text-danger"> *</span></label>
                  <input type="password" name="password" class="form-control" required minlength="8">
                  <small class="text-muted">Minimal 8 karakter, kombinasi huruf dan angka</small>
                </div>
              </div>

              <div class="row justify-content-between text-left">
                <div class="form-group col-sm-12 flex-column d-flex">
                  <label class="form-control-label px-3">Konfirmasi Password<span class="text-danger"> *</span></label>
                  <input type="password" name="confirm" class="form-control" required minlength="8">
                </div>
              </div>

              <div class="row gy-4">
                <div class="col-12 text-center">
                  <button type="submit" class="btn mt-4">Daftar Akun</button>
                </div>
              </div>

            </form>

            <div class="col-md-12 mt-4">
                                <p class="text-left text-dark">
                                    <span>Sudah Punya Akun?</span>
                                    <a href="<?= base_url('auth/login') ?>">
                                        <span>Login</span>
                                    </a><br>
                                    <span>Atau Kembali ke Halaman</span>
                                    <a href="<?= base_url() ?>">
                                        <span>Beranda</span>
                                    </a>
                                </p>
                            </div>
          </div>

          
        </div>

      </div>

    </div>

  </section><!-- /Contact Section -->


</main>

<script>
    // ===================== VALIDASI NIA =====================
    document.getElementById('nia').addEventListener('input', function(e) {
      let value = this.value.replace(/\D/g, '');

      if (value.length > 2) {
        value = value.slice(0, 2) + '.' + value.slice(2);
      }
      if (value.length > 8) {
        value = value.slice(0, 8);
      }

      this.value = value;

      const niaPattern = /^[0-9]{2}\.[0-9]{5}$/;
      if (value.length === 8 && !niaPattern.test(value)) {
        this.classList.add('error');
        this.classList.remove('success');
      } else if (value.length === 8 && niaPattern.test(value)) {
        this.classList.remove('error');
        this.classList.add('success');
      } else {
        this.classList.remove('error', 'success');
      }

      validateForm();
    });
  </script>
 <?= $this->renderSection('scripts') ?>

<?= $this->endSection() ?>