<?php
namespace App\Libraries;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfExporter
{
    public function export(array $pengajuan, array $options = []): string
    {
        $options_dompdf = new Options();
        $options_dompdf->set('isRemoteEnabled', true);
        $options_dompdf->set('isHtml5ParserEnabled', true);
        $options_dompdf->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options_dompdf);

        $html = view('admin/export_pdf', [
            'pengajuan' => $pengajuan,
            'header1'   => SettingModel::get('export_pdf_header_1', 'DEWAN PIMPINAN NASIONAL'),
            'header2'   => SettingModel::get('export_pdf_header_2', 'PERSATUAN ADVOKAT INDONESIA (PERADI)'),
            'header3'   => SettingModel::get('export_pdf_header_3', 'PUSAT BANTUAN HUKUM'),
            'alamat'    => SettingModel::get('export_pdf_alamat', ''),
            'footer'    => SettingModel::get('export_pdf_footer', ''),
            'periode'   => $options['periode'] ?? 'Semua Periode',
            'judul'     => $options['judul'] ?? 'Laporan Data Pro Bono',
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'pro-bono-' . date('Ymd-His') . '.pdf';
        $path = WRITEPATH . 'exports/' . $filename;
        if (!is_dir(dirname($path))) mkdir(dirname($path), 0775, true);

        file_put_contents($path, $dompdf->output());
        return $path;
    }
}