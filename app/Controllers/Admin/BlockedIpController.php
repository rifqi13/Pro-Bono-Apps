<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\ActivityLogger;
use App\Libraries\IpBlocker;
use App\Models\BlockedIpModel;

class BlockedIpController extends BaseController
{
    public function index()
    {
        $blocked = (new BlockedIpModel())->getActive(50);
        $whitelist = model('IpWhitelistModel')->orderBy('created_at', 'DESC')->paginate(20);

        return view('admin/blocked_ip', [
            'title'     => 'Manajemen IP',
            'blocked'   => $blocked,
            'pager'     => model('BlockedIpModel')->pager,
            'whitelist' => $whitelist,
            'pagerW'    => model('IpWhitelistModel')->pager,
        ]);
    }

    public function block()
    {
        $ip = trim($this->request->getPost('ip_address'));
        $reason = trim($this->request->getPost('reason') ?? '');
        $expireHours = (int) $this->request->getPost('expire_hours') ?: null;

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return redirect()->back()->with('error', 'Format IP tidak valid.');
        }
        if (empty($reason)) {
            return redirect()->back()->with('error', 'Alasan wajib diisi.');
        }

        IpBlocker::block($ip, $reason, session()->get('user_id'), $expireHours);

        return redirect()->back()->with('success', "IP {$ip} berhasil diblokir.");
    }

    public function unblock()
    {
        $ip = trim($this->request->getPost('ip_address'));
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return redirect()->back()->with('error', 'Format IP tidak valid.');
        }
        IpBlocker::unblock($ip, session()->get('user_id'));
        return redirect()->back()->with('success', "IP {$ip} berhasil di-unblock.");
    }

    public function whitelist()
    {
        $ip = trim($this->request->getPost('ip_address'));
        $ket = trim($this->request->getPost('keterangan') ?? '');

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return redirect()->back()->with('error', 'Format IP tidak valid.');
        }

        IpBlocker::whitelist($ip, $ket, session()->get('user_id'));
        return redirect()->back()->with('success', "IP {$ip} ditambahkan ke whitelist.");
    }

    public function removeWhitelist(int $id)
    {
        $row = model('IpWhitelistModel')->find($id);
        if ($row) {
            model('IpWhitelistModel')->delete($id);
            ActivityLogger::log('ip_whitelist_remove', 'security',
                "IP {$row['ip_address']} dihapus dari whitelist");
        }
        return redirect()->back()->with('success', 'IP dihapus dari whitelist.');
    }
}