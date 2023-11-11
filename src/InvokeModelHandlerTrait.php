<?php declare(strict_types=1);

namespace PhilippR\Atk4\ModelHandler;

use Atk4\Data\Exception;
use Atk4\Data\Model;

/**
 * @extends Model<Model>
 */
trait InvokeModelHandlerTrait
{

    protected array $invokedHookSpots = [];

    protected function assertHookSpotNotInvoked(string $hookSpot): void
    {
        if (in_array($hookSpot, $this->invokedHookSpots)) {
            throw (new Exception('The hook spot is already registered in ModelHandler'))
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
     */
    protected function invokeModelHandler(string $hookSpot): void
    {
        match ($hookSpot) {
            Model::HOOK_BEFORE_SAVE => $this->invokeSave(Model::HOOK_BEFORE_SAVE, 'beforeSave'),
            Model::HOOK_AFTER_SAVE => $this->invokeSave(Model::HOOK_AFTER_SAVE, 'afterSave'),
            Model::HOOK_BEFORE_INSERT => $this->invokeBeforeInsertOrUpdate(Model::HOOK_BEFORE_INSERT, 'beforeInsert'),
            Model::HOOK_AFTER_INSERT => $this->invokeAfterInsertOrUpdateOrDelete(
                Model::HOOK_AFTER_INSERT,
                'afterInsert'
            ),
            Model::HOOK_BEFORE_UPDATE => $this->invokeBeforeInsertOrUpdate(Model::HOOK_BEFORE_UPDATE, 'beforeUpdate'),
            Model::HOOK_AFTER_UPDATE => $this->invokeAfterInsertOrUpdateOrDelete(
                Model::HOOK_AFTER_UPDATE,
                'afterUpdate'
            ),
            Model::HOOK_BEFORE_DELETE => $this->invokeAfterInsertOrUpdateOrDelete(
                Model::HOOK_BEFORE_DELETE,
                'beforeDelete'
            ),
            Model::HOOK_AFTER_DELETE => $this->invokeAfterInsertOrUpdateOrDelete(
                Model::HOOK_AFTER_DELETE,
                'afterDelete'
            ),
            default => throw new Exception('Unknown Hook spot ' . $hookSpot)
        };
    }

    private function invokeSave(string $hookSpot, string $functionName): void
    {
        $this->assertHookSpotNotInvoked($hookSpot);
        $this->onHook(
            $hookSpot,
            function (Model $entity, bool $isUpdate) use ($functionName) {
                ModelHandler::getInstance()->$functionName($entity, $isUpdate);
            }
        );
        $this->addInvokedHookSpot($hookSpot);
    }

    private function invokeBeforeInsertOrUpdate(string $hookSpot, string $functionName): void
    {
        $this->assertHookSpotNotInvoked($hookSpot);
        $this->onHook(
            $hookSpot,
            function (Model $entity, array &$data) use ($functionName) {
                ModelHandler::getInstance()->$functionName($entity, $data);
            }
        );
        $this->addInvokedHookSpot($hookSpot);
    }

    private function invokeAfterInsertOrUpdateOrDelete(string $hookSpot, string $functionName): void
    {
        $this->assertHookSpotNotInvoked($hookSpot);
        $this->onHook(
            $hookSpot,
            function (Model $entity) use ($functionName) {
                ModelHandler::getInstance()->$functionName($entity);
            }
        );
        $this->addInvokedHookSpot($hookSpot);
    }
}
