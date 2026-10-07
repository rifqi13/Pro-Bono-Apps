<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateDetailLitigasi extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'pengajuan_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'jenis_perkara'=> ['type' => 'VARCHAR', 'constraint' => 50],
            'no_perkara'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'pengadilan'   => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('pengajuan_id', true);
        $this->forge->addForeignKey('pengajuan_id', 'pengajuan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_litigasi');
    }

    public function down()
    {
        $this->forge->dropTable('detail_litigasi');
    }
}