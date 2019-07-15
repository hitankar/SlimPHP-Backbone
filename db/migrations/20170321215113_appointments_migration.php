<?php

use Phinx\Migration\AbstractMigration;

class AppointmentsMigration extends AbstractMigration
{
    public function up()
    {
        $appointments_table = $this->table('appointments');
        $appointments_table
            ->addColumn('next_appointment', 'date')
            ->addColumn('patient_id', 'integer', array('null' => false))
            ->addColumn('created_at', 'timestamp', array( 'default' => 'CURRENT_TIMESTAMP', 'update' => ''))
            ->addColumn('updated_at', 'timestamp', array('null' => true, 'default' => null, 'update' => 'CURRENT_TIMESTAMP'))
            ->addForeignKey('patient_id', 'patients', 'id', array('delete'=> 'CASCADE', 'update'=> 'NO_ACTION'))
            ->create();
    }

    public function down() {
        $this->dropTable('appointments');
    }
}
