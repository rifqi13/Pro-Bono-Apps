<?php
namespace App\Models;
use CodeIgniter\Model;

class PbhCabangModel extends Model
{
    protected $table      = 'pbh_cabang';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'kode','nama','tipe','alamat','telepon','email','is_active',
    ];
    protected $useTimestamps = true;

    public function getDpcList(): array
    {
        return $this->where('tipe', 'DPC')->where('is_active', 1)->findAll();
    }

    public function findByKode(string $kode): ?array
    {
        return $this->where('kode', $kode)->first();
    }
}