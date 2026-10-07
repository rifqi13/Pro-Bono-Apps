<?php
namespace App\Models;
use CodeIgniter\Model;

class DetailPendampinganModel extends Model
{
    protected $table      = 'detail_pendampingan';
    protected $primaryKey = 'pengajuan_id';
    protected $returnType = 'array';
    protected $allowedFields = ['pengajuan_id','lokasi'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}