<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateMasterAdvokat extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'nia'            => ['type' => 'VARCHAR', 'constraint' => 10, 'unique' => true],
            'nama_lengkap'   => ['type' => 'VARCHAR', 'constraint' => 150],
            'gelar'          => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'email'          => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'no_wa'          => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'pbh_cabang_id'  => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'status_advokat' => ['type' => 'ENUM', 'constraint' => ['aktif','nonaktif','suspend'], 'default' => 'aktif'],
            'is_registered'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('nia');
        $this->forge->addKey('pbh_cabang_id');
        $this->forge->addForeignKey('pbh_cabang_id', 'pbh_cabang', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('master_advokat');
    }

    public function down()
    {
        $this->forge->dropTable('master_advokat');
    }
}