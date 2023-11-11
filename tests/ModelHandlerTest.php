<?php declare(strict_types=1);

namespace PhilippR\Atk4\ModelHandler\Tests;

use Atk4\Data\Persistence\Sql;
use Atk4\Data\Schema\TestCase;
use PhilippR\Atk4\ModelHandler\Tests\Helpers\TestModel;

class ModelHandlerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->db = new Sql('sqlite::memory:');
        $this->createMigrator(new TestModel($this->db))->create();
    }

}