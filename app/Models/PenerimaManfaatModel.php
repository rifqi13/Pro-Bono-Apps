<?php
namespace App\Models;
use CodeIgniter\Model;

class PenerimaManfaatModel extends Model
{
    protected $table      = 'penerima_manfaat';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'pengajuan_id','usia','kategori','nama','jenis_kelamin','pekerjaan','file_ktp',
    ];
    protected $useTimestamps = true;
}