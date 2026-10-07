<?php
namespace App\Models;
use CodeIgniter\Model;

class ActivityLogArchiveModel extends Model
{
    protected $table      = 'activity_logs_archive';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'original_id','user_id','user_role','action','module','description',
        'subject_type','subject_id','old_values','new_values',
        'ip_address','user_agent','archived_at','created_at',
    ];
    protected $useTimestamps = false;

    public function getFiltered(array $filters, int $perPage = 50)
    {
        $builder = $this->orderBy('created_at', 'DESC');
        if (!empty($filters['action']))  $builder->where('action', $filters['action']);
        if (!empty($filters['module']))  $builder->where('module', $filters['module']);
        if (!empty($filters['user_id'])) $builder->where('user_id', $filters['user_id']);
        if (!empty($filters['q']))       $builder->like('description', $filters['q']);
        if (!empty($filters['dari']))    $builder->where('created_at >=', $filters['dari'] . ' 00:00:00');
        if (!empty($filters['sampai']))  $builder->where('created_at <=', $filters['sampai'] . ' 23:59:59');
        return $builder->paginate($perPage);
    }

    public function countAll(): int
    {
        return $this->countAllResults();
    }
}