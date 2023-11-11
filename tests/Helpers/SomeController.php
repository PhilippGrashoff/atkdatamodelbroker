<?php declare(strict_types=1);

namespace PhilippR\Atk4\ModelHandler\Tests\Helpers;

use Atk4\Data\Model;
use PhilippR\Atk4\ModelHandler\ModelHandler;

class SomeController
{
    public static function doSomething(Model $entity): void
    {
        if (!isset($_ENV['someCounter'])) {
            $_ENV['someCounter'] = 0;
        } else {
            $_ENV['someCounter']++;
        }
    }

    public static function registerModelHandlerHooks(): void
    {
        ModelHandler::getInstance()->onHook(
            Model::HOOK_BEFORE_SAVE,
            function (ModelHandler $modelHandler, Model $entity, bool $isUpdate) {
                self::doSomething($entity);
            }
        );
        ModelHandler::getInstance()->onHook(
            Model::HOOK_AFTER_SAVE,
            function (ModelHandler $modelHandler, Model $entity, bool $isUpdate) {
                self::doSomething($entity);
            }
        );
        ModelHandler::getInstance()->onHook(
            Model::HOOK_BEFORE_DELETE,
            function (ModelHandler $modelHandler, Model $entity) {
                self::doSomething($entity);
            }
        );
        ModelHandler::getInstance()->onHook(
            Model::HOOK_AFTER_DELETE,
            function (ModelHandler $modelHandler, Model $entity) {
                self::doSomething($entity);
            }
        );
    }
}