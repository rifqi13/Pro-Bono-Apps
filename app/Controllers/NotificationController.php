<?php
namespace App\Controllers;

class NotificationController extends BaseController
{
    public function index()
    {
        $notifs = model('NotificationModel')
            ->where('user_id', session()->get('user_id'))
            ->orderBy('created_at', 'DESC')
            ->paginate(20);
        return view('notifications/index', ['notifs' => $notifs, 'pager' => model('NotificationModel')->pager]);
    }

    

    public function markRead(int $id)
{
    $model = model('NotificationModel');
    $userId = session()->get('user_id');

    // Pastikan notifikasi milik user yang login
    $notif = $model->where('id', $id)->where('user_id', $userId)->first();

    if (!$notif) {
        return $this->response->setStatusCode(404)->setJSON([
            'success' => false,
            'message' => 'Notifikasi tidak ditemukan.'
        ]);
    }

    $model->update($id, [
        'is_read' => 1,
        'read_at' => date('Y-m-d H:i:s'),
    ]);

    return $this->response->setJSON([
        'success' => true,
        'unread'  => $model->countUnread($userId),   // kirim sisa unread
    ]);
}
}