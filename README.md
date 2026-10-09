Pencatatan Pro Bono PERADI
==========================

Sistem Pendataan dan Verifikasi Bantuan Hukum Pro Bono PERADI.

Fitur Utama
===========

**Multi-Role System**

- **Advokat** — submit pengajuan pro bono, lihat riwayat & sertifikat
- **PBH Cabang** — lihat data cabangnya sendiri (read-only)
- **Admin** — verifikasi pengajuan, kelola user, export laporan
- **Super Admin** — akses penuh + konfigurasi keamanan sistem

**Pengajuan Pro Bono**

- Form dinamis: Litigasi (7 jenis perkara) & Non-Litigasi (Seminar, Penyuluhan, Pendampingan)
- Penerima manfaat berjenjang: Anak (< 18 tahun) atau Dewasa (≥ 18 tahun)
- Upload multi-file: KTP, Resume, Surat Kuasa, Dokumen Pendukung, Foto
- Auto-save draft tiap 30 detik ke localStorage + server
- Preview sebelum submit

**Verifikasi & Feedback**

- Verifikasi approve dengan input durasi jam
- Reject dengan alasan wajib
- Kirim feedback per catatan (advokat upload perbaikan)
- Bulk approve dengan skip invalid
- Riwayat feedback transparan

**Sertifikat & Rekapitulasi**

- Perhitungan otomatis: 50 jam penanganan = 1 sertifikat
- Rekapitulasi per tahun di dashboard advokat
- Download e-certificate PDF (format A4 landscape)
- QR code untuk verifikasi keaslian

**Keamanan**

- Password hashing Argon2ID
- Rate limit login (5x/menit per IP)
- Auto-block IP setelah 20 gagal dalam 60 menit
- Alert email login gagal berulang ke admin
- 2FA opsional untuk admin/super_admin
- CSP, CSRF, XSS protection
- Log aktivitas lengkap + arsip 90 hari
- Session fingerprint + idle timeout

**Notifikasi**

- In-app notification (bell icon)
- Email via PHPMailer (SMTP)
- Mark as read + navigasi otomatis
- Multi-channel: register, verify, reset password, alert keamanan


Teknologi
=========

**Backend**

- PHP 8.1+ (tested 8.2.12)
- CodeIgniter 4.7.x
- MySQL 8.0+ / MariaDB 10.4+

**Frontend**

- Bootstrap 5.3.3
- Bootstrap Icons
- Chart.js 4.4.0
- AOS (Animate on Scroll)
- Template iLanding (BootstrapMade)

**Library PHP**

- `phpmailer/phpmailer` — pengiriman email SMTP
- `dompdf/dompdf` — generate PDF
- `phpoffice/phpspreadsheet` — export Excel
- `pragmarx/google2fa` — Two-Factor Authentication

**Server Requirement**

- Apache / Nginx
- cPanel atau VPS Linux
- SSL Let's Encrypt
- Cron job support


Struktur Direktori
==================

::

    probono-ci4/
    ├── app/
    │   ├── Config/                 # Konfigurasi aplikasi
    │   ├── Controllers/
    │   │   ├── Auth/               # Login, register, password reset
    │   │   ├── Advokat/            # Dashboard advokat
    │   │   ├── Admin/              # Dashboard admin
    │   │   └── Cabang/             # Dashboard cabang
    │   ├── Database/
    │   │   ├── Migrations/         # Struktur tabel
    │   │   └── Seeds/              # Data awal
    │   ├── Filters/                # Middleware keamanan
    │   ├── Helpers/                # Fungsi bantu
    │   ├── Libraries/              # Mailer, FileUploader, dll
    │   ├── Models/                 # Model database
    │   └── Views/
    │       ├── layouts/            # Layout utama
    │       ├── auth/               # View autentikasi
    │       ├── advokat/            # View advokat
    │       ├── admin/              # View admin
    │       ├── cabang/             # View cabang
    │       ├── notifications/      # View notifikasi
    │       ├── emails/             # Template email
    │       └── errors/             # Halaman error
    ├── public/
    │   ├── index.php               # Entry point
    │   ├── assets/                 # CSS, JS, gambar
    │   └── .htaccess
    ├── writable/
    │   ├── uploads/                # File upload user
    │   │   ├── ktp/
    │   │   ├── dokumen/
    │   │   ├── foto/
    │   │   └── profil/
    │   ├── certificates/           # Sertifikat PDF generated
    │   ├── exports/                # Export Excel/PDF
    │   ├── logs/                   # Log aplikasi
    │   ├── session/                # Session file
    │   └── cache/
    ├── .env                        # Konfigurasi environment
    ├── composer.json
    └── spark                       # CLI CodeIgniter


Instalasi
=========

**1. Clone Repository**

.. code-block:: bash

    git clone https://github.com/USERNAME/probono-ci4.git
    cd probono-ci4

**2. Install Dependencies**

.. code-block:: bash

    composer install

    # Package tambahan
    composer require phpmailer/phpmailer
    composer require dompdf/dompdf
    composer require phpoffice/phpspreadsheet

**3. Setup Environment**

.. code-block:: bash

    cp env .env
    php spark key:generate

Edit file ``.env``:

.. code-block:: ini

    CI_ENVIRONMENT = development
    app.baseURL = 'http://localhost:8080/'

    # Database
    database.default.hostname = localhost
    database.default.database = probono_a
    database.default.username = root
    database.default.password =

    # Email SMTP
    email.SMTPHost = mail.peradi.or.id
    email.SMTPPort = 587
    email.SMTPUser = pbh@peradi.or.id
    email.SMTPPass = PASSWORD_ANDA
    email.SMTPCrypto = tls
    email.fromEmail = pbh@peradi.or.id
    email.fromName = 'PERADI Pro Bono'

**4. Buat Database**

.. code-block:: sql

    CREATE DATABASE probono_a
      CHARACTER SET utf8mb4
      COLLATE utf8mb4_unicode_ci;

**5. Migrate Database**

.. code-block:: bash

    php spark migrate

**6. Seed Data Awal**

.. code-block:: bash

    php spark db:seed PbhCabangSeeder
    php spark db:seed MasterAdvokatSeeder
    php spark db:seed UserSeeder
    php spark db:seed SettingSeeder
    php spark db:seed SecuritySettingsSeeder

**7. Setup Folder Permission**

.. code-block:: bash

    chmod -R 775 writable/

Buat folder upload:

.. code-block:: bash

    mkdir -p writable/uploads/{ktp,dokumen,foto,profil}
    mkdir -p writable/{certificates,exports}

**8. Jalankan Development Server**

.. code-block:: bash

    php spark serve

Buka browser: **http://localhost:8080**


Akun Demo
=========

Setelah seeding, tersedia akun demo:

==========================  ==========================  =================
Role                        Email                       Password
==========================  ==========================  =================
Super Admin                 superadmin@peradi.or.id     Admin@2026
Admin                       admin@peradi.or.id          Admin@2026
PBH Cabang                  cabang.jakarta@peradi.or.id Cabang@2026
Advokat                     advokat@peradi.or.id        Advokat@2026
==========================  ==========================  =================

.. warning::
   Ganti semua password default sebelum deploy ke production!


Konfigurasi Email (SMTP)
========================

Aplikasi menggunakan **PHPMailer** untuk pengiriman email.

**Testing via CLI**

.. code-block:: bash

    php spark email:test your@email.com

**Konfigurasi SMTP PERADI**

.. code-block:: ini

    email.SMTPHost = mail.peradi.or.id
    email.SMTPPort = 587
    email.SMTPUser = pbh@peradi.or.id
    email.SMTPPass = PASSWORD_ANDA
    email.SMTPCrypto = tls

Email yang dikirim:

- Verifikasi registrasi
- Reset password
- Password berhasil diubah
- Akun diaktifkan
- Alert login gagal (ke admin)
- Notifikasi IP diblokir


Cron Job
========

Tambahkan cron job untuk maintenance otomatis:

.. code-block:: bash

    # 1. Arsip log harian (jam 3 pagi)
    0 3 * * * /usr/local/bin/php /home/USERNAME/probono-ci4/spark log:archive 90 >> /home/USERNAME/logs/cron.log 2>&1

    # 2. Cleanup security (jam 4 pagi)
    0 4 * * * /usr/local/bin/php /home/USERNAME/probono-ci4/spark security:cleanup >> /home/USERNAME/logs/cron.log 2>&1

    # 3. Backup database harian (jam 2 pagi)
    0 2 * * * /usr/bin/mysqldump -u root -pPASS probono_a | gzip > /home/USERNAME/backups/db-$(date +\%Y\%m\%d).sql.gz


Keamanan
========

Fitur keamanan yang aktif:

- ✅ Password hashing Argon2ID
- ✅ CSRF protection
- ✅ XSS filtering
- ✅ Content Security Policy (CSP)
- ✅ Rate limit login (5x/menit)
- ✅ Auto-block IP (20 gagal dalam 60 menit)
- ✅ Session fingerprint
- ✅ Session idle timeout (2 jam)
- ✅ Log aktivitas lengkap
- ✅ Two-Factor Authentication (opsional)
- ✅ File upload validation (MIME + rename)
- ✅ Authorization check per role

**Blokir IP**

Admin dapat memblokir IP yang mencurigakan via:

- **Manual**: dari widget "10 Login Gagal Terbaru" di Log Action
- **Otomatis**: setelah 20 gagal dalam 60 menit
- **Whitelist**: super_admin dapat whitelist IP tertentu

**Log Aktivitas**

Semua aksi dicatat di tabel ``activity_logs``:

- Login sukses/gagal, logout
- Submit pengajuan, verifikasi
- Reset password, aktivasi user
- Export data, download file
- Blokir/unblock IP

Log lebih tua dari 90 hari otomatis diarsipkan ke ``activity_logs_archive``.


Testing
=======

**Jalankan unit test**

.. code-block:: bash

    php spark test

**Test email**

.. code-block:: bash

    php spark email:test admin@peradi.or.id

**Test archive log**

.. code-block:: bash

    php spark log:archive 90

**Test security cleanup**

.. code-block:: bash

    php spark security:cleanup


Deployment Production
=====================

**Checklist sebelum deploy:**

- [ ] Ubah ``CI_ENVIRONMENT = production`` di ``.env``
- [ ] Update ``app.baseURL`` ke domain production
- [ ] Aktifkan ``app.forceGlobalSecureRequests = true``
- [ ] Update kredensial SMTP production
- [ ] Hapus akun demo dari database
- [ ] Set password semua admin dengan password kuat
- [ ] Enable 2FA untuk admin & super_admin
- [ ] Setup SSL Let's Encrypt
- [ ] Setup cron job (arsip log, cleanup, backup)
- [ ] Setup monitoring (UptimeRobot, Sentry)
- [ ] Backup database & files
- [ ] Test semua flow di production

**Deploy Commands**

.. code-block:: bash

    # Pull latest code
    git pull origin main

    # Install dependencies
    composer install --no-dev --optimize-autoloader

    # Migrate (kalau ada migration baru)
    php spark migrate

    # Clear cache
    php spark cache:clear


Troubleshooting
===============

**Error: Cannot connect to database**

- Pastikan MySQL jalan (XAMPP Control Panel)
- Cek konfigurasi ``.env`` (hostname, database, user, password)
- Coba ``127.0.0.1`` ganti ``localhost``

**Error: mime_content_type failed**

- File temporary sudah dipindah sebelum MIME dicek
- Solusi: ambil MIME **sebelum** upload (sudah difix di kode)

**Error: CSRF token mismatch**

- Ubah ``security.regenerate = false`` di ``.env``
- Restart server

**Email tidak terkirim**

- Cek log: ``writable/logs/log-*.log``
- Cari ``[Mailer]`` atau ``[SMTP]``
- Test: ``php spark email:test your@email.com``

**File upload gagal**

- Cek folder ``writable/uploads/`` ada & writable
- Test: ``http://localhost/test_upload.php``
- Cek permission: ``chmod -R 775 writable/``

**Chart tidak muncul**

- Pastikan CDN tidak diblokir CSP
- Cek Console (F12) → error?
- Pastikan Chart.js ter-load dari Network tab


Kontribusi
==========

**Development Workflow**

.. code-block:: bash

    # Buat branch fitur
    git checkout -b fitur/nama-fitur

    # Commit perubahan
    git add .
    git commit -m "feat: tambah fitur X"

    # Push ke remote
    git push origin fitur/nama-fitur

    # Buat Pull Request

**Conventional Commits**

- ``feat:`` — fitur baru
- ``fix:`` — perbaikan bug
- ``docs:`` — dokumentasi
- ``style:`` — formatting
- ``refactor:`` — refactoring
- ``test:`` — testing
- ``chore:`` — maintenance


Lisensi
=======

Copyright © 2026 DPN PERADI. All Rights Reserved.

Sistem ini bersifat **proprietary** — hanya untuk internal PERADI.
Dilarang mendistribusikan tanpa izin tertulis dari DPN PERADI.


Catatan Rilis
=============

**v1.0.0** — 2026-10-09

Fitur awal:

- Sistem autentikasi multi-role
- Pengajuan pro bono (litigasi & non-litigasi)
- Verifikasi & feedback dari admin
- Notifikasi in-app + email (PHPMailer)
- Rekapitulasi sertifikat per tahun
- Export Excel & PDF
- Log aktivitas + blokir IP
- Two-Factor Authentication (opsional)
- Dashboard advokat, admin, cabang
