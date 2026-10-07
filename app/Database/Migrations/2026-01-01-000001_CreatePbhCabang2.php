<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreatePbhCabang2 extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'kode'       => ['type' => 'VARCHAR', 'constraint' => 20, 'unique' => true],
            'nama'       => ['type' => 'VARCHAR', 'constraint' => 150],
            'tipe'       => ['type' => 'ENUM', 'constraint' => ['DPN','DPC'], 'default' => 'DPC'],
            'alamat'     => ['type' => 'TEXT', 'null' => true],
            'telepon'    => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('kode');
        $this->forge->addKey('tipe');
        $this->forge->createTable('pbh_cabang_2');
    }

    public function down()
    {
        $this->forge->dropTable('pbh_cabang_2');
    }
}