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
            Model::HOOK_BEFORE_SAVE => $this->addSaveOnHook(Model::HOOK_BEFORE_SAVE),
            Model::HOOK_AFTER_SAVE => $this->addSaveOnHook(Model::HOOK_AFTER_SAVE),
            Model::HOOK_BEFORE_INSERT => $this->addBeforeInsertOrUpdateOnHook(Model::HOOK_BEFORE_INSERT),
            Model::HOOK_AFTER_INSERT => $this->addInsertUpdateDeleteOnHook(Model::HOOK_AFTER_INSERT),
            Model::HOOK_BEFORE_UPDATE => $this->addBeforeInsertOrUpdateOnHook(Model::HOOK_BEFORE_UPDATE),
            Model::HOOK_AFTER_UPDATE => $this->addInsertUpdateDeleteOnHook(Model::HOOK_AFTER_UPDATE),
            Model::HOOK_BEFORE_DELETE => $this->addInsertUpdateDeleteOnHook(Model::HOOK_BEFORE_DELETE),
            Model::HOOK_AFTER_DELETE => $this->addInsertUpdateDeleteOnHook(Model::HOOK_AFTER_DELETE),
            default => throw (new Exception('Unknown Hook spot'))->addMoreInfo('hook spot', $hookSpot)
        };
    }

    private function addSaveOnHook(string $hookSpot): void
    {
        $this->assertHookSpotNotInvoked($hookSpot);
        $this->onHook(
            $hookSpot,
            function (Model $entity, bool $isUpdate) use ($hookSpot) {
                ModelBroker::getInstance()->hook($hookSpot, [$entity, $isUpdate]);
            }
        );
        $this->addInvokedHookSpot($hookSpot);
    }

    private function addBeforeInsertOrUpdateOnHook(string $hookSpot): void
    {
        $this->assertHookSpotNotInvoked($hookSpot);
        $this->onHook(
            $hookSpot,
            function (Model $entity, array &$data) use ($hookSpot) {
                ModelBroker::getInstance()->hook($hookSpot, [$entity, $data]);
            }
        );
        $this->addInvokedHookSpot($hookSpot);
    }

    private function addInsertUpdateDeleteOnHook(string $hookSpot): void
    {
        $this->assertHookSpotNotInvoked($hookSpot);
        $this->onHook(
            $hookSpot,
            function (Model $entity) use ($hookSpot) {
                ModelBroker::getInstance()->hook($hookSpot, [$entity]);
            }
        );
        $this->addInvokedHookSpot($hookSpot);
    }
}
