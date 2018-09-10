<?php

use Phinx\Migration\AbstractMigration;

class DiscoveredSymptomsMigration extends AbstractMigration
{
    public function up()
    {
        $discovered_symptoms_table = $this->table('discovered_symptoms');
        $discovered_symptoms_table
            ->addColumn('name', 'string')
            ->addColumn('appointment_id', 'integer', array('null' => false))
            ->addForeignKey('appointment_id', 'appointments', 'id', array('delete'=> 'RESTRICT', 'update'=> 'CASCADE'))
            ->addColumn('created_at', 'timestamp', array( 'default' => 'CURRENT_TIMESTAMP', 'update' => ''))
            ->addColumn('updated_at', 'timestamp', array('null' => true, 'default' => null, 'update' => 'CURRENT_TIMESTAMP'))
            ->create();
    }

    public function down() {
        $this->dropTable('discovered_symptoms');
    }
}
