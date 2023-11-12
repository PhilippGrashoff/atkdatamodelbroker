<?php declare(strict_types=1);

namespace PhilippR\Atk4\ModelBroker;

use Atk4\Data\Exception;
use Atk4\Data\Model;

/**
 * @extends Model<Model>
 */
trait InvokeModelBrokerTrait
{
    /** @var String[] $invokedHookSpots */
    protected array $invokedHookSpots = [];

    protected function assertHookSpotNotInvoked(string $hookSpot): void
    {
        if (in_array($hookSpot, $this->invokedHookSpots)) {
            throw (new Exception('The hook spot is already registered in ModelBroker'))
                ->addMoreInfo('hook spot', $hookSpot);
        }
    }

    protected function addInvokedHookSpot(string $hookSpot): void
    {
        $this->invokedHookSpots[] = $hookSpot;
    }

    /**
     * Call this function in Model::init() to invoke a handler on a save or delete spot
     *
     * @param string $hookSpot
     * @return void
     * @throws Exception
     * @throws \Atk4\Core\Exception
     */
    protected function publish(string $hookSpot): void
    {
        match ($hookSpot) {
            Model::HOOK_BEFORE_SAVE => $this->addSaveOnHook(Model::HOOK_BEFORE_SAVE, 'beforeSave'),
            Model::HOOK_AFTER_SAVE => $this->addSaveOnHook(Model::HOOK_AFTER_SAVE, 'afterSave'),
            Model::HOOK_BEFORE_INSERT => $this->addBeforeInsertOrUpdateOnHook(Model::HOOK_BEFORE_INSERT, 'beforeInsert'),
            Model::HOOK_AFTER_INSERT => $this->addInsertUpdateDeleteOnHook(Model::HOOK_AFTER_INSERT, 'afterInsert'),
            Model::HOOK_BEFORE_UPDATE => $this->addBeforeInsertOrUpdateOnHook(Model::HOOK_BEFORE_UPDATE, 'beforeUpdate'),
            Model::HOOK_AFTER_UPDATE => $this->addInsertUpdateDeleteOnHook(Model::HOOK_AFTER_UPDATE, 'afterUpdate'),
            Model::HOOK_BEFORE_DELETE => $this->addInsertUpdateDeleteOnHook(Model::HOOK_BEFORE_DELETE, 'beforeDelete'),
            Model::HOOK_AFTER_DELETE => $this->addInsertUpdateDeleteOnHook(Model::HOOK_AFTER_DELETE, 'afterDelete'),
            default => throw (new Exception('Unknown Hook spot'))->addMoreInfo('hook spot', $hookSpot)
        };
    }

    private function addSaveOnHook(string $hookSpot, string $functionName): void
    {
        $this->assertHookSpotNotInvoked($hookSpot);
        $this->onHook(
            $hookSpot,
            function (Model $entity, bool $isUpdate) use ($functionName) {
                ModelBroker::getInstance()->$functionName($entity, $isUpdate);
            }
        );
        $this->addInvokedHookSpot($hookSpot);
    }

    private function addBeforeInsertOrUpdateOnHook(string $hookSpot, string $functionName): void
    {
        $this->assertHookSpotNotInvoked($hookSpot);
        $this->onHook(
            $hookSpot,
            function (Model $entity, array &$data) use ($functionName) {
                ModelBroker::getInstance()->$functionName($entity, $data);
            }
        );
        $this->addInvokedHookSpot($hookSpot);
    }

    private function addInsertUpdateDeleteOnHook(string $hookSpot, string $functionName): void
    {
        $this->assertHookSpotNotInvoked($hookSpot);
        $this->onHook(
            $hookSpot,
            function (Model $entity) use ($functionName) {
                ModelBroker::getInstance()->$functionName($entity);
            }
        );
        $this->addInvokedHookSpot($hookSpot);
    }
}
