<?php
namespace App\Controllers;

class PublicController extends BaseController
{
    public function index()
    {
        // Kalau sudah login → redirect ke dashboard sesuai role
        if (is_logged_in()) {
            return redirect()->to(dashboard_url());
        }

        // Statistik dari DB (real-time)
        $db = \Config\Database::connect();

        $stats = [
            'total_cabang'    => $db->table('pbh_cabang')->where('is_active', 1)->countAllResults(),
            'total_advokat'   => $db->table('users')->where('role', 'advokat')->where('status', 'aktif')->countAllResults(),
            'total_pengajuan' => $db->table('pengajuan')->where('status !=', 'draft')->countAllResults(),
            'total_approved'  => $db->table('pengajuan')->where('status', 'approved')->countAllResults(),
        ];

        return view('public/index', [
            'title' => 'Pendataan Data Pro Bono — Advokat PERADI',
            'stats' => $stats,
        ]);
    }

    public function caraKerja()
    {
        if (is_logged_in()) {
            return redirect()->to(dashboard_url());
        }
        return view('public/cara_kerja', ['title' => 'Cara Kerja']);
    }

    public function faq()
    {
        if (is_logged_in()) {
            return redirect()->to(dashboard_url());
        }
        return view('public/faq', ['title' => 'FAQ']);
    }

    public function apiStatistik()
    {
        // API ini tidak perlu redirect, biarkan publik
        $db = \Config\Database::connect();
        return $this->response->setJSON([
            'cabang'    => $db->table('pbh_cabang')->where('is_active', 1)->countAllResults(),
            'advokat'   => $db->table('users')->where('role', 'advokat')->where('status', 'aktif')->countAllResults(),
            'perkara'   => $db->table('pengajuan')->where('status !=', 'draft')->countAllResults(),
            'sertifikat'=> $db->table('pengajuan')->where('status', 'approved')->countAllResults(),
        ]);
    }
}