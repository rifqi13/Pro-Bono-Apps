<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateLoginAttempts extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'email'       => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'ip_address'  => ['type' => 'VARCHAR', 'constraint' => 45],
            'user_agent'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'success'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'attempted_at' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('email');
        $this->forge->addKey('ip_address');
        $this->forge->addKey('attempted_at');
        $this->forge->addKey('success');
        $this->forge->createTable('login_attempts');
    }

    public function down()
    {
        $this->forge->dropTable('login_attempts');
    }
}