<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreatePengajuan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                 => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'no_registrasi'      => ['type' => 'VARCHAR', 'constraint' => 40, 'unique' => true],
            'user_id'            => ['type' => 'BIGINT', 'unsigned' => true],
            'pbh_cabang_id'      => ['type' => 'BIGINT', 'unsigned' => true],
            'jenis_layanan'      => ['type' => 'ENUM', 'constraint' => ['litigasi','non-litigasi']],
            'jenis_non_litigasi' => ['type' => 'ENUM', 'constraint' => ['seminar','penyuluhan','pendampingan'], 'null' => true],
            'tahun'              => ['type' => 'YEAR'],
            'status'             => ['type' => 'ENUM', 'constraint' => ['draft','submitted','review','approved','rejected'], 'default' => 'draft'],
            'catatan_verifikator'=> ['type' => 'TEXT', 'null' => true],
            'verified_by'        => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'verified_at'        => ['type' => 'DATETIME', 'null' => true],
            'draft_data'         => ['type' => 'LONGTEXT', 'null' => true], // JSON untuk auto-save
            'submitted_at'       => ['type' => 'DATETIME', 'null' => true],
            'created_at'         => ['type' => 'DATETIME', 'null' => true],
            'updated_at'         => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'         => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('no_registrasi');
        $this->forge->addKey('user_id');
        $this->forge->addKey('pbh_cabang_id');
        $this->forge->addKey('status');
        $this->forge->addKey('tahun');
        $this->forge->addKey('jenis_layanan');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('pbh_cabang_id', 'pbh_cabang', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pengajuan');
    }

    public function down()
    {
        $this->forge->dropTable('pengajuan');
    }
}