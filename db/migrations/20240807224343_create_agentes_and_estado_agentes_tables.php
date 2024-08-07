<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateAgentesAndEstadoAgentesTables extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        // Crear la tabla estado_agentes
        $table = $this->table('estado_agentes');
        $table->addColumn('estado', 'string', ['limit' => 50])
              ->create();

        // Insertar los estados iniciales en la tabla estado_agentes
        $this->execute("
            INSERT INTO estado_agentes (estado) VALUES ('Actividad');
            INSERT INTO estado_agentes (estado) VALUES ('Retirado');
        ");

        // Crear la tabla agentes
        $this->table('agentes')
            ->addColumn('cuil', 'string', ['limit' => 20])
            ->addColumn('credencial', 'string', ['limit' => 50])
            ->addColumn('apellido', 'string', ['limit' => 50])
            ->addColumn('nombre', 'string', ['limit' => 50])
            ->addColumn('estado_id', 'integer', ['signed' => false]) // Clave foránea no firmada
            ->addForeignKey('estado_id', 'estado_agentes', 'id', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])
            ->create();
    }
}
