<?php declare(strict_types=1);


use Atk4\Data\Model;
use Atk4\Data\Persistence\Sql;
use Atk4\Data\Schema\TestCase;
use PhilippR\Atk4\ModelHandler\Tests\Helpers\TestModel;

class InvokeModelHandlerTraitTest extends TestCase
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
        $this->callProtected($testModel, 'invokeModelHandler', Model::HOOK_BEFORE_UPDATE);
        self::expectExceptionMessage('The hook spot is already registered in ModelHandler');
        $this->callProtected($testModel, 'invokeModelHandler', Model::HOOK_BEFORE_UPDATE);
    }

    public function testUnknownHookSpotThrowsException(): void
    {
        $testModel = new TestModel($this->db);
        self::expectExceptionMessage('Unknown Hook spot');
        $this->callProtected($testModel, 'invokeModelHandler', 'someUnknownSpot');
    }
}