<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateActivityLogs extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'      => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'user_role'    => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'action'       => ['type' => 'VARCHAR', 'constraint' => 50],
            'module'       => ['type' => 'VARCHAR', 'constraint' => 50],
            'description'  => ['type' => 'TEXT', 'null' => true],
            'subject_type' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'subject_id'   => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'old_values'   => ['type' => 'JSON', 'null' => true],
            'new_values'   => ['type' => 'JSON', 'null' => true],
            'ip_address'   => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'user_agent'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('action');
        $this->forge->addKey('module');
        $this->forge->addKey('created_at');
        $this->forge->addKey('subject_type');
        $this->forge->createTable('activity_logs');
    }

    public function down()
    {
        $this->forge->dropTable('activity_logs');
    }
}