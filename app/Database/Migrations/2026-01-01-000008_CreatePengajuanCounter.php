<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreatePengajuanCounter extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'tahun'         => ['type' => 'YEAR'],
            'pbh_cabang_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'last_number'   => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['tahun', 'pbh_cabang_id']);
        $this->forge->addForeignKey('pbh_cabang_id', 'pbh_cabang', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pengajuan_counter');
    }

    public function down()
    {
        $this->forge->dropTable('pengajuan_counter');
    }
}