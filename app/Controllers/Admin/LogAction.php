<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\ActivityLogModel;

class LogAction extends BaseController
{
    public function index()
    {
        $model = new ActivityLogModel();
        $filters = [
            'action' => $this->request->getGet('action'),
            'module' => $this->request->getGet('module'),
            'user_id'=> $this->request->getGet('user_id'),
            'dari'   => $this->request->getGet('dari'),
            'sampai' => $this->request->getGet('sampai'),
        ];

        $builder = $model->orderBy('created_at', 'DESC');
        if ($filters['action'])  $builder->where('action', $filters['action']);
        if ($filters['module'])  $builder->where('module', $filters['module']);
        if ($filters['user_id']) $builder->where('user_id', $filters['user_id']);
        if ($filters['dari'])    $builder->where('created_at >=', $filters['dari'] . ' 00:00:00');
        if ($filters['sampai'])  $builder->where('created_at <=', $filters['sampai'] . ' 23:59:59');

        $data = [
            'logs'    => $builder->paginate(50),
            'pager'   => $model->pager,
            'filters' => $filters,
        ];
        return view('admin/log_action', $data);
    }

    public function export()
    {
        // Export CSV — hanya admin
        $model = new ActivityLogModel();
        $logs = $model->orderBy('created_at', 'DESC')->limit(10000)->findAll();

        $filename = 'log-action-' . date('Ymd-His') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID','Waktu','User','Role','Action','Module','Deskripsi','IP']);
        foreach ($logs as $l) {
            fputcsv($out, [$l['id'],$l['created_at'],$l['user_id'],$l['user_role'],$l['action'],$l['module'],$l['description'],$l['ip_address']]);
        }
        fclose($out);

        \App\Libraries\ActivityLogger::log('export_log', 'admin', 'Export log action');
        exit;
    }
}