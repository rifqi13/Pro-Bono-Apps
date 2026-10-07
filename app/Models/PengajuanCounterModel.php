<?php
namespace App\Models;
use CodeIgniter\Model;

class PengajuanCounterModel extends Model
{
    protected $table      = 'pengajuan_counter';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['tahun','pbh_cabang_id','last_number'];
    protected $useTimestamps = true;
    protected $createdField  = '';
    protected $updatedField  = 'updated_at';
}