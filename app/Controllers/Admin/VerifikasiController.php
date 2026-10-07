<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\ActivityLogger;
use App\Models\PengajuanModel;
use App\Models\NotificationModel;
use App\Models\VerificationFeedbackModel;

class VerifikasiController extends BaseController
{
    public function index()
    {
        $filters = [
            'status'        => $this->request->getGet('status'),
            'cabang_id'     => $this->request->getGet('cabang_id'),
            'jenis_layanan' => $this->request->getGet('jenis_layanan'),
            'jenis_perkara' => $this->request->getGet('jenis_perkara'),
            'tahun'         => $this->request->getGet('tahun'),
            'q'             => $this->request->getGet('q'),
            'warning'       => $this->request->getGet('warning'),
        ];

        $model = new PengajuanModel();
        $builder = $model->select('pengajuan.*, users.nama_lengkap as advokat_nama, users.nia as advokat_nia, pbh_cabang.nama as cabang_nama')
            ->join('users', 'users.id = pengajuan.user_id')
            ->join('pbh_cabang', 'pbh_cabang.id = pengajuan.pbh_cabang_id')
            ->where('pengajuan.status !=', 'draft')
            ->orderBy('pengajuan.submitted_at', 'ASC');

        if (!empty($filters['status']))    $builder->where('pengajuan.status', $filters['status']);
        if (!empty($filters['cabang_id'])) $builder->where('pengajuan.pbh_cabang_id', $filters['cabang_id']);
        if (!empty($filters['jenis_layanan'])) $builder->where('pengajuan.jenis_layanan', $filters['jenis_layanan']);
        if (!empty($filters['tahun']))     $builder->where('pengajuan.tahun', $filters['tahun']);
        if (!empty($filters['warning']))   $builder->where('pengajuan.has_warning', 1);
        if (!empty($filters['q'])) {
            $builder->groupStart()
                ->like('pengajuan.no_registrasi', $filters['q'])
                ->orLike('users.nama_lengkap', $filters['q'])
                ->orLike('users.nia', $filters['q'])
            ->groupEnd();
        }
        if (!empty($filters['jenis_perkara'])) {
            $builder->join('detail_litigasi', 'detail_litigasi.pengajuan_id = pengajuan.id', 'left')
                    ->where('detail_litigasi.jenis_perkara', $filters['jenis_perkara']);
        }

        $list = $builder->paginate(20);
        $pager = $model->pager;

        // Ambil data advokat pending (menunggu aktivasi)
        $pendingAdvokat = model('UserModel')
            ->where('role', 'advokat')
            ->where('status', 'pending')
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();

        return view('admin/verifikasi_list', [
            'title'          => 'Daftar Verifikasi',
            'list'           => $list,
            'pager'          => $pager,
            'filters'        => $filters,
            'cabangList'     => model('PbhCabangModel')->where('tipe', 'DPC')->findAll(),
            'pendingAdvokat' => $pendingAdvokat,
        ]);
    }

    public function show(int $id)
    {
        $pengajuan = model('PengajuanModel')->getWithRelations($id);
        if (!$pengajuan) {
            return redirect()->to(base_url('admin/verifikasi'))->with('error', 'Pengajuan tidak ditemukan.');
        }

        // Ambil feedback & warning
        $feedbacks = model('VerificationFeedbackModel')->getByPengajuan($id);

        // Validasi otomatis dokumen
        $validasi = $this->validateDokumen($pengajuan);

        return view('admin/verifikasi_detail', [
            'title'      => 'Detail Verifikasi',
            'pengajuan'  => $pengajuan,
            'feedbacks'  => $feedbacks,
            'validasi'   => $validasi,
        ]);
    }

    public function approve(int $id)
{
    // Validasi input
    $rules = [
        'durasi_jam' => 'required|integer|greater_than[0]|less_than_equal_to[9999]',
    ];
    if (!$this->validate($rules)) {
        return redirect()->back()->with('error', 'Durasi jam wajib diisi (1-9999).');
    }

    $catatan   = $this->request->getPost('catatan_verifikator') ?? '';
    $durasiJam = (int) $this->request->getPost('durasi_jam');

    $model = new PengajuanModel();
    $pengajuan = $model->find($id);
    if (!$pengajuan) {
        return redirect()->back()->with('error', 'Data tidak ditemukan.');
    }

    $model->update($id, [
        'status'              => 'approved',
        'durasi_jam'          => $durasiJam,        // ← simpan durasi
        'catatan_verifikator' => $catatan ?: null,
        'verified_by'         => session()->get('user_id'),
        'verified_at'         => date('Y-m-d H:i:s'),
    ]);

    // Tandai semua feedback resolved
    model('VerificationFeedbackModel')
        ->where('pengajuan_id', $id)
        ->where('resolved_at', null)
        ->set(['resolved_at' => date('Y-m-d H:i:s'), 'resolved_by' => session()->get('user_id')])
        ->update();

    $model->update($id, ['has_warning' => 0, 'warning_count' => 0]);

    // Notifikasi ke advokat
    \App\Models\NotificationModel::push(
        (int) $pengajuan['user_id'],
        'Pengajuan Disetujui ✅',
        "Pengajuan {$pengajuan['no_registrasi']} disetujui dengan durasi {$durasiJam} jam.",
        'success',
        base_url('advokat/pengajuan/' . $id),
        'bi-check-circle'
    );

    \App\Libraries\ActivityLogger::log('verify_approve', 'verifikasi',
        "Approve pengajuan: {$pengajuan['no_registrasi']} | Durasi: {$durasiJam} jam",
        ['subject_type' => 'Pengajuan', 'subject_id' => $id]);

    return redirect()->to(base_url('admin/verifikasi'))->with('success', 'Pengajuan disetujui.');
}

    public function reject(int $id)
    {
        $catatan = $this->request->getPost('catatan_verifikator');
        if (empty($catatan)) {
            return redirect()->back()->with('error', 'Catatan wajib diisi saat menolak.');
        }

        $model = new PengajuanModel();
        $pengajuan = $model->find($id);
        if (!$pengajuan) return redirect()->back()->with('error', 'Data tidak ditemukan.');

        $model->update($id, [
            'status'              => 'rejected',
            'catatan_verifikator' => $catatan,
            'verified_by'         => session()->get('user_id'),
            'verified_at'         => date('Y-m-d H:i:s'),
        ]);

        NotificationModel::push(
            (int) $pengajuan['user_id'],
            'Pengajuan Ditolak ❌',
            "Pengajuan {$pengajuan['no_registrasi']} ditolak. Catatan: {$catatan}",
            'danger',
            base_url('advokat/pengajuan/' . $id),
            'bi-x-circle'
        );

        ActivityLogger::log('verify_reject', 'verifikasi',
            "Reject pengajuan: {$pengajuan['no_registrasi']} - Alasan: {$catatan}",
            ['subject_type' => 'Pengajuan', 'subject_id' => $id]);

        return redirect()->to(base_url('admin/verifikasi'))->with('success', 'Pengajuan ditolak.');
    }

    /**
     * Kirim feedback kekurangan dokumen (tidak menolak, hanya warning)
     */
    public function feedback(int $id)
{
    $catatan = trim($this->request->getPost('catatan') ?? '');
    $kategori = $this->request->getPost('kategori') ?? 'kekurangan_dokumen';

    if (empty($catatan) || strlen($catatan) < 5) {
        return redirect()->back()->with('error', 'Catatan minimal 5 karakter.');
    }

    $pengajuanModel = new \App\Models\PengajuanModel();
    $pengajuan = $pengajuanModel->find($id);

    if (!$pengajuan) {
        return redirect()->back()->with('error', 'Pengajuan tidak ditemukan.');
    }

    // Simpan feedback
    $fbModel = new \App\Models\VerificationFeedbackModel();
    $fbModel->insert([
        'pengajuan_id' => $id,
        'admin_id'     => session()->get('user_id'),
        'kategori'     => $kategori,
        'catatan'      => $catatan,
        'created_at'   => date('Y-m-d H:i:s'),
    ]);

    // Update warning counter
    $warningCount = $fbModel
        ->where('pengajuan_id', $id)
        ->where('resolved_at', null)
        ->countAllResults();

    $pengajuanModel->update($id, [
        'has_warning'   => 1,
        'warning_count' => $warningCount,
    ]);

    // Notifikasi ke advokat
    \App\Models\NotificationModel::push(
        (int) $pengajuan['user_id'],
        '⚠️ Ada Catatan dari Verifikator',
        "Pengajuan {$pengajuan['no_registrasi']}: {$catatan}",
        'warning',
        base_url('advokat/pengajuan/' . $id),
        'bi-exclamation-triangle'
    );

    \App\Libraries\ActivityLogger::log('verify_feedback', 'verifikasi',
        "Feedback pengajuan: {$pengajuan['no_registrasi']} - {$catatan}",
        ['subject_type' => 'Pengajuan', 'subject_id' => $id]);

    return redirect()->back()->with('success', 'Feedback berhasil dikirim ke advokat.');
}

    /**
     * Bulk approve dari halaman list.
     */
    public function bulkApprove()
{
    $ids       = $this->request->getPost('ids') ?? [];
    $catatan   = $this->request->getPost('catatan_verifikator') ?? '';
    $durasiJam = (int) $this->request->getPost('durasi_jam_default') ?: 24; // default 24 kalau tidak diisi
    $skipInvalid = $this->request->getPost('skip_invalid') === '1';

    if (empty($ids)) return redirect()->back()->with('error', 'Tidak ada data dipilih.');

    $model = new PengajuanModel();
    $success = 0; $skipped = 0; $errors = [];

    foreach ($ids as $id) {
        $pengajuan = $model->getWithRelations((int) $id);
        if (!$pengajuan) continue;

        $validasi = $this->validateDokumen($pengajuan);
        if ($skipInvalid && !$validasi['valid']) {
            $skipped++;
            $errors[] = "{$pengajuan['no_registrasi']}: " . implode(', ', $validasi['errors']);
            continue;
        }

        $model->update((int) $id, [
            'status'              => 'approved',
            'durasi_jam'          => $durasiJam,        // ← durasi sama untuk semua
            'catatan_verifikator' => $catatan ?: null,
            'verified_by'         => session()->get('user_id'),
            'verified_at'         => date('Y-m-d H:i:s'),
            'has_warning'         => 0,
            'warning_count'       => 0,
        ]);

        model('VerificationFeedbackModel')
            ->where('pengajuan_id', (int) $id)
            ->where('resolved_at', null)
            ->set(['resolved_at' => date('Y-m-d H:i:s'), 'resolved_by' => session()->get('user_id')])
            ->update();

        \App\Models\NotificationModel::push(
            (int) $pengajuan['user_id'],
            'Pengajuan Disetujui ✅',
            "Pengajuan {$pengajuan['no_registrasi']} disetujui dengan durasi {$durasiJam} jam.",
            'success',
            base_url('advokat/pengajuan/' . $id),
            'bi-check-circle'
        );

        $success++;
    }

    \App\Libraries\ActivityLogger::log('verify_bulk_approve', 'verifikasi',
        "Bulk approve: {$success} sukses, {$skipped} skip | Durasi: {$durasiJam} jam");

    $msg = "{$success} pengajuan disetujui.";
    if ($skipped > 0) $msg .= " {$skipped} dilewati.";
    return redirect()->back()->with('success', $msg)->with('bulk_errors', $errors);
}

    /**
     * Validasi otomatis dokumen wajib per jenis.
     */
    private function validateDokumen(array $p): array
    {
        $errors = [];
        $dokumenAda = array_column($p['dokumen'] ?? [], 'jenis_dokumen');

        if ($p['jenis_layanan'] === 'litigasi') {
            $wajib = ['resume','surat_tidak_kuasa','surat_penilaian','surat_kuasa','dokumen_pendukung'];
            foreach ($wajib as $w) {
                if (!in_array($w, $dokumenAda, true)) $errors[] = "Dokumen {$w} tidak ada";
            }
            if (empty($p['detail']['jenis_perkara'])) $errors[] = 'Jenis perkara kosong';
        } elseif (($p['jenis_non_litigasi'] ?? '') === 'pendampingan') {
            $wajib = ['surat_tidak_kuasa','surat_penilaian','surat_kuasa','dokumen_pendukung'];
            foreach ($wajib as $w) {
                if (!in_array($w, $dokumenAda, true)) $errors[] = "Dokumen {$w} tidak ada";
            }
            if (empty($p['detail']['lokasi'])) $errors[] = 'Lokasi pendampingan kosong';
        } elseif (in_array($p['jenis_non_litigasi'] ?? '', ['seminar','penyuluhan'])) {
            if (!in_array('undangan_flyer', $dokumenAda, true)) $errors[] = 'Undangan/flyer tidak ada';
            if (count($p['foto'] ?? []) < 3) $errors[] = 'Foto kurang dari 3';
            foreach (['nama_acara','tanggal_acara','waktu_acara','tempat_acara','peserta','jumlah_peserta'] as $f) {
                if (empty($p['detail'][$f])) $errors[] = "Field {$f} kosong";
            }
        }

        if (empty($p['penerima_manfaat']) && !in_array($p['jenis_non_litigasi'] ?? '', ['seminar','penyuluhan'])) {
            $errors[] = 'Penerima manfaat kosong';
        }

        return ['valid' => empty($errors), 'errors' => $errors];
    }
}