<?php declare(strict_types=1);

namespace PhilippR\Atk4\ModelHandler\Tests;

use Atk4\Data\Model;
use Atk4\Data\Persistence\Sql;
use Atk4\Data\Schema\TestCase;
use PhilippR\Atk4\ModelHandler\Tests\Helpers\SomeController;
use PhilippR\Atk4\ModelHandler\Tests\Helpers\SomeOtherController;
use PhilippR\Atk4\ModelHandler\Tests\Helpers\TestModel;

class ModelHandlerTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        SomeController::registerModelHandlerHooks();
        SomeOtherController::registerModelHandlerHooks();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = new Sql('sqlite::memory:');
        $this->createMigrator(new TestModel($this->db))->create();


        if (isset($_ENV['someCounter'])) {
            unset($_ENV['someCounter']);
        }
        if (isset($_ENV['someOtherCounter'])) {
            unset($_ENV['someOtherCounter']);
        }
    }

    public function testInvokeBeforeSave(): void
    {
        $this->_save(Model::HOOK_BEFORE_SAVE);
    }

    public function testInvokeAfterSave(): void
    {
        $this->_save(Model::HOOK_AFTER_SAVE);
    }

    public function testInvokeBeforeDelete(): void
    {
        $this->_delete(Model::HOOK_BEFORE_DELETE);
    }

    public function testInvokeAfterDelete(): void
    {
        $this->_delete(Model::HOOK_AFTER_DELETE);
    }

    public function testInvokeBeforeInsert(): void
    {
        $this->_insert(Model::HOOK_BEFORE_INSERT);
    }

    public function testInvokeAfterInsert(): void
    {
        $this->_insert(Model::HOOK_AFTER_INSERT);
    }

    public function testInvokeBeforeUpdate(): void
    {
        $this->_update(Model::HOOK_BEFORE_UPDATE);
    }

    public function testInvokeAfterUpdate(): void
    {
        $this->_update(Model::HOOK_AFTER_UPDATE);
    }

    protected function _insert(string $hookSpot): void
    {
        $testModel = new TestModel($this->db);
        $this->callProtected($testModel, 'invokeModelHandler', $hookSpot);
        $entity = $testModel->createEntity()->save();
        self::assertArrayNotHasKey('someCounter', $_ENV);
        self::assertSame(0, $_ENV['someOtherCounter']);

        $entity->set('name', 'SomeName');
        $entity->save();
        self::assertArrayNotHasKey('someCounter', $_ENV);
        //insert should not be triggered again
        self::assertSame(0, $_ENV['someOtherCounter']);
    }

    protected function _update(string $hookSpot): void
    {
        $testModel = new TestModel($this->db);
        $this->callProtected($testModel, 'invokeModelHandler', $hookSpot);
        $entity = $testModel->createEntity()->save();
        //update should not be triggered yet
        self::assertArrayNotHasKey('someCounter', $_ENV);
        self::assertArrayNotHasKey('someOtherCounter', $_ENV);

        $entity->set('name', 'SomeName');
        $entity->save();
        self::assertArrayNotHasKey('someCounter', $_ENV);
        self::assertSame(0, $_ENV['someOtherCounter']);

        $entity->set('name', 'SomeName2');
        $entity->save();
        self::assertArrayNotHasKey('someCounter', $_ENV);
        self::assertSame(1, $_ENV['someOtherCounter']);
    }

    protected function _save(string $hookSpot): void
    {
        $testModel = new TestModel($this->db);
        $this->callProtected($testModel, 'invokeModelHandler', $hookSpot);
        $entity = $testModel->createEntity()->save();
        self::assertSame(0, $_ENV['someCounter']);
        self::assertArrayNotHasKey('someOtherCounter', $_ENV);

        $entity->set('name', 'SomeName');
        $entity->save();
        self::assertSame(1, $_ENV['someCounter']);
        self::assertArrayNotHasKey('someOtherCounter', $_ENV);
    }

    protected function _delete(string $hookSpot): void
    {
        $testModel = new TestModel($this->db);
        $this->callProtected($testModel, 'invokeModelHandler', $hookSpot);
        $entity = $testModel->createEntity()->save();
        self::assertArrayNotHasKey('someCounter', $_ENV);
        self::assertArrayNotHasKey('someOtherCounter', $_ENV);

        $entity->delete();
        self::assertSame(0, $_ENV['someCounter']);
        self::assertArrayNotHasKey('someOtherCounter', $_ENV);
    }
}