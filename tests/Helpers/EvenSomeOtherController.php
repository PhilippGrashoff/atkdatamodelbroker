<?php declare(strict_types=1);

namespace PhilippR\Atk4\ModelBroker\Tests\Helpers;

use Atk4\Data\Model;
use PhilippR\Atk4\ModelBroker\ModelBroker;

class EvenSomeOtherController
{
    public static function doSomething(Model $entity): void
    {
        if (!isset($_ENV['evenSomeOtherCounter'])) {
            $_ENV['evenSomeOtherCounter'] = 0;
        } else {
            $_ENV['evenSomeOtherCounter']++;
        }
    }

    public static function registerModelBrokerHooks(): void
    {
        ModelBroker::getInstance()->subscribe(
            Model::HOOK_AFTER_SAVE,
            function (Model $entity, bool $isUpdate) {
                self::doSomething($entity);
            }
        );
        ModelBroker::getInstance()->subscribe(
            Model::HOOK_AFTER_DELETE,
            function (Model $entity) {
                self::doSomething($entity);
            }
        );
    }
}