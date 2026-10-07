<?php
namespace App\Models;
use CodeIgniter\Model;

class FotoKegiatanModel extends Model
{
    protected $table      = 'foto_kegiatan';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'pengajuan_id','file_path','file_name','file_size','caption','urutan',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}