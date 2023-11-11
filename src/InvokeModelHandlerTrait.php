<?php declare(strict_types=1);

namespace PhilippR\Atk4\ModelHandler;

use Atk4\Data\Exception;
use Atk4\Data\Model;

/**
 * @extends <Model|Model>
 */
trait InvokeModelHandlerTrait
{

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
            Model::HOOK_BEFORE_SAVE => $this->invokeBeforeSave(),
            Model::HOOK_AFTER_SAVE => $this->invokeAfterSave(),
            Model::HOOK_BEFORE_INSERT => $this->invokeBeforeInsert(),
            Model::HOOK_AFTER_INSERT => $this->invokeAfterInsert(),
            Model::HOOK_BEFORE_UPDATE => $this->invokeBeforeUpdate(),
            Model::HOOK_AFTER_UPDATE => $this->invokeAfterUpdate(),
            Model::HOOK_BEFORE_DELETE => $this->invokeBeforeDelete(),
            Model::HOOK_AFTER_DELETE => $this->invokeAfterDelete(),
            default => throw new Exception('Unknown Hook spot ' . $hookSpot)
        };
    }

    protected function invokeBeforeSave(): void
    {
        $this->onHook(
            Model::HOOK_BEFORE_SAVE,
            function (self $entity, bool $isUpdate) {
                ModelHandler::getInstance()->beforeSave($entity, $isUpdate);
            }
        );
    }

    protected function invokeAfterSave(): void
    {
        $this->onHook(
            Model::HOOK_AFTER_SAVE,
            function (self $entity, bool $isUpdate) {
                ModelHandler::getInstance()->afterSave($entity, $isUpdate);
            }
        );
    }


    protected function invokeBeforeInsert(): void
    {
        $this->onHook(
            Model::HOOK_BEFORE_INSERT,
            function (self $entity, array &$data) {
                ModelHandler::getInstance()->beforeInsert($entity, $data);
            }
        );
    }

    protected function invokeAfterInsert(): void
    {
        $this->onHook(
            Model::HOOK_AFTER_INSERT,
            function (self $entity, array &$data) {
                ModelHandler::getInstance()->afterInsert($entity, $data);
            }
        );
    }

    protected function invokeBeforeUpdate(): void
    {
        $this->onHook(
            Model::HOOK_BEFORE_UPDATE,
            function (self $entity, array &$data) {
                ModelHandler::getInstance()->beforeUpdate($entity, $data);
            }
        );
    }

    protected function invokeAfterUpdate(): void
    {
        $this->onHook(
            Model::HOOK_AFTER_UPDATE,
            function (self $entity, array &$data) {
                ModelHandler::getInstance()->afterUpdate($entity, $data);
            }
        );
    }

    protected function invokeBeforeDelete(): void
    {
        $this->onHook(
            Model::HOOK_BEFORE_DELETE,
            function (self $entity) {
                ModelHandler::getInstance()->beforeDelete($entity);
            }
        );
    }

    protected function invokeAfterDelete(): void
    {
        $this->onHook(
            Model::HOOK_AFTER_DELETE,
            function (self $entity) {
                ModelHandler::getInstance()->afterDelete($entity);
            }
        );
    }
}
