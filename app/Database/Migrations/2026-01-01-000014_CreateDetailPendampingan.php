<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateDetailPendampingan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'pengajuan_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'lokasi'       => ['type' => 'VARCHAR', 'constraint' => 200],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('pengajuan_id', true);
        $this->forge->addForeignKey('pengajuan_id', 'pengajuan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_pendampingan');
    }

    public function down()
    {
        $this->forge->dropTable('detail_pendampingan');
    }
}