<?php
namespace App\Models;
use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table      = 'notifications';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_id','title','message','type','icon','link','is_read','read_at',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public function getUnread(int $userId, int $limit = 10): array
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    public function countUnread(int $userId): int
{
    return $this->where('user_id', $userId)
                ->where('is_read', 0)
                ->countAllResults();
}

    public function markAsRead(int $id, int $userId): bool
    {
        return (bool) $this->where('id', $id)
                          ->where('user_id', $userId)
                          ->set(['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')])
                          ->update();
    }

    public function markAllRead(int $userId): bool
    {
        return (bool) $this->where('user_id', $userId)
                          ->where('is_read', 0)
                          ->set(['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')])
                          ->update();
    }

    /**
     * Helper statis untuk kirim notifikasi dari mana saja.
     */
    public static function push(
        int $userId,
        string $title,
        string $message,
        string $type = 'info',
        ?string $link = null,
        ?string $icon = null
    ): int {
        $model = new self();
        return $model->insert([
            'user_id' => $userId,
            'title'   => $title,
            'message' => $message,
            'type'    => $type,
            'icon'    => $icon ?? self::defaultIcon($type),
            'link'    => $link,
        ], true);
    }

    private static function defaultIcon(string $type): string
    {
        return match ($type) {
            'success' => 'bi-check-circle',
            'warning' => 'bi-exclamation-triangle',
            'danger'  => 'bi-x-circle',
            default   => 'bi-info-circle',
        };
    }
}