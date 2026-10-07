<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateFotoKegiatan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'pengajuan_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'file_path'    => ['type' => 'VARCHAR', 'constraint' => 255],
            'file_name'    => ['type' => 'VARCHAR', 'constraint' => 255],
            'file_size'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'caption'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'urutan'       => ['type' => 'INT', 'unsigned' => true, 'default' => 1],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('pengajuan_id');
        $this->forge->addForeignKey('pengajuan_id', 'pengajuan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('foto_kegiatan');
    }

    public function down()
    {
        $this->forge->dropTable('foto_kegiatan');
    }
}