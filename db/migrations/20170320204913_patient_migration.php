<?php

use Phinx\Migration\AbstractMigration;

class PatientMigration extends AbstractMigration
{
    public function up()
    {
        $patients_table = $this->table('patients');
        $patients_table->addColumn('name', 'string')
            ->addColumn('address', 'text')
            ->addColumn('age', 'string')
            ->create();
    }

    public function down() {
        $this->dropTable('patients');
    }
}
