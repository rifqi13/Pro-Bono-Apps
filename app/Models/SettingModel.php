<?php
namespace App\Models;
use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table      = 'settings';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['key','value','type','group','label'];
    protected $useTimestamps = true;
    protected $createdField  = '';
    protected $updatedField  = 'updated_at';

    public static function get(string $key, $default = null)
    {
        $row = (new self())->where('key', $key)->first();
        if (!$row) return $default;
        return match ($row['type']) {
            'int'  => (int) $row['value'],
            'bool' => (bool) $row['value'],
            'json' => json_decode($row['value'], true),
            default => $row['value'],
        };
    }

    public static function set(string $key, $value): void
    {
        $model = new self();
        $row = $model->where('key', $key)->first();
        $model->update($row['id'] ?? null, ['value' => is_array($value) ? json_encode($value) : (string) $value]);
    }

    public static function getGroup(string $group): array
    {
        return (new self())->where('group', $group)->findAll();
    }
}