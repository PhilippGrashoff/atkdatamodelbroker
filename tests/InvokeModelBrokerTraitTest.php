<?php declare(strict_types=1);

namespace PhilippR\Atk4\ModelBroker\Tests;

use Atk4\Data\Model;
use Atk4\Data\Persistence\Sql;
use Atk4\Data\Schema\TestCase;
use PhilippR\Atk4\ModelBroker\Tests\Helpers\TestModel;

class InvokeModelBrokerTraitTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->db = new Sql('sqlite::memory:');
        $this->createMigrator(new TestModel($this->db))->create();
    }

    public function testSameHookSpotCannotBeInvokedTwice(): void
    {
        $testModel = new TestModel($this->db);
        $this->callProtected($testModel, 'publish', Model::HOOK_BEFORE_UPDATE);
        self::expectExceptionMessage('The hook spot is already registered in ModelBroker');
        $this->callProtected($testModel, 'publish', Model::HOOK_BEFORE_UPDATE);
    }

    public function testUnknownHookSpotThrowsException(): void
    {
        $testModel = new TestModel($this->db);
        self::expectExceptionMessage('Unknown Hook spot');
        $this->callProtected($testModel, 'publish', 'someUnknownSpot');
    }
}