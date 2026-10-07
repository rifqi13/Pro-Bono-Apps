<?php
namespace App\Models;
use CodeIgniter\Model;

class EmailVerificationModel extends Model
{
    protected $table      = 'email_verifications';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['user_id','token_hash','expired_at','used_at','ip_address'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}