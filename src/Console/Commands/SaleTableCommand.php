<?php

namespace Viviniko\Sale\Console\Commands;

use Viviniko\Support\Console\CreateMigrationCommand;

class SaleTableCommand extends CreateMigrationCommand
{
    /**
     * @var string
     */
    protected $name = 'sale:table';

    /**
     * @var string
     */
    protected $description = 'Create a migration for the sale service table';

    /**
     * @var string
     */
    protected $stub = __DIR__.'/stubs/sale.stub';

    /**
     * @var string
     */
    protected $migration = 'create_sale_table';
}
