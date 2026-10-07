<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateUsers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'nia'               => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'nama_lengkap'      => ['type' => 'VARCHAR', 'constraint' => 150],
            'gelar'             => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'email'             => ['type' => 'VARCHAR', 'constraint' => 150, 'unique' => true],
            'no_wa'             => ['type' => 'VARCHAR', 'constraint' => 20],
            'password_hash'     => ['type' => 'VARCHAR', 'constraint' => 255],
            'role'              => ['type' => 'ENUM', 'constraint' => ['advokat','cabang','admin','super_admin']],
            'pbh_cabang_id'     => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'status'            => ['type' => 'ENUM', 'constraint' => ['pending','aktif','nonaktif','suspend'], 'default' => 'pending'],
            'email_verified_at' => ['type' => 'DATETIME', 'null' => true],
            'last_login_at'     => ['type' => 'DATETIME', 'null' => true],
            'last_login_ip'     => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'remember_token'    => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'two_factor_secret' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('role');
        $this->forge->addKey('status');
        $this->forge->addKey('pbh_cabang_id');
        $this->forge->addForeignKey('pbh_cabang_id', 'pbh_cabang', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}