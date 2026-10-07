<?php

namespace App\Controllers\Advokat;

use App\Controllers\BaseController;
use App\Libraries\ActivityLogger;
use App\Libraries\DraftManager;
use App\Libraries\FileUploader;
use App\Models\PengajuanModel;
use App\Models\PenerimaManfaatModel;
use App\Models\DokumenPengajuanModel;
use App\Models\FotoKegiatanModel;
use App\Models\DetailLitigasiModel;
use App\Models\DetailSeminarModel;
use App\Models\DetailPendampinganModel;
use App\Models\NotificationModel;

class PengajuanController extends BaseController
{
    public function index()
    {
        return redirect()->to(base_url('advokat/dashboard'));
    }

    public function create()
    {
        return view('advokat/pengajuan_form', [
            'title'         => 'Submit Data Pro Bono',
            'mode'          => 'create',
            'pengajuan'     => null,
            'draft'         => null,
        ]);
    }

    public function edit(int $id)
    {
        $userId = session()->get('user_id');
        $draft = DraftManager::load($userId, $id);
        if (!$draft) {
            return redirect()->to(base_url('advokat/dashboard'))
                ->with('error', 'Draft tidak ditemukan.');
        }
        return view('advokat/pengajuan_form', [
            'title'     => 'Edit Draft Pro Bono',
            'mode'      => 'edit',
            'pengajuan' => $draft,
            'draft'     => $draft['draft_data'],
        ]);
    }

    /**
     * AJAX: auto-save draft tiap 30 detik
     */
    public function saveDraft()
    {
        $userId = session()->get('user_id');
        $pengajuanId = $this->request->getPost('pengajuan_id') ?: null;
        $payload = json_decode($this->request->getPost('payload') ?? '{}', true);

        if (empty($payload)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Payload kosong.']);
        }

        $result = DraftManager::save($userId, $pengajuanId, $payload);

        if ($result['success']) {
            ActivityLogger::log(
                'draft_saved',
                'pengajuan',
                'Draft tersimpan: ' . ($pengajuanId ?? $result['pengajuan_id']),
                ['subject_type' => 'Pengajuan', 'subject_id' => $result['pengajuan_id']]
            );
        }

        return $this->response->setJSON($result);
    }

    public function loadDraft(int $id)
    {
        $draft = DraftManager::load(session()->get('user_id'), $id);
        return $this->response->setJSON($draft ?? ['success' => false]);
    }

    /**
     * Submit final (dari modal konfirmasi preview)
     */
    public function store()
    {
        $userId = session()->get('user_id');
        $pengajuanId = $this->request->getPost('pengajuan_id') ?: null;

        $rules = [
            'jenis_layanan' => 'required|in_list[litigasi,non-litigasi]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $jenis = $this->request->getPost('jenis_layanan');

        // Validasi per jenis
        if ($jenis === 'litigasi') {
            $rulesLitigasi = [
                'jenis_perkara' => 'required',
            ];
            if (!$this->validate($rulesLitigasi)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
        } elseif ($jenis === 'non-litigasi') {
            $sub = $this->request->getPost('jenis_non_litigasi');
            if (!in_array($sub, ['seminar', 'penyuluhan', 'pendampingan'], true)) {
                return redirect()->back()->withInput()->with('error', 'Jenis non-litigasi tidak valid.');
            }
            if (in_array($sub, ['seminar', 'penyuluhan'], true)) {
                $rulesSeminar = [
                    'nama_acara'    => 'required|max_length[200]',
                    'tanggal_acara' => 'required|valid_date',
                    'waktu_acara'   => 'required',
                    'tempat_acara'  => 'required|max_length[200]',
                    'peserta'       => 'required|max_length[200]',
                    'jumlah_peserta' => 'required|integer|greater_than[0]',
                ];
                if (!$this->validate($rulesSeminar)) {
                    return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
                }
            } elseif ($sub === 'pendampingan') {
                if (!$this->validate(['lokasi_pendampingan' => 'required|max_length[200]'])) {
                    return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
                }
            }
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Buat / update header pengajuan
        $model = new PengajuanModel();
        if ($pengajuanId) {
            $existing = $model->where('id', $pengajuanId)->where('user_id', $userId)->first();
            if (!$existing || $existing['status'] !== 'draft') {
                return redirect()->back()->with('error', 'Draft tidak valid.');
            }
            $model->update($pengajuanId, [
                'jenis_layanan'      => $jenis,
                'jenis_non_litigasi' => $jenis === 'non-litigasi' ? $this->request->getPost('jenis_non_litigasi') : null,
                'status'             => 'submitted',
                'submitted_at'       => date('Y-m-d H:i:s'),
                'draft_data'         => null, // hapus draft setelah submit
            ]);
        } else {
            $tahun = date('Y');
            $noReg = $model->generateNoRegistrasi($tahun, session()->get('cabang_id'));
            $pengajuanId = $model->insert([
                'no_registrasi'      => $noReg,
                'user_id'            => $userId,
                'pbh_cabang_id'      => session()->get('cabang_id'),
                'jenis_layanan'      => $jenis,
                'jenis_non_litigasi' => $jenis === 'non-litigasi' ? $this->request->getPost('jenis_non_litigasi') : null,
                'tahun'              => $tahun,
                'status'             => 'submitted',
                'submitted_at'       => date('Y-m-d H:i:s'),
            ], true);
        }

        // Bersihkan data lama (kalau edit draft yang sudah pernah submit partial)
        model('PenerimaManfaatModel')->where('pengajuan_id', $pengajuanId)->delete();
        model('DokumenPengajuanModel')->where('pengajuan_id', $pengajuanId)->delete();
        model('FotoKegiatanModel')->where('pengajuan_id', $pengajuanId)->delete();

        // Simpan penerima manfaat (array dari form)
        $this->savePenerimaManfaat($pengajuanId, $jenis);

        // Simpan detail per jenis
        if ($jenis === 'litigasi') {
            $this->saveDetailLitigasi($pengajuanId);
            $this->saveDokumenLitigasi($pengajuanId);
        } elseif ($this->request->getPost('jenis_non_litigasi') === 'pendampingan') {
            $this->saveDetailPendampingan($pengajuanId);
            $this->saveDokumenPendampingan($pengajuanId);
        } else {
            $this->saveDetailSeminar($pengajuanId);
            $this->saveFotoSeminar($pengajuanId);
            $this->saveDokumenSeminar($pengajuanId);
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data. Coba lagi.');
        }

        // Notifikasi ke semua admin (verifikator)
        $this->notifyAdmins($pengajuanId, $jenis);

        // Notifikasi ke advokat sendiri
        NotificationModel::push(
            $userId,
            'Pengajuan Berhasil Dikirim',
            "Pengajuan {$model->find($pengajuanId)['no_registrasi']} berhasil dikirim dan menunggu verifikasi.",
            'success',
            base_url('advokat/pengajuan/' . $pengajuanId),
            'bi-send-check'
        );

        ActivityLogger::log(
            'submit_pengajuan',
            'pengajuan',
            'Submit pengajuan: ' . $model->find($pengajuanId)['no_registrasi'],
            ['subject_type' => 'Pengajuan', 'subject_id' => $pengajuanId]
        );

        return redirect()->to(base_url('advokat/pengajuan/' . $pengajuanId))
            ->with('success', 'Pengajuan berhasil dikirim. Menunggu verifikasi admin.');
    }

    public function show(int $id)
    {
        $pengajuan = model('PengajuanModel')->getWithRelations($id);
        if (!$pengajuan) {
            return redirect()->to(base_url('advokat/dashboard'))->with('error', 'Pengajuan tidak ditemukan.');
        }

        // Cek ownership
        if ((int) $pengajuan['user_id'] !== (int) session()->get('user_id')) {
            return redirect()->to(base_url('advokat/dashboard'))->with('error', 'Anda tidak berhak melihat data ini.');
        }

        // ✅ TAMBAHKAN INI — ambil feedback
        $feedbacks = model('VerificationFeedbackModel')->getByPengajuan($id);

        return view('advokat/pengajuan_detail', [
            'title'     => 'Detail Pengajuan',
            'pengajuan' => $pengajuan,
            'feedbacks' => $feedbacks,   // ← kirim ke view
        ]);
    }

    public function preview(int $id)
    {
        $draft = DraftManager::load(session()->get('user_id'), $id);
        if (!$draft) return $this->response->setJSON(['success' => false]);
        return $this->response->setJSON([
            'success' => true,
            'html'    => view('advokat/pengajuan_preview_modal', ['draft' => $draft]),
        ]);
    }

    public function delete(int $id)
    {
        $ok = DraftManager::delete(session()->get('user_id'), $id);
        ActivityLogger::log('delete_draft', 'pengajuan', 'Hapus draft ID: ' . $id);
        return redirect()->to(base_url('advokat/dashboard'))
            ->with($ok ? 'success' : 'error', $ok ? 'Draft dihapus.' : 'Gagal hapus draft.');
    }

    // ============================================================
    // PRIVATE HELPERS
    // ============================================================

    private function savePenerimaManfaat(int $pengajuanId, string $jenis): void
    {
        $prefix = $jenis === 'litigasi' ? 'litigasi' : 'pendampingan';
        $pmModel = new PenerimaManfaatModel();
        $uploader = new FileUploader();

        foreach ($this->request->getPost($prefix . '_usia') ?? [] as $idx => $usia) {
            $usia = (int) $usia;
            $kategori = $usia < 18 ? 'anak' : 'dewasa';

            $fileKtp = null;
            if (!empty($_FILES[$prefix . '_ktp_' . $idx]['name'])) {
                $fileKtp = $uploader->uploadKtp($_FILES[$prefix . '_ktp_' . $idx], $pengajuanId);
            }

            $pmModel->insert([
                'pengajuan_id'  => $pengajuanId,
                'usia'          => $usia,
                'kategori'      => $kategori,
                'nama'          => $kategori === 'anak' ? $this->request->getPost($prefix . '_nama_anak_' . $idx) : null,
                'jenis_kelamin' => $kategori === 'dewasa' ? $this->request->getPost($prefix . '_jk_' . $idx) : null,
                'pekerjaan'     => $kategori === 'dewasa' ? $this->request->getPost($prefix . '_pekerjaan_' . $idx) : null,
                'file_ktp'      => $fileKtp,
            ]);
        }
    }

    private function saveDetailLitigasi(int $pengajuanId): void
    {
        model('DetailLitigasiModel')->insert([
            'pengajuan_id'  => $pengajuanId,
            'jenis_perkara' => $this->request->getPost('jenis_perkara'),
            'no_perkara'    => $this->request->getPost('no_perkara'),
            'pengadilan'    => $this->request->getPost('pengadilan'),
        ]);
    }

    private function saveDetailSeminar(int $pengajuanId): void
    {
        model('DetailSeminarModel')->insert([
            'pengajuan_id'  => $pengajuanId,
            'nama_acara'    => $this->request->getPost('nama_acara'),
            'tanggal_acara' => $this->request->getPost('tanggal_acara'),
            'waktu_acara'   => $this->request->getPost('waktu_acara'),
            'tempat_acara'  => $this->request->getPost('tempat_acara'),
            'peserta'       => $this->request->getPost('peserta'),
            'jumlah_peserta' => (int) $this->request->getPost('jumlah_peserta'),
        ]);
    }

    private function saveDetailPendampingan(int $pengajuanId): void
    {
        model('DetailPendampinganModel')->insert([
            'pengajuan_id' => $pengajuanId,
            'lokasi'       => $this->request->getPost('lokasi_pendampingan'),
        ]);
    }

    private function saveDokumenLitigasi(int $pengajuanId): void
    {
        $uploader = new FileUploader();
        $dokModel = new DokumenPengajuanModel();

        $dokumenWajib = [
            'resume_perkara'             => 'resume',
            'surat_tidak_kuasa_litigasi' => 'surat_tidak_kuasa',
            'surat_penilaian_litigasi'   => 'surat_penilaian',
            'surat_kuasa_litigasi'       => 'surat_kuasa',
            'dokumen_pendukung_litigasi' => 'dokumen_pendukung',
        ];

        foreach ($dokumenWajib as $field => $jenisDok) {
            if (empty($_FILES[$field]['name']) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
                continue;
            }

            $tmpPath = $_FILES[$field]['tmp_name'];

            // Ambil MIME SEBELUM dipindah
            $mime = 'application/octet-stream';
            if (file_exists($tmpPath)) {
                $mime = mime_content_type($tmpPath) ?: 'application/octet-stream';
            }

            // Upload — pastikan folder ada dulu
            try {
                $path = $uploader->uploadDokumen($_FILES[$field], $pengajuanId, $jenisDok);
            } catch (\Throwable $e) {
                log_message('error', "[Upload] {$field} gagal: " . $e->getMessage());
                continue;
            }

            $dokModel->insert([
                'pengajuan_id'  => $pengajuanId,
                'jenis_dokumen' => $jenisDok,
                'file_path'     => $path,
                'file_name'     => $_FILES[$field]['name'],
                'file_size'     => $_FILES[$field]['size'],
                'mime_type'     => $mime,
            ]);
        }
    }

    private function saveDokumenPendampingan(int $pengajuanId): void
    {
        $uploader = new FileUploader();
        $dokModel = new DokumenPengajuanModel();
        $dokumenWajib = [
            'surat_tidak_kuasa_pendampingan' => 'surat_tidak_kuasa',
            'surat_penilaian_pendampingan'   => 'surat_penilaian',
            'surat_kuasa_pendampingan'       => 'surat_kuasa',
            'dokumen_pendukung_pendampingan' => 'dokumen_pendukung',
        ];
        foreach ($dokumenWajib as $field => $jenisDok) {
            if (!empty($_FILES[$field]['name'])) {
                $path = $uploader->uploadDokumen($_FILES[$field], $pengajuanId, $jenisDok);
                $dokModel->insert([
                    'pengajuan_id'  => $pengajuanId,
                    'jenis_dokumen' => $jenisDok,
                    'file_path'     => $path,
                    'file_name'     => $_FILES[$field]['name'],
                    'file_size'     => $_FILES[$field]['size'],
                    'mime_type'     => mime_content_type($_FILES[$field]['tmp_name']),
                ]);
            }
        }
    }

    private function saveDokumenSeminar(int $pengajuanId): void
    {
        if (!empty($_FILES['undangan_flyer']['name'])) {
            $uploader = new FileUploader();
            $path = $uploader->uploadDokumen($_FILES['undangan_flyer'], $pengajuanId, 'undangan_flyer');
            model('DokumenPengajuanModel')->insert([
                'pengajuan_id'  => $pengajuanId,
                'jenis_dokumen' => 'undangan_flyer',
                'file_path'     => $path,
                'file_name'     => $_FILES['undangan_flyer']['name'],
                'file_size'     => $_FILES['undangan_flyer']['size'],
                'mime_type'     => mime_content_type($_FILES['undangan_flyer']['tmp_name']),
            ]);
        }
    }

    private function saveFotoSeminar(int $pengajuanId): void
    {
        $uploader = new FileUploader();
        $fotoModel = new FotoKegiatanModel();
        $urutan = 1;
        foreach ($_FILES as $field => $file) {
            if (strpos($field, 'bukti_foto_sp_') === 0 && !empty($file['name'])) {
                $path = $uploader->uploadFoto($file, $pengajuanId);
                $fotoModel->insert([
                    'pengajuan_id' => $pengajuanId,
                    'file_path'    => $path,
                    'file_name'    => $file['name'],
                    'file_size'    => $file['size'],
                    'urutan'       => $urutan++,
                ]);
            }
        }
    }

    private function notifyAdmins(int $pengajuanId, string $jenis): void
    {
        $pengajuan = model('PengajuanModel')->find($pengajuanId);
        $admins = model('UserModel')
            ->whereIn('role', ['admin', 'super_admin'])
            ->where('status', 'aktif')
            ->findAll();

        foreach ($admins as $admin) {
            NotificationModel::push(
                (int) $admin['id'],
                'Pengajuan Pro Bono Baru',
                "Pengajuan {$pengajuan['no_registrasi']} ({$jenis}) menunggu verifikasi Anda.",
                'info',
                base_url('admin/verifikasi/' . $pengajuanId),
                'bi-inbox'
            );
        }
        $this->notifyCabang($pengajuanId, $jenis);
    }

    private function notifyCabang(int $pengajuanId, string $jenis): void
    {
        $pengajuan = model('PengajuanModel')->find($pengajuanId);
        $cabangId = (int) $pengajuan['pbh_cabang_id'];

        // Cari semua user role 'cabang' di cabang yang sama
        $pengurus = model('UserModel')
            ->where('role', 'cabang')
            ->where('pbh_cabang_id', $cabangId)
            ->where('status', 'aktif')
            ->findAll();

        foreach ($pengurus as $p) {
            \App\Models\NotificationModel::push(
                (int) $p['id'],
                'Pengajuan Baru di Cabang Anda',
                "Pengajuan {$pengajuan['no_registrasi']} ({$jenis}) telah diajukan oleh advokat cabang Anda.",
                'info',
                base_url('cabang/pengajuan/' . $pengajuanId),
                'bi-inbox'
            );
        }
    }

    /**
     * Advokat upload perbaikan untuk satu catatan verifikator.
     * POST /advokat/pengajuan/feedback/{id}/tangani
     */
    public function tanganiFeedback(int $feedbackId)
    {
        $userId = session()->get('user_id');
        $fbModel = model('VerificationFeedbackModel');
        $pengajuanModel = model('PengajuanModel');

        // 1. Ambil feedback
        $fb = $fbModel->find($feedbackId);
        if (!$fb) {
            return redirect()->back()->with('error', 'Catatan tidak ditemukan.');
        }

        // 2. Cek ownership
        $pengajuan = $pengajuanModel->find($fb['pengajuan_id']);
        if (!$pengajuan || (int) $pengajuan['user_id'] !== (int) $userId) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        // 3. Validasi input
        $catatan = trim($this->request->getPost('catatan_advokat') ?? '');
        $file = $this->request->getFile('file_perbaikan');

        if (empty($catatan) && (!$file || !$file->isValid())) {
            return redirect()->back()->with('error', 'Isi keterangan atau upload file perbaikan.');
        }

        // 4. Upload file kalau ada
        $filePath = null;
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Validasi MIME & ukuran
            $allowedMimes = ['application/pdf', 'image/jpeg', 'image/png'];
            $mime = $file->getMimeType();
            if (!in_array($mime, $allowedMimes, true)) {
                return redirect()->back()->with('error', 'Tipe file tidak diizinkan.');
            }
            if ($file->getSize() > 5 * 1024 * 1024) {
                return redirect()->back()->with('error', 'Ukuran file melebihi 5 MB.');
            }

            // Pindah ke folder
            $newName = $file->getRandomName();
            $dir = WRITEPATH . 'uploads/perbaikan/' . date('Y/m/');
            if (!is_dir($dir)) mkdir($dir, 0775, true);

            $file->move($dir, $newName);
            $filePath = 'perbaikan/' . date('Y/m/') . $newName;
        }

        // 5. Update feedback
        $fbModel->update($feedbackId, [
            'file_perbaikan'      => $filePath,
            'catatan_advokat'     => $catatan,
            'resolved_by_advokat' => 1,
            'resolved_at'         => date('Y-m-d H:i:s'),
            'resolved_by'         => $userId,
        ]);

        // 6. Cek sisa feedback yang belum ditangani
        $sisa = $fbModel->where('pengajuan_id', $fb['pengajuan_id'])
            ->where('resolved_at', null)
            ->countAllResults();

        // 7. Update warning counter
        $pengajuanModel->update($fb['pengajuan_id'], [
            'has_warning'   => $sisa > 0 ? 1 : 0,
            'warning_count' => $sisa,
        ]);

        // 8. Kalau semua sudah ditangani → notifikasi ke admin
        if ($sisa === 0) {
            $this->notifyAdminPerbaikanSelesai($pengajuan, 'upload');
        }

        // 9. Log
        \App\Libraries\ActivityLogger::log(
            'advokat_tangani_feedback',
            'pengajuan',
            "Advokat tangani feedback #{$feedbackId} untuk {$pengajuan['no_registrasi']}",
            ['subject_type' => 'Pengajuan', 'subject_id' => $pengajuan['id']]
        );

        return redirect()->back()->with('success', 'Perbaikan berhasil dikirim. Admin akan verifikasi ulang.');
    }
    public function revisi(int $id)
    {
        $userId = session()->get('user_id');
        $pengajuan = model('PengajuanModel')->getWithRelations($id);

        if (!$pengajuan) {
            return redirect()->to(base_url('advokat/dashboard'))
                ->with('error', 'Pengajuan tidak ditemukan.');
        }

        // Cek ownership
        if ((int) $pengajuan['user_id'] !== (int) $userId) {
            return redirect()->to(base_url('advokat/dashboard'))
                ->with('error', 'Akses ditolak.');
        }

        // Hanya bisa revisi kalau ada warning ATAU status masih submitted/review
        if (!in_array($pengajuan['status'], ['submitted', 'review'], true)) {
            return redirect()->to(base_url('advokat/pengajuan/' . $id))
                ->with('error', 'Pengajuan tidak dapat direvisi pada status ini.');
        }

        // Ambil feedback yang belum ditangani
        $feedbacks = model('VerificationFeedbackModel')
            ->where('pengajuan_id', $id)
            ->where('resolved_at', null)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('advokat/pengajuan_revisi', [
            'title'     => 'Revisi Pengajuan',
            'mode'      => 'revisi',
            'pengajuan' => $pengajuan,
            'feedbacks' => $feedbacks,
        ]);
    }
    public function revisiStore(int $id)
    {
        $userId = session()->get('user_id');
        $pengajuanModel = model('PengajuanModel');
        $pengajuan = $pengajuanModel->find($id);

        if (!$pengajuan || (int) $pengajuan['user_id'] !== (int) $userId) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        if (!in_array($pengajuan['status'], ['submitted', 'review'], true)) {
            return redirect()->back()->with('error', 'Pengajuan tidak dapat direvisi.');
        }

        // Simpan data (mirip store())
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Hapus data lama
        model('PenerimaManfaatModel')->where('pengajuan_id', $id)->delete();
        model('DokumenPengajuanModel')->where('pengajuan_id', $id)->delete();
        model('FotoKegiatanModel')->where('pengajuan_id', $id)->delete();

        // 2. Simpan ulang penerima manfaat
        $jenis = $pengajuan['jenis_layanan'];
        $this->savePenerimaManfaat($id, $jenis);

        // 3. Simpan ulang detail & dokumen
        if ($jenis === 'litigasi') {
            model('DetailLitigasiModel')->where('pengajuan_id', $id)->delete();
            $this->saveDetailLitigasi($id);
            $this->saveDokumenLitigasi($id);
        } else {
            $sub = $pengajuan['jenis_non_litigasi'];
            if ($sub === 'pendampingan') {
                model('DetailPendampinganModel')->where('pengajuan_id', $id)->delete();
                $this->saveDetailPendampingan($id);
                $this->saveDokumenPendampingan($id);
            } else {
                model('DetailSeminarModel')->where('pengajuan_id', $id)->delete();
                $this->saveDetailSeminar($id);
                $this->saveFotoSeminar($id);
                $this->saveDokumenSeminar($id);
            }
        }

        // 4. Tandai semua feedback resolved
        model('VerificationFeedbackModel')
            ->where('pengajuan_id', $id)
            ->where('resolved_at', null)
            ->set([
                'resolved_at'         => date('Y-m-d H:i:s'),
                'resolved_by'         => $userId,
                'resolved_by_advokat' => 1,
                'catatan_advokat'     => 'Direvisi lengkap oleh advokat',
            ])
            ->update();

        // 5. Reset warning di pengajuan
        $pengajuanModel->update($id, [
            'has_warning'   => 0,
            'warning_count' => 0,
        ]);

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->with('error', 'Gagal menyimpan revisi.');
        }

        // 6. Notifikasi ke admin
        $this->notifyAdminPerbaikanSelesai($pengajuan, 'revisi');

        // 7. Log
        \App\Libraries\ActivityLogger::log(
            'advokat_revisi_pengajuan',
            'pengajuan',
            "Advokat revisi lengkap pengajuan {$pengajuan['no_registrasi']}",
            ['subject_type' => 'Pengajuan', 'subject_id' => $id]
        );

        return redirect()->to(base_url('advokat/pengajuan/' . $id))
            ->with('success', 'Revisi berhasil dikirim. Admin akan verifikasi ulang.');
    }
    private function notifyAdminPerbaikanSelesai(array $pengajuan, string $tipe = 'upload'): void
    {
        $admins = model('UserModel')
            ->whereIn('role', ['admin', 'super_admin'])
            ->where('status', 'aktif')
            ->findAll();

        $judul = $tipe === 'revisi'
            ? '📝 Pengajuan Direvisi Lengkap'
            : '✅ Advokat Sudah Memperbaiki';

        $pesan = $tipe === 'revisi'
            ? "Pengajuan {$pengajuan['no_registrasi']} sudah direvisi lengkap. Mohon verifikasi ulang."
            : "Pengajuan {$pengajuan['no_registrasi']} sudah diperbaiki. Mohon verifikasi ulang.";

        foreach ($admins as $admin) {
            \App\Models\NotificationModel::push(
                (int) $admin['id'],
                $judul,
                $pesan,
                'success',
                base_url('admin/verifikasi/' . $pengajuan['id']),
                'bi-check-circle'
            );
        }
    }
}
