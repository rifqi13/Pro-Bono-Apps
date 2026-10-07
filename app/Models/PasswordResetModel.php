<?php
namespace App\Models;
use CodeIgniter\Model;

class PasswordResetModel extends Model
{
    protected $table      = 'password_resets';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['email','token_hash','expired_at','used_at','ip_address'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}