<?php
namespace App\Libraries;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ExcelExporter
{
    /**
     * Export data pengajuan ke Excel.
     * $pengajuan: array hasil query dengan relasi (penerima_manfaat, dokumen, detail).
     */
    public function export(array $pengajuan, array $options = []): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Pro Bono');

        // ---------- HEADER JUDUL ----------
        $judul = $options['judul'] ?? SettingModel::get('export_excel_title', 'Laporan Data Pro Bono PERADI');
        $sheet->mergeCells('A1:M1');
        $sheet->setCellValue('A1', $judul);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $periode = $options['periode'] ?? 'Semua Periode';
        $sheet->mergeCells('A2:M2');
        $sheet->setCellValue('A2', 'Periode: ' . $periode);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ---------- HEADER KOLOM ----------
        $headers = [
            'No. Registrasi','Tanggal','Tahun','NIA Advokat','Nama Advokat',
            'PBH Cabang','Jenis Layanan','Jenis Perkara',
            'Nama Penerima Manfaat / Peserta','Jumlah Penerima Manfaat',
            'Link Surat Kuasa','Tanggal Verifikasi','Verifikator',
        ];
        $row = 4;
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . $row, $h);
            $sheet->getStyle($col . $row)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle($col . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('0A1F3C');
            $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
            $col++;
        }
        $sheet->getRowDimension($row)->setRowHeight(30);

        // ---------- DATA ----------
        $row++;
        foreach ($pengajuan as $p) {
            $namaPm = $this->getNamaPenerimaManfaat($p);
            $jumlahPm = count($p['penerima_manfaat'] ?? []);
            $linkSuratKuasa = $this->getLinkSuratKuasa($p);

            $sheet->setCellValue('A' . $row, $p['no_registrasi']);
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($p['created_at'])));
            $sheet->setCellValue('C' . $row, $p['tahun']);
            $sheet->setCellValue('D' . $row, $p['advokat_nia'] ?? '-');
            $sheet->setCellValue('E' . $row, ($p['advokat_nama'] ?? '-') . (!empty($p['advokat_gelar']) ? ', ' . $p['advokat_gelar'] : ''));
            $sheet->setCellValue('F' . $row, $p['cabang_nama'] ?? '-');
            $sheet->setCellValue('G' . $row, ucfirst($p['jenis_layanan']) . ($p['jenis_non_litigasi'] ? ' - ' . ucfirst($p['jenis_non_litigasi']) : ''));
            $sheet->setCellValue('H' . $row, $p['detail']['jenis_perkara'] ?? '-');
            $sheet->setCellValue('I' . $row, $namaPm);
            $sheet->setCellValue('J' . $row, $jumlahPm);
            $sheet->setCellValue('K' . $row, $linkSuratKuasa);
            $sheet->setCellValue('L' . $row, $p['verified_at'] ? date('d/m/Y H:i', strtotime($p['verified_at'])) : '-');
            $sheet->setCellValue('M' . $row, $p['verifikator_nama'] ?? '-');
            $row++;
        }

        // ---------- AUTO SIZE ----------
        foreach (range('A', 'M') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        // ---------- BORDER ----------
        $lastRow = $row - 1;
        if ($lastRow >= 4) {
            $sheet->getStyle('A4:M' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        // ---------- SIMPAN ----------
        $filename = 'pro-bono-' . date('Ymd-His') . '.xlsx';
        $path = WRITEPATH . 'exports/' . $filename;
        if (!is_dir(dirname($path))) mkdir(dirname($path), 0775, true);

        (new Xlsx($spreadsheet))->save($path);
        return $path;
    }

    private function getNamaPenerimaManfaat(array $p): string
    {
        if (empty($p['penerima_manfaat'])) return '-';

        // Kalau seminar/penyuluhan → pakai nama acara + peserta
        if (($p['jenis_non_litigasi'] ?? null) === 'seminar' || ($p['jenis_non_litigasi'] ?? null) === 'penyuluhan') {
            return ($p['detail']['nama_acara'] ?? '-') . ' (' . ($p['detail']['peserta'] ?? '-') . ')';
        }

        $names = [];
        foreach ($p['penerima_manfaat'] as $pm) {
            if ($pm['kategori'] === 'anak') $names[] = $pm['nama'];
            else $names[] = ($pm['jenis_kelamin'] === 'L' ? 'Tn. ' : 'Ny. ') . '(Dewasa)';
        }
        return implode(', ', array_slice($names, 0, 3)) . (count($names) > 3 ? '...' : '');
    }

    private function getLinkSuratKuasa(array $p): string
    {
        foreach ($p['dokumen'] ?? [] as $d) {
            if ($d['jenis_dokumen'] === 'surat_kuasa') {
                return base_url('file/dokumen/' . $d['id']);
            }
        }
        return '-';
    }
}