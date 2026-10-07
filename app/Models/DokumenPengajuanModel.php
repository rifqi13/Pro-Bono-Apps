<?php
namespace App\Models;
use CodeIgniter\Model;

class DokumenPengajuanModel extends Model
{
    protected $table      = 'dokumen_pengajuan';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'pengajuan_id','jenis_dokumen','file_path','file_name','file_size','mime_type','is_encrypted',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}