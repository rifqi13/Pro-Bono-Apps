<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreatePenerimaManfaat extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'pengajuan_id'  => ['type' => 'BIGINT', 'unsigned' => true],
            'usia'          => ['type' => 'INT', 'unsigned' => true],
            'kategori'      => ['type' => 'ENUM', 'constraint' => ['anak','dewasa']],
            'nama'          => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'jenis_kelamin' => ['type' => 'ENUM', 'constraint' => ['L','P'], 'null' => true],
            'pekerjaan'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'file_ktp'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('pengajuan_id');
        $this->forge->addForeignKey('pengajuan_id', 'pengajuan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('penerima_manfaat');
    }

    public function down()
    {
        $this->forge->dropTable('penerima_manfaat');
    }
}