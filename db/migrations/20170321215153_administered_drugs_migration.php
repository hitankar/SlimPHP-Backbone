<?php

use Phinx\Migration\AbstractMigration;

class AdministeredDrugsMigration extends AbstractMigration
{
    public function up()
    {
        $administered_drugs_table = $this->table('administered_drugs');
        $administered_drugs_table
            ->addColumn('name', 'string')
            ->addColumn('appointment_id', 'integer', array('null' => false))
            ->addForeignKey('appointment_id', 'appointments', 'id', array('delete'=> 'RESTRICT', 'update'=> 'CASCADE'))
            ->addColumn('created_at', 'timestamp', array( 'default' => 'CURRENT_TIMESTAMP', 'update' => ''))
            ->addColumn('updated_at', 'timestamp', array('null' => true, 'default' => null, 'update' => 'CURRENT_TIMESTAMP'))
            ->create();
    }

    public function down() {
        $this->dropTable('administered_drugs');
    }
}
