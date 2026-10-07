<?php
namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nia','nama_lengkap','gelar','email','no_wa','password_hash',
        'role','pbh_cabang_id','status','email_verified_at',
        'last_login_at','last_login_ip','remember_token','two_factor_secret',
    ];
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'nama_lengkap' => 'required|min_length[3]|max_length[150]',
        'role'  => 'required|in_list[advokat,cabang,admin,super_admin]',
        'status'=> 'required|in_list[pending,aktif,nonaktif,suspend]',
    ];

    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    public function getWithCabang(int $id): ?array
    {
        return $this->select('users.*, pbh_cabang.nama as cabang_nama, pbh_cabang.kode as cabang_kode')
                    ->join('pbh_cabang', 'pbh_cabang.id = users.pbh_cabang_id', 'left')
                    ->where('users.id', $id)
                    ->first();
    }

    public function getAdvokatByCabang(int $cabangId): array
    {
        return $this->where('role', 'advokat')
                    ->where('pbh_cabang_id', $cabangId)
                    ->where('status', 'aktif')
                    ->findAll();
    }
}