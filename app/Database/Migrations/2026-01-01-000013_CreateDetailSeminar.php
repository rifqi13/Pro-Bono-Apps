<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateDetailSeminar extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'pengajuan_id'  => ['type' => 'BIGINT', 'unsigned' => true],
            'nama_acara'    => ['type' => 'VARCHAR', 'constraint' => 200],
            'tanggal_acara' => ['type' => 'DATE'],
            'waktu_acara'   => ['type' => 'TIME'],
            'tempat_acara'  => ['type' => 'VARCHAR', 'constraint' => 200],
            'peserta'       => ['type' => 'VARCHAR', 'constraint' => 200],
            'jumlah_peserta'=> ['type' => 'INT', 'unsigned' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('pengajuan_id', true);
        $this->forge->addForeignKey('pengajuan_id', 'pengajuan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_seminar');
    }

    public function down()
    {
        $this->forge->dropTable('detail_seminar');
    }
}