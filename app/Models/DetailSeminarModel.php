<?php
namespace App\Models;
use CodeIgniter\Model;

class DetailSeminarModel extends Model
{
    protected $table      = 'detail_seminar';
    protected $primaryKey = 'pengajuan_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'pengajuan_id','nama_acara','tanggal_acara','waktu_acara',
        'tempat_acara','peserta','jumlah_peserta',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}