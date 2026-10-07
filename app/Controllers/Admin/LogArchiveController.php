<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\ActivityLogger;
use App\Libraries\LogArchiver;
use App\Models\ActivityLogArchiveModel;

class LogArchiveController extends BaseController
{
    public function index()
    {
        $model = new ActivityLogArchiveModel();
        $filters = [
            'action'  => $this->request->getGet('action'),
            'module'  => $this->request->getGet('module'),
            'dari'    => $this->request->getGet('dari'),
            'sampai'  => $this->request->getGet('sampai'),
            'q'       => $this->request->getGet('q'),
        ];

        $logs = $model->getFiltered($filters, 50);

        return view('admin/log_archive', [
            'title'   => 'Arsip Log Aktivitas',
            'logs'    => $logs,
            'pager'   => $model->pager,
            'filters' => $filters,
            'total'   => $model->countAll(),
        ]);
    }

    public function download()
    {
        $model = new ActivityLogArchiveModel();
        $logs = $model->orderBy('created_at', 'DESC')->findAll();
        $path = LogArchiver::exportCsv($logs, 'log-archive-' . date('Ymd-His') . '.csv');

        ActivityLogger::log('log_archive_download', 'admin',
            "Download arsip log: " . count($logs) . " baris");

        return $this->response->download($path, null)->setFileName(basename($path));
    }
}