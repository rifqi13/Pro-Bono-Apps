<?php
namespace App\Models;
use CodeIgniter\Model;

class DetailLitigasiModel extends Model
{
    protected $table      = 'detail_litigasi';
    protected $primaryKey = 'pengajuan_id';
    protected $returnType = 'array';
    protected $allowedFields = ['pengajuan_id','jenis_perkara','no_perkara','pengadilan'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}