<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\ActivityLogger;
use App\Libraries\ExcelExporter;
use App\Libraries\PdfExporter;
use App\Models\PengajuanModel;

class ExportController extends BaseController
{
    public function form()
    {
        return view('admin/export_form', [
            'title'      => 'Export Laporan',
            'cabangList' => model('PbhCabangModel')->where('tipe', 'DPC')->findAll(),
        ]);
    }

    public function excel()
    {
        $params = $this->getFilterParams();
        $data = $this->getData($params);

        $filepath = (new ExcelExporter())->export($data, [
            'judul'   => 'Laporan Data Pro Bono PERADI',
            'periode' => $this->describePeriode($params),
        ]);

        ActivityLogger::log('export_excel', 'admin',
            'Export Excel: ' . count($data) . ' baris, ' . $this->describePeriode($params));

        return $this->response->download($filepath, null)->setFileName(basename($filepath));
    }

    public function pdf()
    {
        $params = $this->getFilterParams();
        $data = $this->getData($params);

        $filepath = (new PdfExporter())->export($data, [
            'judul'   => 'Laporan Data Pro Bono PERADI',
            'periode' => $this->describePeriode($params),
        ]);

        ActivityLogger::log('export_pdf', 'admin',
            'Export PDF: ' . count($data) . ' baris, ' . $this->describePeriode($params));

        return $this->response->download($filepath, null)->setFileName(basename($filepath));
    }

    private function getFilterParams(): array
    {
        return [
            'cabang_id' => $this->request->getPost('cabang_id'),
            'dari'      => $this->request->getPost('dari'),
            'sampai'    => $this->request->getPost('sampai'),
            'status'    => $this->request->getPost('status'),
            'tahun'     => $this->request->getPost('tahun'),
        ];
    }

    private function getData(array $params): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('pengajuan')
            ->select('pengajuan.*, users.nama_lengkap as advokat_nama, users.nia as advokat_nia, users.gelar as advokat_gelar,
                      pbh_cabang.nama as cabang_nama, verifikator.nama_lengkap as verifikator_nama')
            ->join('users', 'users.id = pengajuan.user_id')
            ->join('pbh_cabang', 'pbh_cabang.id = pengajuan.pbh_cabang_id')
            ->join('users as verifikator', 'verifikator.id = pengajuan.verified_by', 'left')
            ->where('pengajuan.status !=', 'draft')
            ->orderBy('pengajuan.submitted_at', 'DESC');

        if (!empty($params['cabang_id'])) $builder->where('pengajuan.pbh_cabang_id', $params['cabang_id']);
        if (!empty($params['status']))    $builder->where('pengajuan.status', $params['status']);
        if (!empty($params['tahun']))     $builder->where('pengajuan.tahun', $params['tahun']);
        if (!empty($params['dari']))      $builder->where('pengajuan.submitted_at >=', $params['dari'] . ' 00:00:00');
        if (!empty($params['sampai']))    $builder->where('pengajuan.submitted_at <=', $params['sampai'] . ' 23:59:59');

        $rows = $builder->get()->getResultArray();

        // Enrich dengan relasi
        $pmModel = model('PenerimaManfaatModel');
        $dokModel = model('DokumenPengajuanModel');
        $fotoModel = model('FotoKegiatanModel');
        $litigasiModel = model('DetailLitigasiModel');
        $seminarModel = model('DetailSeminarModel');
        $pendampinganModel = model('DetailPendampinganModel');

        foreach ($rows as &$row) {
            $id = (int) $row['id'];
            $row['penerima_manfaat'] = $pmModel->where('pengajuan_id', $id)->findAll();
            $row['dokumen']          = $dokModel->where('pengajuan_id', $id)->findAll();
            $row['foto']             = $fotoModel->where('pengajuan_id', $id)->findAll();

            if ($row['jenis_layanan'] === 'litigasi') {
                $row['detail'] = $litigasiModel->where('pengajuan_id', $id)->first();
            } elseif (($row['jenis_non_litigasi'] ?? '') === 'pendampingan') {
                $row['detail'] = $pendampinganModel->where('pengajuan_id', $id)->first();
            } else {
                $row['detail'] = $seminarModel->where('pengajuan_id', $id)->first();
            }
        }
        return $rows;
    }

    private function describePeriode(array $params): string
    {
        $parts = [];
        if (!empty($params['dari']) && !empty($params['sampai'])) {
            $parts[] = date('d/m/Y', strtotime($params['dari'])) . ' s/d ' . date('d/m/Y', strtotime($params['sampai']));
        } elseif (!empty($params['tahun'])) {
            $parts[] = 'Tahun ' . $params['tahun'];
        } else {
            $parts[] = 'Semua Periode';
        }
        if (!empty($params['cabang_id'])) {
            $c = model('PbhCabangModel')->find($params['cabang_id']);
            if ($c) $parts[] = 'Cabang: ' . $c['nama'];
        } else {
            $parts[] = 'Semua Cabang';
        }
        return implode(' | ', $parts);
    }
}