<?php
namespace App\Models;
use CodeIgniter\Model;

class MasterAdvokatModel extends Model
{
    protected $table      = 'master_advokat';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'nia','nama_lengkap','gelar','email','no_wa',
        'pbh_cabang_id','status_advokat','is_registered',
    ];
    protected $useTimestamps = true;

    public function findByNia(string $nia): ?array
    {
        return $this->where('nia', $nia)->first();
    }
}