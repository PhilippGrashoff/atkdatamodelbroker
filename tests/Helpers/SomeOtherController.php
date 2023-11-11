<?php declare(strict_types=1);

namespace PhilippR\Atk4\ModelHandler\Tests\Helpers;


use Atk4\Data\Model;
use PhilippR\Atk4\ModelHandler\ModelHandler;

class SomeOtherController
{
    public static function doSomething(Model $entity): void
    {
        if (!isset($_ENV['someOtherCounter'])) {
            $_ENV['someOtherCounter'] = 0;
        } else {
            $_ENV['someOtherCounter']++;
        }
    }

    public static function registerModelHandlerHooks(): void
    {
        ModelHandler::getInstance()->onHook(
            Model::HOOK_BEFORE_INSERT,
            function (ModelHandler $modelHandler, Model $entity, array &$data) {
                self::doSomething($entity);
            }
        );

        ModelHandler::getInstance()->onHook(
            Model::HOOK_AFTER_INSERT,
            function (ModelHandler $modelHandler, Model $entity) {
                self::doSomething($entity);
            }
        );

        ModelHandler::getInstance()->onHook(
            Model::HOOK_BEFORE_UPDATE,
            function (ModelHandler $modelHandler, Model $entity, array &$data) {
                self::doSomething($entity);
            }
        );

        ModelHandler::getInstance()->onHook(
            Model::HOOK_AFTER_UPDATE,
            function (ModelHandler $modelHandler, Model $entity) {
                self::doSomething($entity);
            }
        );
    }
}